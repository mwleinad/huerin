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
     * Vigencia de las URLs de descarga firmadas, en segundos. 15 minutos.
     * Es corta a proposito: la firma viaja en la URL (queda en historial,
     * logs de proxy, Referer), asi que debe caducar pronto.
     */
    define('API_SIGN_TTL', 30 * 60);

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
    $columns = array('documento', 'archivo', 'requerimiento', 'expediente');
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

/**
 * Secreto con el que se firman las URLs de descarga.
 *
 * Vive solo en el servidor (tabla api_setting) y se genera la primera vez.
 * No se pone en config.php para no tener que editar cada entorno ni arriesgar
 * que se copie a un repo. Quien lo tenga puede firmar cualquier descarga.
 */
function api_url_secret()
{
    static $secret = null;

    if ($secret !== null) {
        return $secret;
    }

    $db = api_db();
    $db->setQuery("SELECT value FROM api_setting WHERE name = 'url_secret' LIMIT 1");
    $row = $db->GetRow();

    if ($row && !empty($row['value'])) {
        $secret = $row['value'];
        return $secret;
    }

    // Primera vez: se genera y se guarda. INSERT IGNORE evita una carrera si
    // dos peticiones lo crean a la vez; tras el intento se relee el valor que
    // haya quedado, sea el nuestro o el del otro proceso.
    $nuevo = api_random_hex(32);
    api_write("INSERT IGNORE INTO api_setting (name, value, createdAt)
               VALUES ('url_secret', '" . api_escape($nuevo) . "', NOW())");

    $db->setQuery("SELECT value FROM api_setting WHERE name = 'url_secret' LIMIT 1");
    $row = $db->GetRow();

    $secret = ($row && !empty($row['value'])) ? $row['value'] : $nuevo;

    return $secret;
}

/**
 * Firma HMAC-SHA256 sobre "tipo|id|exp". Cualquier cambio en uno de los tres
 * campos invalida la firma.
 */
function api_sign($tipo, $id, $exp)
{
    $mensaje = $tipo . '|' . api_canonical_resource_id($tipo, $id) . '|' . (int)$exp;

    return hash_hmac('sha256', $mensaje, api_url_secret());
}

/**
 * Forma canonica del id de un recurso dentro de la URL y de la firma.
 *
 * Documento, archivo y requerimiento tienen llave primaria de una columna, asi
 * que su id es el entero tal cual. El expediente de un empleado vive en
 * personalExpedientes, cuya llave es compuesta (personalId, expedienteId): se
 * representa como "<personalId>-<expedienteId>" para que quepa en el mismo
 * parametro id y quede cubierto por la firma completo, sin que se pueda
 * cambiar el expediente sin invalidarla.
 *
 * Devuelve null si el id no tiene la forma esperada.
 */
function api_canonical_resource_id($tipo, $id)
{
    $id = (string)$id;

    if ($tipo === 'expediente') {
        if (!preg_match('/^([0-9]{1,10})-([0-9]{1,10})$/', $id, $m)) {
            return null;
        }

        if ((int)$m[1] <= 0 || (int)$m[2] <= 0) {
            return null;
        }

        return ((int)$m[1]) . '-' . ((int)$m[2]);
    }

    if (!preg_match('/^[0-9]{1,10}$/', $id) || (int)$id <= 0) {
        return null;
    }

    return (string)(int)$id;
}

/**
 * URL absoluta de descarga ya firmada, valida durante API_SIGN_TTL segundos.
 * Es la que consume el navegador directamente, sin cabecera Authorization.
 */
function api_signed_download_url($tipo, $id)
{
    $exp   = time() + (int)API_SIGN_TTL;
    $firma = api_sign($tipo, $id, $exp);

    return api_download_base() . '/descargar.php'
         . '?tipo=' . rawurlencode($tipo)
         . '&id='   . rawurlencode(api_canonical_resource_id($tipo, $id))
         . '&exp='  . $exp
         . '&firma=' . $firma;
}

/**
 * Base publica sobre la que se arman las URLs de descarga.
 *
 * Por omision es este mismo servidor (WEB_ROOT). Pero cuando el legacy se
 * sirve por HTTP y quien consume la API corre sobre HTTPS, el navegador
 * bloquea la descarga por "Mixed Content". Para ese caso se define en
 * config.php una base publica que apunte a un proxy HTTPS:
 *
 *   define('API_PUBLIC_DOWNLOAD_BASE', 'https://api.braunhuerin.com/legacy');
 *
 * La firma no incluye el host -solo tipo|id|exp-, asi que la URL puede
 * apuntar al proxy y seguir validando al llegar aqui.
 */
function api_download_base()
{
    if (defined('API_PUBLIC_DOWNLOAD_BASE') && API_PUBLIC_DOWNLOAD_BASE !== '') {
        return rtrim(API_PUBLIC_DOWNLOAD_BASE, '/');
    }

    return WEB_ROOT . '/api/v1';
}

/**
 * Valida una firma de descarga. Devuelve true solo si la firma corresponde
 * exactamente a tipo|id|exp y aun no vence.
 *
 * Se usa hash_equals para comparar en tiempo constante y no filtrar, por el
 * tiempo de respuesta, que tan "cerca" estuvo una firma incorrecta.
 */
function api_verify_signature($tipo, $id, $exp, $firma)
{
    $exp = (int)$exp;

    if ($exp <= 0 || !is_string($firma) || !preg_match('/^[a-f0-9]{64}$/', $firma)) {
        return false;
    }

    if ($exp < time()) {
        return false;   // vencida
    }

    $esperada = api_sign($tipo, $id, $exp);

    return hash_equals($esperada, $firma);
}

// -------------------------------------------------------------------------
// Expedientes del personal
//
// A diferencia de documento/archivo/requerimiento -que cuelgan de una empresa
// y viven en DOC_ROOT/<carpeta>/<contractId>_<archivo>-, el expediente de un
// empleado vive en DOC_ROOT/expedientes/<personalId>/<archivo> y su registro
// esta en personalExpedientes, con llave compuesta (personalId, expedienteId).
// Por eso no entra en api_resource_map() y tiene sus propias funciones.
// -------------------------------------------------------------------------

/**
 * Ruta absoluta del archivo de un expediente, o null si no esta en disco.
 *
 * Misma defensa que api_resource_path(): el nombre sale de la BD, aun asi se
 * pasa por basename() y se verifica con realpath() que el resultado quede
 * dentro de la carpeta del empleado.
 */
function api_expediente_path($personalId, $path)
{
    $personalId = (int)$personalId;

    if ($personalId <= 0 || $path === null || $path === '') {
        return null;
    }

    $base = api_files_root() . '/expedientes/' . $personalId;
    $name = basename(str_replace('\\', '/', (string)$path));

    if ($name === '' || $name === '.' || $name === '..') {
        return null;
    }

    $full = $base . '/' . $name;

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

/** URL de descarga firmada de un expediente concreto de un empleado. */
function api_expediente_signed_url($personalId, $expedienteId)
{
    return api_signed_download_url('expediente', ((int)$personalId) . '-' . ((int)$expedienteId));
}

/**
 * Localiza a un empleado por id o por nombre.
 *
 * El nombre se busca por coincidencia parcial (LIKE) porque el sistema guarda
 * el nombre completo con prefijos de clave interna ("CIGER3 Miguel Angel..."),
 * asi que exigir el nombre exacto lo volveria inservible. Si la busqueda
 * devuelve mas de un empleado NO se adivina: responde 409 con los candidatos
 * para que el cliente reintente con personal_id.
 *
 * $nombre puede ser null cuando se envio personal_id.
 */
function api_resolve_empleado($auth, $personalId = null, $nombre = null)
{
    $db     = api_db();
    $campos = 'p.personalId, p.name, p.puesto, p.email, p.active, p.tipoPersonal,
               p.fechaIngreso, p.departamentoId, d.departamento';
    $join   = ' FROM personal p
                LEFT JOIN departamentos d ON d.departamentoId = p.departamentoId';

    if ($personalId !== null && $personalId !== '') {
        $personalId = (int)$personalId;

        if ($personalId <= 0) {
            api_fail(400, 'bad_request', 'El parametro personal_id debe ser un entero valido.');
        }

        $db->setQuery('SELECT ' . $campos . $join . ' WHERE p.personalId = ' . $personalId . ' LIMIT 1');
        $row = $db->GetRow();

        if (!$row) {
            api_log('denied', array(
                'apiClientId' => $auth['apiClientId'],
                'apiTokenId'  => $auth['apiTokenId'],
                'detail'      => 'empleado inexistente (' . $personalId . ')',
            ));
            api_fail(404, 'not_found', 'No existe un empleado con personal_id ' . $personalId . '.');
        }

        return $row;
    }

    if ($nombre === null || trim($nombre) === '') {
        api_fail(400, 'bad_request', 'Se requiere personal_id o empleado (nombre).');
    }

    $nombre = trim($nombre);

    if (strlen($nombre) < 3) {
        api_fail(400, 'bad_request', 'El nombre del empleado debe tener al menos 3 caracteres.');
    }

    $like = api_escape($nombre);

    // Se privilegia la coincidencia exacta: si existe, ya no hay ambiguedad.
    $db->setQuery('SELECT ' . $campos . $join . " WHERE p.name = '" . $like . "'
                    ORDER BY p.active DESC, p.personalId ASC");
    $rows = $db->GetResult();

    if (!$rows) {
        $db->setQuery('SELECT ' . $campos . $join . " WHERE p.name LIKE '%" . $like . "%'
                        ORDER BY p.active DESC, p.name ASC");
        $rows = $db->GetResult();
    }

    if (!$rows) {
        api_log('denied', array(
            'apiClientId' => $auth['apiClientId'],
            'apiTokenId'  => $auth['apiTokenId'],
            'detail'      => 'empleado inexistente por nombre',
        ));
        api_fail(404, 'not_found', 'Ningun empleado coincide con "' . $nombre . '".');
    }

    if (count($rows) > 1) {
        $candidatos = array();

        foreach ($rows as $r) {
            $candidatos[] = api_empleado_publico($r);
        }

        api_log('denied', array(
            'apiClientId' => $auth['apiClientId'],
            'apiTokenId'  => $auth['apiTokenId'],
            'detail'      => 'empleado ambiguo (' . count($rows) . ' coincidencias)',
        ));

        api_json(array(
            'error'      => 'empleado_ambiguo',
            'message'    => '"' . $nombre . '" coincide con ' . count($rows)
                          . ' empleados. Reintenta con personal_id.',
            'candidatos' => $candidatos,
        ), 409);
    }

    return $rows[0];
}

/**
 * Localiza un tipo de expediente del catalogo (tabla expedientes) por id o por
 * nombre. Igual que con el empleado, si el nombre coincide con varios responde
 * 409 con los candidatos en lugar de elegir uno.
 */
function api_resolve_tipo_expediente($auth, $expedienteId = null, $nombre = null)
{
    $db = api_db();

    if ($expedienteId !== null && $expedienteId !== '') {
        $expedienteId = (int)$expedienteId;

        if ($expedienteId <= 0) {
            api_fail(400, 'bad_request', 'El parametro expediente_id debe ser un entero valido.');
        }

        $db->setQuery('SELECT expedienteId, name, status, extension
                         FROM expedientes
                        WHERE expedienteId = ' . $expedienteId . ' LIMIT 1');
        $row = $db->GetRow();

        if (!$row) {
            api_fail(404, 'not_found', 'No existe un tipo de expediente con id ' . $expedienteId . '.');
        }

        return $row;
    }

    if ($nombre === null || trim($nombre) === '') {
        api_fail(400, 'bad_request', 'Se requiere expediente_id o expediente (nombre).');
    }

    $nombre = trim($nombre);
    $like   = api_escape($nombre);

    $db->setQuery("SELECT expedienteId, name, status, extension
                     FROM expedientes
                    WHERE name = '" . $like . "'
                    ORDER BY status ASC, expedienteId ASC");
    $rows = $db->GetResult();

    if (!$rows) {
        $db->setQuery("SELECT expedienteId, name, status, extension
                         FROM expedientes
                        WHERE name LIKE '%" . $like . "%'
                        ORDER BY status ASC, name ASC");
        $rows = $db->GetResult();
    }

    if (!$rows) {
        api_fail(404, 'not_found', 'Ningun tipo de expediente coincide con "' . $nombre . '".');
    }

    if (count($rows) > 1) {
        $candidatos = array();

        foreach ($rows as $r) {
            $candidatos[] = array(
                'expedienteId' => (int)$r['expedienteId'],
                'nombre'       => $r['name'],
                'status'       => $r['status'],
            );
        }

        api_json(array(
            'error'      => 'expediente_ambiguo',
            'message'    => '"' . $nombre . '" coincide con ' . count($rows)
                          . ' tipos de expediente. Reintenta con expediente_id.',
            'candidatos' => $candidatos,
        ), 409);
    }

    return $rows[0];
}

/** Representacion publica de un empleado. Solo datos de identificacion. */
function api_empleado_publico($row)
{
    return array(
        'personalId'   => (int)$row['personalId'],
        'nombre'       => $row['name'],
        'puesto'       => isset($row['puesto']) ? $row['puesto'] : null,
        'email'        => isset($row['email']) ? $row['email'] : null,
        'departamento' => isset($row['departamento']) ? $row['departamento'] : null,
        'tipoPersonal' => isset($row['tipoPersonal']) ? $row['tipoPersonal'] : null,
        'fechaIngreso' => isset($row['fechaIngreso']) && $row['fechaIngreso'] !== '' ? $row['fechaIngreso'] : null,
        'activo'       => isset($row['active']) ? ((string)$row['active'] === '1') : null,
    );
}

/**
 * Nombre con el que se entrega el archivo de un expediente.
 *
 * En disco se llama "employe_file<idp><ide>.pdf", que no le dice nada a quien
 * lo descarga. Se arma uno legible -"ACTA DE NACIMIENTO - Juan Perez.pdf"-
 * limitado a caracteres inocuos para la cabecera Content-Disposition.
 */
function api_expediente_filename($tipoNombre, $empleadoNombre, $path)
{
    $ext = strtolower(pathinfo((string)$path, PATHINFO_EXTENSION));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext);

    $base = trim($tipoNombre) . ' - ' . trim($empleadoNombre);
    $base = preg_replace('/[^A-Za-z0-9 ._-]/', '_', $base);
    $base = trim(preg_replace('/\s+/', ' ', $base));
    $base = substr($base, 0, 120);

    if ($base === '') {
        $base = 'expediente';
    }

    return $ext !== '' ? $base . '.' . $ext : $base;
}
