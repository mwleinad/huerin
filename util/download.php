<?php
/**
 * Descarga de archivos por ruta (variante con ruta partida en tres).
 *
 * Uso historico (sin cambios para quien lo invoca):
 *   /util/download.php?file=<ruta>
 *   /util/download.php?path=<...>&secPath=<...>&filename=<...>
 *
 * Antes servia cualquier ruta sin validar sesion. Ahora exige sesion
 * iniciada y solo sirve archivos dentro de la lista blanca de
 * util/download-guard.php.
 *
 * El parametro contentType que mandan algunas plantillas se sigue
 * ignorando: el tipo se deduce de la extension real del archivo.
 */

include_once('../init_files.php');
include_once('../config.php');
include_once(DOC_ROOT . '/properties/errors.es.php');
include_once(DOC_ROOT . '/util/download-guard.php');

$file = isset($_GET['file']) ? $_GET['file'] : '';

if ($file === '') {
    $file = (isset($_GET['path']) ? $_GET['path'] : '')
          . '/' . (isset($_GET['secPath']) ? $_GET['secPath'] : '')
          . '/' . (isset($_GET['filename']) ? $_GET['filename'] : '');
}

$real = dl_resolve($file);

dl_serve($real, isset($mime_types) ? $mime_types : array());
