<?php
/**
 * Bootstrap comun de la API v1.
 *
 * Carga minima a proposito: config.php + la clase DB. No arranca sesion,
 * no carga Smarty ni libraries.php, porque la API no necesita nada de eso
 * y cada include extra es superficie de ataque.
 *
 * Compatible con PHP 5.6+.
 */

if (!defined('API_BOOTSTRAP')) {
    define('API_BOOTSTRAP', true);

    /** Vigencia del token, en segundos. 8 horas. */
    define('API_TOKEN_TTL', 8 * 60 * 60);

    /** Intentos fallidos de autenticacion tolerados por IP dentro de la ventana. */
    define('API_MAX_AUTH_FAILS', 10);

    /** Ventana del limite de intentos, en segundos. */
    define('API_AUTH_WINDOW', 15 * 60);

    /**
     * Exigir HTTPS. Se deja en false porque config.php arma WEB_ROOT como
     * "http://..." y el sitio hoy corre sin TLS: activarlo dejaria la API
     * inservible de inmediato. Ponerlo en true en cuanto haya certificado.
     * Mientras siga en false, el token viaja en claro.
     */
    define('API_REQUIRE_HTTPS', false);

    $apiV1Dir = dirname(__FILE__);
    $appRoot  = dirname(dirname($apiV1Dir));

    require_once($appRoot . '/config.php');
    require_once($appRoot . '/classes/db.class.php');

    date_default_timezone_set('America/Mexico_City');
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
}

/**
 * Raiz en disco donde viven las carpetas /documentos, /archivos y
 * /requerimientos. Es la misma que usan classes/documento.class.php y
 * compania, para que API y UI lean exactamente los mismos ficheros.
 */
function api_files_root()
{
    return rtrim(str_replace('\\', '/', DOC_ROOT), '/');
}

/** Conexion unica a la BD durante la peticion. */
function api_db()
{
    static $db = null;

    if ($db === null) {
        // El codigo legacy de db.class.php no espera el modo de reporte por
        // excepciones que mysqli activa por defecto desde PHP 8.1; se apaga
        // para que los errores se manejen por valor de retorno, como en 7.4.
        if (function_exists('mysqli_report')) {
            mysqli_report(MYSQLI_REPORT_OFF);
        }

        $db = new DB();
        $db->DatabaseConnect();
    }

    return $db;
}

/**
 * Ejecuta una escritura (INSERT/UPDATE/DELETE) sobre la conexion cruda.
 *
 * No usa DB::InsertData()/UpdateData() a proposito: esos metodos llaman
 * CleanQuery(), que hace mysqli_free_result() sobre el booleano true que
 * devuelve un INSERT. En PHP 8.1+ eso es un TypeError fatal. Aqui se evita
 * por completo, de modo que la API corre igual en 7.4 y en 8.2+.
 *
 * Devuelve el mysqli link para poder leer insert_id / affected_rows.
 */
function api_write($sql)
{
    $conn = api_db()->getConnect();
    $ok   = mysqli_query($conn, $sql);

    if ($ok === false) {
        return false;
    }

    return $conn;
}

/** INSERT que devuelve el id generado, o 0 si fallo. */
function api_insert($sql)
{
    $conn = api_write($sql);

    return $conn ? (int)mysqli_insert_id($conn) : 0;
}

/** UPDATE/DELETE que devuelve las filas afectadas, o -1 si fallo. */
function api_affected($sql)
{
    $conn = api_write($sql);

    return $conn ? (int)mysqli_affected_rows($conn) : -1;
}

/** Escapa un valor para interpolarlo en SQL. */
function api_escape($value)
{
    return mysqli_real_escape_string(api_db()->getConnect(), (string)$value);
}

/** IP del cliente. No se confia en X-Forwarded-For: es falsificable. */
function api_client_ip()
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** Bytes aleatorios criptograficamente seguros, con respaldo para PHP 5.6. */
function api_random_hex($bytes)
{
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($bytes));
    }

    if (function_exists('openssl_random_pseudo_bytes')) {
        $strong = false;
        $raw    = openssl_random_pseudo_bytes($bytes, $strong);

        if ($strong && $raw !== false) {
            return bin2hex($raw);
        }
    }

    api_fail(500, 'server_error', 'No hay una fuente de aleatoriedad segura disponible.');
}

