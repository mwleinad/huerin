<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    session_save_path("/tmp");
}
define('DOC_ROOT', $docRoot);

include_once(DOC_ROOT.'/init_cron.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/constants.php');
include_once(DOC_ROOT.'/libraries33.php');

if (!isset($_SESSION)) {
  session_start();
}
$db->setQuery("SELECT comprobanteId,serie,folio FROM comprobante WHERE comprobanteId = 68567");
$comprobantes = $db->GetResult();
$razon = new Razon();
$totalEnviar = count($comprobantes);
$enviado = 0;
foreach($comprobantes as $Key => $factura) {
    if($razon->sendComprobante33($factura["comprobanteId"], false, true)) {
        $enviado++;
    }
}
echo "$enviado de $totalEnviar  facturas enviadas.".chr(10).chr(13);
echo "Cron Completado Satisfactoriamente";
?>
