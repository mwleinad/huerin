<?php
/**
 * Guardian compartido de las descargas por ruta (/download.php y
 * /util/download.php).
 *
 * Ambos scripts hacian readfile(DOC_ROOT."/".$_GET["file"]) sin validar
 * sesion ni sanitizar el parametro, asi que cualquiera en internet podia
 * pedir download.php?file=config.php y llevarse las credenciales de BD y
 * SMTP. Este archivo centraliza el blindaje para que los dos scripts
 * compartan exactamente las mismas reglas.
 *
 * Estrategia: lista blanca de raices. La ruta pedida se normaliza, se
 * resuelve con realpath() y solo se sirve si cae dentro de una de las
 * carpetas que la aplicacion usa de verdad para descargar.
 *
 * Compatible con PHP 5.6+.
 */

/**
 * Carpetas bajo DOC_ROOT desde las que SI se puede descargar.
 * Deducidas de los ~50 puntos de invocacion reales de la aplicacion.
 * Agregar una raiz nueva es agregar un renglon aqui.
 */
function dl_allowed_dirs()
{
    return array(
        'sendFiles',      // reportes generados (xlsx/pdf/csv)
        'archivos',       // adjuntos de contrato y zips de tareas
        'documentos',     // documentos por empresa
        'requerimientos', // requerimientos por empresa
        'tasks',          // archivos del workflow
        'payments',       // comprobantes de pago
        'filesPendiente', // adjuntos de cambios de plataforma
        'backup_db',      // respaldos (DIR_BACKUP)
    );
}

/**
 * Archivos sueltos permitidos, por nombre exacto.
 *
 * Sobre todo los de la raiz, que no pueden abrirse por carpeta porque ahi
 * mismo vive config.php. pdf/vistaPrevia.pdf va aqui y no como carpeta
 * porque /pdf/ es la libreria dompdf: abrirla expondria su codigo fuente.
 */
function dl_allowed_files()
{
    return array(
        'exportar.pdf',
        'exportar.xlsx',
        'exportar3.pdf',
        'exportar3.xlsx',
        'reporte_comprobantes.csv',
        'pdf/vistaPrevia.pdf',
    );
}

/**
 * Rutas permitidas por patron. Solo los CFDI ya emitidos.
 *
 * No se abre /empresas/ completo a proposito: bajo certificados/ viven los
 * .key y .cer de los sellos digitales, que no deben poder descargarse.
 */
function dl_allowed_patterns()
{
    return array(
        '#^empresas/\d+/certificados/[^/]+/facturas/(xml|pdf|qr)/[^/]+$#',
    );
}

/** Extensiones que nunca se sirven, caiga donde caiga el archivo. */
function dl_denied_extensions()
{
    return array(
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phps', 'phar',
        'inc', 'ini', 'env', 'conf', 'htaccess', 'htpasswd',
        'key', 'pem', 'p12', 'pfx',
        'sh', 'bat', 'cmd', 'exe', 'dll',
    );
}

/** Corta la peticion con un codigo y un mensaje minimo. */
function dl_deny($status, $message)
{
    if (!headers_sent()) {
        header('HTTP/1.1 ' . $status);
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }

    echo $message . "\n";
    exit;
}

/** Exige sesion iniciada. Debe llamarse ANTES de emitir cabeceras. */
function dl_require_login()
{
    if (!isset($_SESSION)) {
        session_start();
    }

    if (empty($_SESSION['User']['isLogged'])) {
        if (!headers_sent()) {
            header('Location: ' . WEB_ROOT . '/login');
        }
        exit;
    }
}

/**
 * Normaliza el valor recibido a una ruta relativa a DOC_ROOT.
 *
 * Tolera lo que la aplicacion ya manda hoy: URLs absolutas con WEB_ROOT,
 * valores url-encoded y barras duplicadas.
 */
