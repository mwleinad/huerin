<?php
/**
 * Descarga de archivos por ruta.
 *
 * Uso historico (sin cambios para quien lo invoca):
 *   /download.php?file=<ruta absoluta con WEB_ROOT o relativa a DOC_ROOT>
 *
 * Antes servia cualquier ruta sin validar sesion, de modo que
 * download.php?file=config.php entregaba las credenciales de BD y SMTP.
 * Ahora exige sesion iniciada y solo sirve archivos dentro de la lista
 * blanca de util/download-guard.php.
 */

include_once('init_files.php');
include_once('config.php');
include_once(DOC_ROOT . '/properties/errors.es.php');
include_once(DOC_ROOT . '/util/download-guard.php');

$real = dl_resolve(isset($_GET['file']) ? $_GET['file'] : '');

dl_serve($real, isset($mime_types) ? $mime_types : array());
