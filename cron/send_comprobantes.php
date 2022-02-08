<?php
if(!$_SERVER["DOCUMENT_ROOT"]) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
($_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html" && $_SERVER['DOCUMENT_ROOT'] != "/var/www/qplatform/public_html")
? session_save_path("C:/laragon/tmp")
: session_save_path("/tmp");

define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
include_once(DOC_ROOT.'/init_cron.php');
include_once(DOC_ROOT.'/constants.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries33.php');

if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['empresaId'] = IDEMPRESA;
$sql  = "SELECT comprobanteId,serie,folio FROM comprobante";
$sql .= " WHERE date_format(fecha,'%Y-%m-%d') > '2022-01-01'";
$sql .= " AND status = '1' ";
$sql .= " AND sent='no' ORDER BY comprobanteId ASC LIMIT 1";
$db->setQuery($sql);
$comprobantes = $db->GetResult();
$razon = new Razon();
$enviado = 0;
foreach($comprobantes as $Key => $factura) {
    if(!$razon->sendComprobante33($factura["comprobanteId"], false, true))
        echo 'Error al enviar comprobante '.$factura['serie'].$factura['folio'].chr(13).chr(10);
}
echo "Cron Completado Satisfactoriamente";
?>
