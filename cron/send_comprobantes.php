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
$_SESSION['empresaId'] = IDEMPRESA;
$db->setQuery("SELECT comprobanteId,serie,folio FROM comprobante WHERE date(fecha) = (CURDATE() - INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY)
    and (date_format(fecha, '%H:%i:%s') > '01:41:22') and sent='no' ");
$comprobantes = $db->GetResult();
$razon = new Razon();
$totalEnviar = count($comprobantes);
$enviado = 0;
foreach($comprobantes as $Key => $factura) {
    if($razon->sendComprobante33($factura["comprobanteId"], false, true,false)) {
        $enviado++;
    }
}
echo "$enviado de $totalEnviar  facturas enviadas.".chr(10).chr(13);
echo "Cron Completado Satisfactoriamente";
?>