/**
 * Escribe una linea de auditoria. Nunca interrumpe la peticion: si el log
 * falla, la operacion sigue.
 */
function api_log($event, $fields = array())
{
    $columns = array('documento', 'archivo', 'requerimiento');
    $type    = isset($fields['resourceType']) && in_array($fields['resourceType'], $columns, true)
        ? "'" . $fields['resourceType'] . "'"
        : 'NULL';

    $num = array('apiClientId', 'apiTokenId', 'contractId', 'resourceId', 'bytes');
    $val = array();

    foreach ($num as $key) {
        $val[$key] = isset($fields[$key]) && $fields[$key] !== null
            ? (string)(int)$fields[$key]
            : 'NULL';
    }

    $detail = isset($fields['detail']) && $fields['detail'] !== null
        ? "'" . api_escape(substr($fields['detail'], 0, 255)) . "'"
        : 'NULL';

    $sql = "INSERT INTO api_log
            (event, apiClientId, apiTokenId, contractId, resourceType, resourceId, bytes, ip, detail, createdAt)
            VALUES ('" . api_escape($event) . "', "
            . $val['apiClientId'] . ", "
            . $val['apiTokenId'] . ", "
            . $val['contractId'] . ", "
            . $type . ", "
            . $val['resourceId'] . ", "
            . $val['bytes'] . ", "
            . "'" . api_escape(api_client_ip()) . "', "
            . $detail . ", NOW())";

    @api_insert($sql);
}

/** Cabeceras comunes a toda respuesta de la API. */
function api_headers()
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: no-referrer');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
}

/** Responde JSON y termina. */
function api_json($payload, $status = 200)
{
    api_headers();
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    $flags = 0;

    if (defined('JSON_UNESCAPED_UNICODE')) {
        $flags |= JSON_UNESCAPED_UNICODE;
    }
    if (defined('JSON_UNESCAPED_SLASHES')) {
        $flags |= JSON_UNESCAPED_SLASHES;
    }

    echo json_encode($payload, $flags);
    exit;
}

/** Responde un error JSON y termina. */
function api_fail($status, $code, $message)
{
    api_json(array(
        'error'   => $code,
        'message' => $message,
    ), $status);
}

/** Restringe el metodo HTTP aceptado. */
function api_require_method($method)
{
    $actual = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

    if ($actual !== strtoupper($method)) {
        header('Allow: ' . strtoupper($method));
        api_fail(405, 'method_not_allowed', 'Este endpoint solo acepta ' . strtoupper($method) . '.');
    }
}

/** Corta la peticion si se exige TLS y la conexion no lo trae. */
function api_require_https()
{
    if (!API_REQUIRE_HTTPS) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    if (!$secure) {
        api_fail(403, 'https_required', 'Esta API solo acepta conexiones HTTPS.');
    }
}

/** Extrae el token del encabezado Authorization: Bearer. */
function api_bearer_token()
{
    $header = '';

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        // Apache con mod_rewrite suele mover el encabezado aqui.
        $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();

        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                $header = $value;
                break;
            }
        }
    }

    if (!preg_match('/^\s*Bearer\s+([A-Za-z0-9]+)\s*$/i', $header, $m)) {
        return null;
    }

    return $m[1];
}

/**
 * Valida el token del encabezado y devuelve array(apiTokenId, apiClientId).
 * Termina la peticion con 401 si no es valido, esta vencido o revocado.
 */
