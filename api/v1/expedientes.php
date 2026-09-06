<?php
/**
 * GET /api/v1/expedientes.php?personal_id=13
 * GET /api/v1/expedientes.php?empleado=Jacobo%20Huerin      (atajo por nombre)
 * GET /api/v1/expedientes.php?catalogo=1                    (tipos de expediente)
 *
 * Devuelve el expediente de un empleado: los tipos de documento que le fueron
 * asignados (ACTA DE NACIMIENTO, CURP, CONTRATO, ...), cuales ya tienen
 * archivo cargado y la URL firmada para descargar cada uno.
 *
 * Parametros:
 *   personal_id      Entero. Obligatorio salvo que se envie empleado.
 *   empleado         Nombre o parte del nombre. Alternativa a personal_id. Si
 *                    coincide con mas de un empleado responde 409 con la lista
 *                    de candidatos para reintentar con personal_id.
 *   expediente       Opcional. Nombre (o parte) de un tipo de expediente para
 *                    limitar la respuesta a ese solo documento.
 *   expediente_id    Opcional. Lo mismo, por id del catalogo.
 *   solo_existentes  Opcional, "1" para omitir los que no tienen archivo.
 *   incluir_baja     Opcional, "1" para incluir tipos dados de baja del
 *                    catalogo que aun conservan un archivo cargado.
 *   catalogo         "1" para devolver unicamente el catalogo de tipos de
 *                    expediente (sin empleado). Ignora el resto de filtros.
 *
 * Requiere: Authorization: Bearer <token>
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$auth = api_require_token();
$db   = api_db();

// --- Catalogo de tipos de expediente -------------------------------------
if (isset($_GET['catalogo']) && $_GET['catalogo'] === '1') {
    $incluirBaja = isset($_GET['incluir_baja']) && $_GET['incluir_baja'] === '1';

    $db->setQuery('SELECT expedienteId, name, status, extension
                     FROM expedientes'
                 . ($incluirBaja ? '' : " WHERE status = 'activo'")
                 . ' ORDER BY name ASC');

    $tipos = array();

    foreach ($db->GetResult() as $row) {
        $tipos[] = array(
            'expedienteId'      => (int)$row['expedienteId'],
            'nombre'            => $row['name'],
            'status'            => $row['status'],
            'extensionEsperada' => $row['extension'],
        );
    }

    api_json(array('catalogo' => $tipos));
}

// --- Expediente de un empleado -------------------------------------------
$personalId = isset($_GET['personal_id']) ? $_GET['personal_id'] : null;
$nombre     = isset($_GET['empleado']) ? $_GET['empleado'] : null;

if (($personalId === null || $personalId === '') && ($nombre === null || trim($nombre) === '')) {
    api_fail(400, 'bad_request', 'Se requiere personal_id o empleado. Usa catalogo=1 para listar los tipos de expediente.');
}

$empleado   = api_resolve_empleado($auth, $personalId, $nombre);
$personalId = (int)$empleado['personalId'];

$where = array('pe.personalId = ' . $personalId);

if (!(isset($_GET['incluir_baja']) && $_GET['incluir_baja'] === '1')) {
    $where[] = "e.status = 'activo'";
}

// Filtro por un tipo concreto: se resuelve primero a un id del catalogo para
// no meter texto del cliente dentro de la consulta del expediente.
if ((isset($_GET['expediente']) && trim($_GET['expediente']) !== '')
    || (isset($_GET['expediente_id']) && $_GET['expediente_id'] !== '')) {

    $tipo = api_resolve_tipo_expediente(
        $auth,
        isset($_GET['expediente_id']) ? $_GET['expediente_id'] : null,
        isset($_GET['expediente']) ? $_GET['expediente'] : null
    );

    $where[] = 'pe.expedienteId = ' . (int)$tipo['expedienteId'];
}

$soloExistentes = isset($_GET['solo_existentes']) && $_GET['solo_existentes'] === '1';

$db->setQuery('SELECT pe.expedienteId, pe.path, pe.fecha,
                      e.name AS tipoNombre, e.status, e.extension
                 FROM personalExpedientes pe
                 INNER JOIN expedientes e ON e.expedienteId = pe.expedienteId
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY e.name ASC');

$filas       = $db->GetResult();
$expedientes = array();
$resumen     = array('asignados' => 0, 'conArchivo' => 0, 'faltantes' => 0, 'bytes' => 0);

foreach ($filas as $fila) {
    $tieneRegistro = ($fila['path'] !== null && $fila['path'] !== '');
    $ruta          = $tieneRegistro ? api_expediente_path($personalId, $fila['path']) : null;
    $existe        = ($ruta !== null);

    if ($soloExistentes && !$existe) {
        continue;
    }

    $bytes = $existe ? (int)filesize($ruta) : null;

    $expedientes[] = array(
        'expedienteId'      => (int)$fila['expedienteId'],
        'nombre'            => $fila['tipoNombre'],
        'status'            => $fila['status'],
        'extensionEsperada' => $fila['extension'],
        'tieneArchivo'      => $tieneRegistro,
        'existeEnDisco'     => $existe,
        'nombreArchivo'     => $existe
            ? api_expediente_filename($fila['tipoNombre'], $empleado['name'], $fila['path'])
            : null,
        'bytes'             => $bytes,
        'mimeType'          => $tieneRegistro ? api_mime_from_name($fila['path']) : null,
        'fecha'             => $fila['fecha'],
        // URL ya firmada (HMAC + vencimiento): el navegador la abre directo,
        // sin cabecera Authorization. Caduca en API_SIGN_TTL.
        'urlDescarga'       => $existe
            ? api_expediente_signed_url($personalId, (int)$fila['expedienteId'])
            : null,
    );

    $resumen['asignados']++;

    if ($existe) {
        $resumen['conArchivo']++;
        $resumen['bytes'] += $bytes;
    } else {
        $resumen['faltantes']++;
    }
}

api_log('manifest', array(
    'apiClientId'  => $auth['apiClientId'],
    'apiTokenId'   => $auth['apiTokenId'],
    'resourceType' => 'expediente',
    'resourceId'   => $personalId,
    'detail'       => 'expediente de empleado ' . $personalId . ': '
                    . $resumen['conArchivo'] . '/' . $resumen['asignados'] . ' con archivo',
));

api_json(array(
    'empleado'    => api_empleado_publico($empleado),
    'resumen'     => $resumen,
    'expedientes' => $expedientes,
));
