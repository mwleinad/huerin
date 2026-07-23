<?php
/**
 * GET /api/v1/descargar.php?tipo=documento&id=19778
 *
 * Entrega el binario de una version concreta. Los ids salen del manifiesto;
 * el cliente nunca envia rutas, solo el tipo de recurso (de una lista blanca)
 * y un id numerico. La ruta en disco se arma desde la BD.
 *
 * Parametros:
 *   tipo  documento | archivo | requerimiento
 *   id    Entero, el id de esa tabla.
 *
 * Requiere: Authorization: Bearer <token>
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$auth = api_require_token();

$mapa = api_resource_map();
$tipo = isset($_GET['tipo']) ? (string)$_GET['tipo'] : '';

if (!isset($mapa[$tipo])) {
    api_fail(400, 'bad_request', 'El parametro tipo debe ser documento, archivo o requerimiento.');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    api_fail(400, 'bad_request', 'Falta el parametro id o no es un entero valido.');
}

$meta = $mapa[$tipo];
$db   = api_db();

$db->setQuery('SELECT r.contractId, r.path
                 FROM `' . $meta['tabla'] . '` r
                WHERE r.`' . $meta['pk'] . '` = ' . $id . '
                LIMIT 1');

$row = $db->GetRow();

if (!$row) {
    api_log('denied', array(
        'apiClientId'  => $auth['apiClientId'],
        'apiTokenId'   => $auth['apiTokenId'],
        'resourceType' => $tipo,
        'resourceId'   => $id,
        'detail'       => 'registro inexistente',
    ));
    api_fail(404, 'not_found', 'No existe un ' . $tipo . ' con id ' . $id . '.');
}

$contractId = (int)$row['contractId'];
$ruta       = api_resource_path($meta['carpeta'], $contractId, $row['path']);

if ($ruta === null) {
    api_log('denied', array(
        'apiClientId'  => $auth['apiClientId'],
        'apiTokenId'   => $auth['apiTokenId'],
        'contractId'   => $contractId,
        'resourceType' => $tipo,
        'resourceId'   => $id,
        'detail'       => 'archivo ausente en disco',
    ));
    api_fail(410, 'file_missing',
        'El registro existe en la base de datos pero el archivo ya no esta en disco.');
}

$bytes  = (int)filesize($ruta);
$nombre = api_safe_filename($row['path']);

api_log('download', array(
    'apiClientId'  => $auth['apiClientId'],
    'apiTokenId'   => $auth['apiTokenId'],
    'contractId'   => $contractId,
    'resourceType' => $tipo,
    'resourceId'   => $id,
    'bytes'        => $bytes,
));

// Se sirve siempre como octet-stream: aunque el archivo sea HTML o SVG, el
// navegador no lo interpretara en el dominio del sistema.
api_headers();
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $bytes);

while (ob_get_level() > 0) {
    ob_end_clean();
}

$fh = fopen($ruta, 'rb');

if ($fh === false) {
    api_fail(500, 'server_error', 'No fue posible abrir el archivo.');
}

// Lectura por bloques: un readfile() de un archivo grande se come la memoria.
while (!feof($fh)) {
    $chunk = fread($fh, 8192);

    if ($chunk === false) {
        break;
    }

    echo $chunk;
    flush();
}

fclose($fh);
exit;

// -------------------------------------------------------------------------

/**
 * Nombre seguro para Content-Disposition: sin separadores de ruta, sin
 * comillas y sin saltos de linea (inyeccion de cabeceras).
 */
function api_safe_filename($path)
{
    $name = basename(str_replace('\\', '/', (string)$path));
    $name = str_replace(array("\r", "\n", '"'), '', $name);

    return $name !== '' ? $name : 'archivo';
}