function api_require_token()
{
    api_require_https();

    $token = api_bearer_token();

    if ($token === null || strlen($token) !== 64 || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        api_log('denied', array('detail' => 'token ausente o mal formado'));
        header('WWW-Authenticate: Bearer');
        api_fail(401, 'unauthorized', 'Falta el encabezado Authorization: Bearer <token> o el token es invalido.');
    }

    $hash = hash('sha256', $token);
    $db   = api_db();

    $db->setQuery("SELECT t.apiTokenId, t.apiClientId, t.expiresAt, t.revokedAt,
                          c.active AS clientActive, c.revokedAt AS clientRevokedAt
                     FROM api_token t
                     INNER JOIN api_client c ON c.apiClientId = t.apiClientId
                    WHERE t.tokenHash = '" . api_escape($hash) . "'
                    LIMIT 1");

    $row = $db->GetRow();

    if (!$row) {
        api_log('denied', array('detail' => 'token desconocido'));
        header('WWW-Authenticate: Bearer');
        api_fail(401, 'unauthorized', 'Token invalido.');
    }

    if ($row['revokedAt'] !== null) {
        api_log('denied', array(
            'apiTokenId'  => $row['apiTokenId'],
            'apiClientId' => $row['apiClientId'],
            'detail'      => 'token revocado',
        ));
        api_fail(401, 'token_revoked', 'El token fue revocado. Solicita uno nuevo.');
    }

    if (strtotime($row['expiresAt']) <= time()) {
        api_log('denied', array(
            'apiTokenId'  => $row['apiTokenId'],
            'apiClientId' => $row['apiClientId'],
            'detail'      => 'token vencido',
        ));
        api_fail(401, 'token_expired', 'El token vencio. Solicita uno nuevo en /api/v1/token.php.');
    }

    if ($row['clientActive'] !== '1' || $row['clientRevokedAt'] !== null) {
        api_log('denied', array(
            'apiTokenId'  => $row['apiTokenId'],
            'apiClientId' => $row['apiClientId'],
            'detail'      => 'cliente inactivo',
        ));
        api_fail(403, 'client_inactive', 'La credencial de integracion esta desactivada.');
    }

    return array(
        'apiTokenId'  => (int)$row['apiTokenId'],
        'apiClientId' => (int)$row['apiClientId'],
        'expiresAt'   => $row['expiresAt'],
    );
}

/**
 * Carga una empresa (contract) por id y verifica que exista.
 * Termina con 404 si no existe.
 */
function api_require_contract($contractId, $auth)
{
    $contractId = (int)$contractId;

    if ($contractId <= 0) {
        api_fail(400, 'bad_request', 'Falta el parametro contract_id o no es un entero valido.');
    }

    $db = api_db();
    $db->setQuery("SELECT contractId, rfc, name, nombreComercial, activo
                     FROM contract
                    WHERE contractId = " . $contractId . "
                    LIMIT 1");

    $row = $db->GetRow();

    if (!$row) {
        api_log('denied', array(
            'apiClientId' => $auth['apiClientId'],
            'apiTokenId'  => $auth['apiTokenId'],
            'contractId'  => $contractId,
            'detail'      => 'empresa inexistente',
        ));
        api_fail(404, 'not_found', 'No existe una empresa con contract_id ' . $contractId . '.');
    }

    return $row;
}

/**
 * Resuelve la empresa a partir de $_GET, aceptando contract_id o rfc.
 *
 * Con contract_id delega en api_require_contract. Con rfc busca en contract;
 * si un mismo RFC corresponde a varias empresas (p.ej. una alterna para
 * facturacion), NO adivina: responde 409 con la lista de candidatas para que
 * el cliente reintente con el contract_id correcto.
 *
 * Termina la peticion con 400/404/409 si no puede resolver una unica empresa.
 */
function api_resolve_contract($auth)
{
    if (isset($_GET['contract_id']) && $_GET['contract_id'] !== '') {
        return api_require_contract($_GET['contract_id'], $auth);
    }

    if (!isset($_GET['rfc']) || trim($_GET['rfc']) === '') {
        api_fail(400, 'bad_request', 'Se requiere contract_id o rfc.');
    }

    $rfc = trim($_GET['rfc']);
    $db  = api_db();

    $db->setQuery("SELECT contractId, rfc, name, nombreComercial, activo
                     FROM contract
                    WHERE rfc = '" . api_escape($rfc) . "'
                    ORDER BY activo DESC, contractId ASC");

    $rows = $db->GetResult();

    if (!$rows) {
        api_log('denied', array(
            'apiClientId' => $auth['apiClientId'],
            'apiTokenId'  => $auth['apiTokenId'],
            'detail'      => 'rfc inexistente',
        ));
        api_fail(404, 'not_found', 'No existe una empresa con el RFC ' . $rfc . '.');
    }

    if (count($rows) > 1) {
        $candidatas = array();

        foreach ($rows as $r) {
            $candidatas[] = array(
                'contractId'      => (int)$r['contractId'],
                'rfc'             => $r['rfc'],
                'razonSocial'     => $r['name'],
                'nombreComercial' => $r['nombreComercial'],
                'activo'          => $r['activo'],
            );
        }

        api_log('denied', array(
            'apiClientId' => $auth['apiClientId'],
            'apiTokenId'  => $auth['apiTokenId'],
            'detail'      => 'rfc ambiguo (' . count($rows) . ' empresas)',
        ));

        api_json(array(
            'error'      => 'rfc_ambiguo',
            'message'    => 'El RFC ' . $rfc . ' corresponde a ' . count($rows)
                          . ' empresas. Reintenta con contract_id.',
            'candidatas' => $candidatas,
        ), 409);
    }

    return $rows[0];
}

/**
 * Metadatos de los tres tipos de recurso. Centralizado para que los
 * endpoints nunca reciban nombres de tabla o carpeta desde el cliente.
 *
 * OJO con las mayusculas de 'tablaTipo': en Linux los nombres de tabla
 * distinguen mayusculas (lower_case_table_names=0), asi que deben escribirse
 * igual que en produccion: tipoDocumento, tipoArchivo, tipoRequerimiento.
 * En Windows daria igual, pero ahi es donde se cuela el error.
 */
function api_resource_map()
{
    return array(
        'documento' => array(
            'tabla'       => 'documento',
            'pk'          => 'documentoId',
            'fkTipo'      => 'tipoDocumentoId',
            'tablaTipo'   => 'tipoDocumento',
            'campoTipo'   => 'nombre',
            'carpeta'     => 'documentos',
            'campoFecha'  => 'dateExpiration',
            'aliasFecha'  => 'fechaVencimiento',
        ),
        'archivo' => array(
            'tabla'       => 'archivo',
            'pk'          => 'archivoId',
            'fkTipo'      => 'tipoArchivoId',
            'tablaTipo'   => 'tipoArchivo',
            'campoTipo'   => 'descripcion',
            'carpeta'     => 'archivos',
            'campoFecha'  => 'date',
            'aliasFecha'  => 'fecha',
        ),
        'requerimiento' => array(
            'tabla'       => 'requerimiento',
            'pk'          => 'requerimientoId',
            'fkTipo'      => 'tipoRequerimientoId',
            'tablaTipo'   => 'tipoRequerimiento',
            'campoTipo'   => 'nombre',
            'carpeta'     => 'requerimientos',
            'campoFecha'  => null,
            'aliasFecha'  => null,
        ),
    );
}

/**
 * Construye la ruta absoluta en disco de un recurso y verifica que quede
 * dentro de su carpeta. El nombre viene de la BD, nunca del cliente, y aun
 * asi se pasa por basename() y se compara el realpath: es la defensa contra
 * el path traversal que hoy tiene download.php.
 *
 * Devuelve null si el archivo no existe o queda fuera de la carpeta.
 */
function api_resource_path($carpeta, $contractId, $path)
{
    $base = api_files_root() . '/' . $carpeta;
    $name = basename(str_replace('\\', '/', (string)$path));

    if ($name === '' || $name === '.' || $name === '..') {
        return null;
    }

    $full = $base . '/' . ((int)$contractId) . '_' . $name;

    if (!is_file($full)) {
        return null;
    }

    $realFull = realpath($full);
    $realBase = realpath($base);

    if ($realFull === false || $realBase === false) {
        return null;
    }

    $realFull = str_replace('\\', '/', $realFull);
    $realBase = rtrim(str_replace('\\', '/', $realBase), '/');

    if (strpos($realFull, $realBase . '/') !== 0) {
        return null;
    }

    return $realFull;
}

/** Tipo MIME informativo, derivado de la extension. */
function api_mime_from_name($name)
{
    $map = array(
        'pdf'  => 'application/pdf',
        'zip'  => 'application/zip',
        'xml'  => 'application/xml',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'txt'  => 'text/plain',
        'csv'  => 'text/csv',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    );

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    return isset($map[$ext]) ? $map[$ext] : 'application/octet-stream';
}
