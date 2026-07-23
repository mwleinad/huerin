<?php
/**
 * POST   /api/v1/token.php    Emite un token con vigencia de 8 horas.
 * DELETE /api/v1/token.php    Revoca el token del encabezado Authorization.
 *
 * Cuerpo del POST (JSON o application/x-www-form-urlencoded):
 *   { "client_id": "...", "client_secret": "..." }
 *
 * La credencial es de integracion (tabla api_client), no una cuenta de
 * usuario del panel: si se filtra, no da acceso al sistema y se revoca sin
 * tocar el login de nadie.
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

if ($method === 'DELETE') {
    api_revoke_current_token();
}

api_require_method('POST');
api_require_https();

$input = api_read_credentials();
$ip    = api_client_ip();
$db    = api_db();

// --- Limite de intentos por IP -------------------------------------------
$db->setQuery("SELECT COUNT(*)
                 FROM api_log
                WHERE event = 'auth_fail'
                  AND ip = '" . api_escape($ip) . "'
                  AND createdAt > DATE_SUB(NOW(), INTERVAL " . (int)API_AUTH_WINDOW . " SECOND)");

$fails = (int)$db->GetSingle();

if ($fails >= API_MAX_AUTH_FAILS) {
    api_log('auth_locked', array('detail' => 'limite de intentos alcanzado'));
    header('Retry-After: ' . (int)API_AUTH_WINDOW);
    api_fail(429, 'too_many_attempts',
        'Demasiados intentos fallidos. Espera ' . (int)(API_AUTH_WINDOW / 60) . ' minutos.');
}

// --- Validacion de la credencial -----------------------------------------
// El clientId se genera en hexadecimal, asi que un formato distinto es
// basura y se rechaza antes de tocar la BD.
if (!preg_match('/^[a-f0-9]{32}$/', $input['client_id'])) {
    api_log('auth_fail', array('detail' => 'client_id mal formado'));
    api_fail(401, 'invalid_client', 'Credenciales invalidas.');
}

$db->setQuery("SELECT apiClientId, secretHash, active, revokedAt
                 FROM api_client
                WHERE clientId = '" . api_escape($input['client_id']) . "'
                LIMIT 1");

$client = $db->GetRow();

$ok = $client
    && $client['active'] === '1'
    && $client['revokedAt'] === null
    && password_verify($input['client_secret'], $client['secretHash']);

if (!$ok) {
    api_log('auth_fail', array(
        'apiClientId' => $client ? $client['apiClientId'] : null,
        'detail'      => $client ? 'secret incorrecto o cliente inactivo' : 'client_id inexistente',
    ));
    // Mismo mensaje en todos los casos: no se le dice al atacante cual de
    // los dos datos fallo.
    api_fail(401, 'invalid_client', 'Credenciales invalidas.');
}

$apiClientId = (int)$client['apiClientId'];

// --- Emision del token ----------------------------------------------------
$token = api_random_hex(32);
$hash  = hash('sha256', $token);

$apiTokenId = api_insert(
    "INSERT INTO api_token (apiClientId, tokenHash, issuedAt, expiresAt, ip)
     VALUES (" . $apiClientId . ",
             '" . api_escape($hash) . "',
             NOW(),
             DATE_ADD(NOW(), INTERVAL " . (int)API_TOKEN_TTL . " SECOND),
             '" . api_escape($ip) . "')");

if ($apiTokenId <= 0) {
    api_fail(500, 'server_error', 'No fue posible emitir el token.');
}

api_log('auth_ok', array(
    'apiClientId' => $apiClientId,
    'apiTokenId'  => $apiTokenId,
));

api_json(array(
    'access_token' => $token,
    'token_type'   => 'Bearer',
    'expires_in'   => (int)API_TOKEN_TTL,
    'expires_at'   => date('c', time() + API_TOKEN_TTL),
));

// -------------------------------------------------------------------------

/**
 * Lee client_id y client_secret del cuerpo, aceptando JSON o formulario.
 */
function api_read_credentials()
{
    $clientId = isset($_POST['client_id']) ? $_POST['client_id'] : null;
    $secret   = isset($_POST['client_secret']) ? $_POST['client_secret'] : null;

    if ($clientId === null || $secret === null) {
        $raw = file_get_contents('php://input');

        if ($raw !== false && $raw !== '') {
            $body = json_decode($raw, true);

            if (is_array($body)) {
                $clientId = isset($body['client_id']) ? $body['client_id'] : $clientId;
                $secret   = isset($body['client_secret']) ? $body['client_secret'] : $secret;
            }
        }
    }

    if (!is_string($clientId) || !is_string($secret) || $clientId === '' || $secret === '') {
        api_fail(400, 'bad_request', 'Se requieren client_id y client_secret.');
    }

    return array('client_id' => $clientId, 'client_secret' => $secret);
}

/**
 * Revoca el token presentado. Util para cerrar una integracion sin esperar
 * a que se cumplan las 8 horas.
 */
function api_revoke_current_token()
{
    $auth = api_require_token();

    api_affected("UPDATE api_token
                     SET revokedAt = NOW()
                   WHERE apiTokenId = " . (int)$auth['apiTokenId'] . "
                     AND revokedAt IS NULL");

    api_log('denied', array(
        'apiClientId' => $auth['apiClientId'],
        'apiTokenId'  => $auth['apiTokenId'],
        'detail'      => 'token revocado por el cliente',
    ));

    api_json(array('revoked' => true));
}
