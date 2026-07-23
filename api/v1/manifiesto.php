<?php
/**
 * GET /api/v1/manifiesto.php?contract_id=2663
 * GET /api/v1/manifiesto.php?rfc=ITB190308MW8      (atajo, sin pasar por empresas.php)
 *
 * Devuelve el inventario completo de una empresa, agrupado en tres bloques
 * (documentos, archivos, requerimientos). Dentro de cada bloque los registros
 * se agrupan por tipo, y cada tipo lista sus N versiones ordenadas de la mas
 * antigua a la mas reciente.
 *
 * Parametros:
 *   contract_id  Entero. Obligatorio salvo que se envie rfc.
 *   rfc          RFC exacto. Alternativa a contract_id. Si el RFC pertenece a
 *                mas de una empresa responde 409 con la lista de candidatas.
 *   grupo        Opcional: documentos | archivos | requerimientos.
 *                Limita la respuesta a un solo bloque.
 *   solo_existentes  Opcional, "1" para omitir registros cuyo archivo ya no
 *                    esta en disco.
 *
 * Requiere: Authorization: Bearer <token>
 */

require_once(dirname(__FILE__) . '/bootstrap.php');

api_require_method('GET');

$auth     = api_require_token();
$contract = api_resolve_contract($auth);

$contractId = (int)$contract['contractId'];
$db         = api_db();

$bloques = array(
    'documentos'     => 'documento',
    'archivos'       => 'archivo',
    'requerimientos' => 'requerimiento',
);

if (isset($_GET['grupo']) && $_GET['grupo'] !== '') {
    if (!isset($bloques[$_GET['grupo']])) {
        api_fail(400, 'bad_request', 'El parametro grupo debe ser documentos, archivos o requerimientos.');
    }

    $bloques = array($_GET['grupo'] => $bloques[$_GET['grupo']]);
}

$soloExistentes = isset($_GET['solo_existentes']) && $_GET['solo_existentes'] === '1';

$mapa      = api_resource_map();
$resultado = array();
$resumen   = array('registros' => 0, 'faltantes' => 0, 'bytes' => 0);

foreach ($bloques as $bloque => $recurso) {
    $meta   = $mapa[$recurso];
    $campos = 'r.' . $meta['pk'] . ' AS id, r.' . $meta['fkTipo'] . ' AS tipoId, r.path';

    if ($meta['campoFecha'] !== null) {
        $campos .= ', r.`' . $meta['campoFecha'] . '` AS fecha';
    }

    $db->setQuery('SELECT ' . $campos . ", t.`" . $meta['campoTipo'] . "` AS tipoNombre
                     FROM `" . $meta['tabla'] . "` r
                     LEFT JOIN `" . $meta['tablaTipo'] . "` t
                            ON t.`" . $meta['fkTipo'] . "` = r.`" . $meta['fkTipo'] . "`
                    WHERE r.contractId = " . $contractId . '
                    ORDER BY tipoNombre ASC, r.' . $meta['pk'] . ' ASC');

    $filas  = $db->GetResult();
    $tipos  = array();

    foreach ($filas as $fila) {
        $ruta   = api_resource_path($meta['carpeta'], $contractId, $fila['path']);
        $existe = ($ruta !== null);

        if ($soloExistentes && !$existe) {
            continue;
        }

        $bytes = $existe ? (int)filesize($ruta) : null;

        $version = array(
            'id'           => (int)$fila['id'],
            'nombre'       => $fila['path'],
            'existeEnDisco' => $existe,
            'bytes'        => $bytes,
            'mimeType'     => api_mime_from_name($fila['path']),
            'urlDescarga'  => WEB_ROOT . '/api/v1/descargar.php?tipo=' . $recurso . '&id=' . (int)$fila['id'],
        );

        if ($meta['aliasFecha'] !== null) {
            $version[$meta['aliasFecha']] = isset($fila['fecha']) ? $fila['fecha'] : null;
        }

        $tipoId     = (int)$fila['tipoId'];
        $tipoNombre = ($fila['tipoNombre'] !== null && $fila['tipoNombre'] !== '')
            ? $fila['tipoNombre']
            : 'Sin tipo';

        $clave = $tipoId . '|' . $tipoNombre;

        if (!isset($tipos[$clave])) {
            $tipos[$clave] = array(
                'tipoId'   => $tipoId,
                'tipo'     => $tipoNombre,
                'versiones' => array(),
            );
        }

        $tipos[$clave]['versiones'][] = $version;

        $resumen['registros']++;

        if ($existe) {
            $resumen['bytes'] += $bytes;
        } else {
            $resumen['faltantes']++;
        }
    }

    // Numeracion de versiones: 1 = la mas antigua, N = la vigente.
    $lista = array();

    foreach ($tipos as $tipo) {
        $total = count($tipo['versiones']);

        foreach ($tipo['versiones'] as $i => $version) {
            $version['version']       = $i + 1;
            $version['esUltima']      = ($i + 1) === $total;
            $tipo['versiones'][$i]    = $version;
        }

        $tipo['totalVersiones'] = $total;
        $lista[] = $tipo;
    }

    $resultado[$bloque] = $lista;
}

api_log('manifest', array(
    'apiClientId' => $auth['apiClientId'],
    'apiTokenId'  => $auth['apiTokenId'],
    'contractId'  => $contractId,
    'detail'      => $resumen['registros'] . ' registros',
));

api_json(array(
    'empresa' => array(
        'contractId'      => $contractId,
        'rfc'             => $contract['rfc'],
        'razonSocial'     => $contract['name'],
        'nombreComercial' => $contract['nombreComercial'],
        'activo'          => $contract['activo'],
    ),
    'resumen'   => $resumen,
    'contenido' => $resultado,
));
