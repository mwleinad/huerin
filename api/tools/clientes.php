<?php
/**
 * Administracion de credenciales de la API. Solo por linea de comandos.
 *
 *   php api/tools/clientes.php listar
 *   php api/tools/clientes.php crear "Portal Contable"
 *   php api/tools/clientes.php revocar <clientId>
 *   php api/tools/clientes.php tokens [clientId]
 *
 * El secret se muestra UNA sola vez al crearlo: en la BD solo queda su hash.
 */

if (php_sapi_name() !== 'cli') {
    header('HTTP/1.1 403 Forbidden');
    exit("Esta herramienta solo corre por linea de comandos.\n");
}

// config.php arma DOC_ROOT y WEB_ROOT desde $_SERVER, que en CLI viene vacio.
// Misma convencion que los scripts de cron-nuevo/.
if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(dirname(dirname(__FILE__))));
}
if (empty($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

require_once(dirname(dirname(__FILE__)) . '/v1/bootstrap.php');

$accion = isset($argv[1]) ? $argv[1] : '';
$db     = api_db();

switch ($accion) {

    case 'crear':
        $nombre = isset($argv[2]) ? trim($argv[2]) : '';

        if ($nombre === '') {
            exit("Uso: php api/tools/clientes.php crear \"Nombre del consumidor\"\n");
        }

        $clientId = api_random_hex(16);   // 32 caracteres hex
        $secret   = api_random_hex(32);   // 64 caracteres hex
        $hash     = password_hash($secret, PASSWORD_DEFAULT);

        $nuevo = api_insert(
            "INSERT INTO api_client (name, clientId, secretHash, active, createdAt)
             VALUES ('" . api_escape($nombre) . "',
                     '" . api_escape($clientId) . "',
                     '" . api_escape($hash) . "',
                     '1', NOW())");

        if ($nuevo <= 0) {
            exit("No se pudo crear la credencial.\n");
        }

        echo "\nCredencial creada (apiClientId {$nuevo}) para: {$nombre}\n\n";
        echo "  client_id     = {$clientId}\n";
        echo "  client_secret = {$secret}\n\n";
        echo "Guarda el secret ahora. No se vuelve a mostrar: en la BD solo queda su hash.\n\n";
        break;

    case 'listar':
        $db->setQuery("SELECT apiClientId, name, clientId, active, createdAt, revokedAt
                         FROM api_client
                        ORDER BY apiClientId ASC");

        $filas = $db->GetResult();

        if (!$filas) {
            echo "No hay credenciales registradas.\n";
            break;
        }

        printf("%-4s  %-30s  %-32s  %-8s  %s\n", 'id', 'nombre', 'client_id', 'estado', 'alta');

        foreach ($filas as $f) {
            $estado = ($f['active'] === '1' && $f['revokedAt'] === null) ? 'activa' : 'revocada';
            printf("%-4s  %-30s  %-32s  %-8s  %s\n",
                $f['apiClientId'], substr($f['name'], 0, 30), $f['clientId'], $estado, $f['createdAt']);
        }
        break;

    case 'revocar':
        $clientId = isset($argv[2]) ? trim($argv[2]) : '';

        if (!preg_match('/^[a-f0-9]{32}$/', $clientId)) {
            exit("Uso: php api/tools/clientes.php revocar <clientId de 32 hex>\n");
        }

        $afectados = api_affected("UPDATE api_client
                          SET active = '0', revokedAt = NOW()
                        WHERE clientId = '" . api_escape($clientId) . "'
                          AND revokedAt IS NULL");

        if ($afectados <= 0) {
            exit("No se encontro esa credencial activa.\n");
        }

        // Se tumban tambien los tokens vivos que haya emitido.
        $tokens = api_affected("UPDATE api_token t
                       INNER JOIN api_client c ON c.apiClientId = t.apiClientId
                          SET t.revokedAt = NOW()
                        WHERE c.clientId = '" . api_escape($clientId) . "'
                          AND t.revokedAt IS NULL
                          AND t.expiresAt > NOW()");

        echo "Credencial revocada. Tokens vigentes invalidados: {$tokens}\n";
        break;

    case 'tokens':
        $filtro = isset($argv[2]) && preg_match('/^[a-f0-9]{32}$/', $argv[2])
            ? " WHERE c.clientId = '" . api_escape($argv[2]) . "'"
            : '';

        $db->setQuery("SELECT t.apiTokenId, c.name, t.issuedAt, t.expiresAt, t.revokedAt, t.ip,
                              CASE WHEN t.revokedAt IS NOT NULL THEN 'revocado'
                                   WHEN t.expiresAt <= NOW()    THEN 'vencido'
                                   ELSE 'vigente' END AS estado
                         FROM api_token t
                         INNER JOIN api_client c ON c.apiClientId = t.apiClientId"
                     . $filtro . "
                        ORDER BY t.apiTokenId DESC
                        LIMIT 50");

        $filas = $db->GetResult();

        if (!$filas) {
            echo "Sin tokens emitidos.\n";
            break;
        }

        printf("%-6s  %-25s  %-19s  %-19s  %-9s  %s\n",
            'id', 'cliente', 'emitido', 'expira', 'estado', 'ip');

        foreach ($filas as $f) {
            printf("%-6s  %-25s  %-19s  %-19s  %-9s  %s\n",
                $f['apiTokenId'], substr($f['name'], 0, 25),
                $f['issuedAt'], $f['expiresAt'], $f['estado'], $f['ip']);
        }
        break;

    default:
        echo "Uso:\n";
        echo "  php api/tools/clientes.php listar\n";
        echo "  php api/tools/clientes.php crear \"Nombre del consumidor\"\n";
        echo "  php api/tools/clientes.php revocar <clientId>\n";
        echo "  php api/tools/clientes.php tokens [clientId]\n";
        break;
}
