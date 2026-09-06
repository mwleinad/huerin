<?php
/**
 * GET /api/v1/descargar.php?tipo=documento&id=19778
 * GET /api/v1/descargar.php?tipo=expediente&id=13-7
 * GET /api/v1/descargar.php?tipo=expediente&empleado=Juan%20Perez&expediente=CURP
 *
 * Entrega el binario de una version concreta. Los ids salen del manifiesto;
 * el cliente nunca envia rutas, solo el tipo de recurso (de una lista blanca)
 * y un id. La ruta en disco se arma desde la BD.
 *
 * Parametros:
 *   tipo  documento | archivo | requerimiento | expediente
 *   id    Entero para los tres primeros. Para expediente, la forma
 *         "<personalId>-<expedienteId>" que emite expedientes.php.
 *
 *   Solo para tipo=expediente, como alternativa a id (requiere Bearer, no
 *   funciona con URL firmada porque la firma cubre el id ya resuelto):
 *     personal_id / empleado       El empleado, por id o por nombre.
 *     expediente_id / expediente   El tipo de documento, por id o por nombre.
 *   Si un nombre coincide con varios registros responde 409 con los
 *   candidatos, igual que expedientes.php.
 *
 * Autenticacion, dos vias:
 *   a) URL firmada: &exp=<timestamp>&firma=<hmac>  (la que emite el
 *      manifiesto; el navegador la abre sin cabeceras). Vence pronto.
 *   b) Authorization: Bearer <token>  (para consumidores API puros).
 *
 * Si llega una firma se valida esa via; si no, se exige Bearer. La resolucion
 * por nombre solo ocurre despues de validar el token: de otro modo seria un
 * buscador de personal abierto a cualquiera.
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$mapa = api_resource_map();
$tipo = isset($_GET['tipo']) ? (string)$_GET['tipo'] : '';

if (!isset($mapa[$tipo]) && $tipo !== 'expediente') {
    api_fail(400, 'bad_request', 'El parametro tipo debe ser documento, archivo, requerimiento o expediente.');
}

$conFirma = isset($_GET['firma']) && $_GET['firma'] !== '';
$idCrudo  = isset($_GET['id']) ? (string)$_GET['id'] : '';

// -------------------------------------------------------------------------
// Autenticacion y resolucion del recurso.
//
// Con firma: el id tiene que venir ya en forma canonica, porque es lo que se
// firmo. Sin firma: primero el token, y solo entonces se aceptan nombres.
// -------------------------------------------------------------------------
$personalId   = 0;
$expedienteId = 0;

if ($conFirma) {
    $idCanonico = api_canonical_resource_id($tipo, $idCrudo);

    if ($idCanonico === null) {
        api_fail(400, 'bad_request', 'Falta el parametro id o no tiene un formato valido.');
    }

    $exp = isset($_GET['exp']) ? $_GET['exp'] : 0;

    if (!api_verify_signature($tipo, $idCanonico, $exp, $_GET['firma'])) {
        api_log('denied', array(
            'resourceType' => $tipo,
            'detail'       => 'firma invalida o vencida (' . $tipo . ' ' . $idCanonico . ')',
        ));
        api_require_https();
        api_fail(403, 'invalid_signature', 'La URL de descarga es invalida o ya vencio. Solicita un manifiesto nuevo.');
    }

    // Autenticado por firma: no hay cliente/token asociado.
    $auth = array('apiClientId' => null, 'apiTokenId' => null, 'via' => 'firma');
} else {
    $auth        = api_require_token();
    $auth['via'] = 'token';

    if ($tipo === 'expediente' && $idCrudo === '') {
        // Descarga por nombre: se resuelve empleado y tipo de expediente.
        $empleado = api_resolve_empleado(
            $auth,
            isset($_GET['personal_id']) ? $_GET['personal_id'] : null,
            isset($_GET['empleado']) ? $_GET['empleado'] : null
        );

        $tipoExpediente = api_resolve_tipo_expediente(
            $auth,
            isset($_GET['expediente_id']) ? $_GET['expediente_id'] : null,
            isset($_GET['expediente']) ? $_GET['expediente'] : null
        );

        $idCanonico = ((int)$empleado['personalId']) . '-' . ((int)$tipoExpediente['expedienteId']);
    } else {
        $idCanonico = api_canonical_resource_id($tipo, $idCrudo);

        if ($idCanonico === null) {
            api_fail(400, 'bad_request', $tipo === 'expediente'
                ? 'Falta el parametro id con la forma <personalId>-<expedienteId>, o el par empleado/expediente.'
                : 'Falta el parametro id o no es un entero valido.');
        }
    }
}

$db = api_db();

// -------------------------------------------------------------------------
// Expediente de empleado: tabla propia, llave compuesta y archivo bajo
// /expedientes/<personalId>/. No pasa por api_resource_map().
// -------------------------------------------------------------------------
if ($tipo === 'expediente') {
    $partes       = explode('-', $idCanonico);
    $personalId   = (int)$partes[0];
    $expedienteId = (int)$partes[1];

    $db->setQuery('SELECT pe.path, pe.fecha, p.name AS empleado, e.name AS tipoNombre
                     FROM personalExpedientes pe
                     INNER JOIN personal p     ON p.personalId   = pe.personalId
                     INNER JOIN expedientes e  ON e.expedienteId = pe.expedienteId
                    WHERE pe.personalId   = ' . $personalId . '
                      AND pe.expedienteId = ' . $expedienteId . '
                    LIMIT 1');

    $row = $db->GetRow();

    if (!$row) {
        api_log('denied', array(
            'apiClientId'  => $auth['apiClientId'],
            'apiTokenId'   => $auth['apiTokenId'],
            'resourceType' => 'expediente',
            'resourceId'   => $expedienteId,
            'detail'       => 'expediente no asignado al empleado ' . $personalId,
        ));
        api_fail(404, 'not_found', 'El empleado ' . $personalId
            . ' no tiene asignado el expediente ' . $expedienteId . '.');
    }

    $ruta = api_expediente_path($personalId, $row['path']);

    if ($ruta === null) {
        api_log('denied', array(
            'apiClientId'  => $auth['apiClientId'],
            'apiTokenId'   => $auth['apiTokenId'],
            'resourceType' => 'expediente',
            'resourceId'   => $expedienteId,
            'detail'       => 'sin archivo en disco (empleado ' . $personalId . ')',
        ));
        api_fail(410, 'file_missing',
            'El expediente esta asignado pero no tiene archivo cargado en el servidor.');
    }

    $bytes  = (int)filesize($ruta);
    $nombre = api_expediente_filename($row['tipoNombre'], $row['empleado'], $row['path']);

    api_log('download', array(
        'apiClientId'  => $auth['apiClientId'],
        'apiTokenId'   => $auth['apiTokenId'],
        'resourceType' => 'expediente',
        'resourceId'   => $expedienteId,
        'bytes'        => $bytes,
        'detail'       => 'empleado ' . $personalId . ' via ' . $auth['via'],
    ));

    api_send_file($ruta, $nombre, $bytes);
}

// -------------------------------------------------------------------------
// Documento / archivo / requerimiento.
// -------------------------------------------------------------------------
$meta = $mapa[$tipo];
$id   = (int)$idCanonico;

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
    'detail'       => 'via ' . $auth['via'],
));

api_send_file($ruta, $nombre, $bytes);

// -------------------------------------------------------------------------

/**
 * Vuelca el archivo al cliente y termina.
 *
 * Se sirve siempre como octet-stream: aunque el archivo sea HTML o SVG, el
 * navegador no lo interpretara en el dominio del sistema.
 */
function api_send_file($ruta, $nombre, $bytes)
{
    $nombre = api_safe_filename($nombre);

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
}

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