function dl_normalize($raw)
{
    $path = (string)$raw;

    // El byte nulo trunca la cadena en las llamadas de sistema.
    if (strpos($path, "\0") !== false) {
        return null;
    }

    $path = urldecode($path);

    if (strpos($path, "\0") !== false) {
        return null;
    }

    // Se descarta cualquier cosa a partir de ? o #.
    $path = preg_replace('/[?#].*$/', '', $path);

    // Quita WEB_ROOT en sus variantes http/https, y el host suelto.
    $path = str_replace(WEB_ROOT, '', $path);
    $path = preg_replace('#^https?://[^/]+#i', '', $path);

    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#/+#', '/', $path);   // colapsa barras duplicadas
    $path = ltrim($path, '/');

    if ($path === '') {
        return null;
    }

    // Ningun segmento puede ser .. ni .
    foreach (explode('/', $path) as $segment) {
        if ($segment === '..' || $segment === '.') {
            return null;
        }
    }

    return $path;
}

/** Indica si la ruta relativa cae en la lista blanca. */
function dl_is_allowed($rel)
{
    if (in_array($rel, dl_allowed_files(), true)) {
        return true;
    }

    foreach (dl_allowed_patterns() as $pattern) {
        if (preg_match($pattern, $rel)) {
            return true;
        }
    }

    foreach (dl_allowed_dirs() as $dir) {
        if (strpos($rel, $dir . '/') === 0) {
            return true;
        }
    }

    return false;
}

/**
 * Valida y resuelve la ruta pedida.
 *
 * Devuelve la ruta absoluta lista para servir, o corta la peticion.
 */
function dl_resolve($raw)
{
    dl_require_login();

    $rel = dl_normalize($raw);

    if ($rel === null) {
        dl_deny('400 Bad Request', 'Solicitud invalida.');
    }

    $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));

    if (in_array($ext, dl_denied_extensions(), true)) {
        dl_deny('403 Forbidden', 'Tipo de archivo no permitido.');
    }

    if (!dl_is_allowed($rel)) {
        dl_deny('403 Forbidden', 'Ruta no permitida.');
    }

    $docRoot = rtrim(str_replace('\\', '/', DOC_ROOT), '/');
    $full    = $docRoot . '/' . $rel;

    if (!is_file($full)) {
        dl_deny('404 Not Found', 'El archivo no existe.');
    }

    // realpath() resuelve enlaces simbolicos: la comprobacion final se hace
    // sobre la ruta ya resuelta, no sobre la que llego por parametro.
    $real = realpath($full);

    if ($real === false) {
        dl_deny('404 Not Found', 'El archivo no existe.');
    }

    $real = str_replace('\\', '/', $real);

    if (strpos($real, $docRoot . '/') !== 0) {
        dl_deny('403 Forbidden', 'Ruta no permitida.');
    }

    // Se revalida la lista blanca contra la ruta resuelta, por si un enlace
    // simbolico apuntaba fuera de su carpeta.
    if (!dl_is_allowed(substr($real, strlen($docRoot) + 1))) {
        dl_deny('403 Forbidden', 'Ruta no permitida.');
    }

    return $real;
}

/**
 * Sirve el archivo ya validado y termina.
 *
 * Se manda siempre como adjunto: aunque el archivo sea HTML o SVG, el
 * navegador no lo interpreta en el dominio del sistema.
 */
function dl_serve($real, $mimeTypes = array())
{
    $name = basename($real);
    $name = str_replace(array("\r", "\n", '"'), '', $name);
    $ext  = strtolower(pathinfo($real, PATHINFO_EXTENSION));

    $mime = isset($mimeTypes[$ext]) && $mimeTypes[$ext] !== ''
        ? $mimeTypes[$ext]
        : 'application/octet-stream';

    header('X-Content-Type-Options: nosniff');
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($real));
    header('Cache-Control: private, must-revalidate');
    header('Pragma: public');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    readfile($real);
    exit;
}
