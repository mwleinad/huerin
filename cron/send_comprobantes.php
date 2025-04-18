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

$condicion = SEND_FACT_CUSTOMER === 'SI' ? " AND (sent='no' OR sentCliente='No') " : " AND sent='no' ";
$sql  = "SELECT comprobanteId,serie,folio,sent,sentCliente FROM comprobante";
$sql .= " WHERE date_format(fecha,'%Y-%m-%d') > '2022-01-01'";
$sql .= " AND status = '1' AND tiposComprobanteId  IN (1,10)";
$sql .= $condicion." ORDER BY comprobanteId ASC LIMIT 12";

$db->setQuery($sql);
$comprobantes = $db->GetResult();
$razon = new Razon();
$enviado = 0;
$log = "------------------------------------------------- ----------------------------".chr(13).chr(10);
$log .="------------ INICIO DE CRONJON ".date("Y-m-d H:i:s")." ----------------".chr(13).chr(10);
foreach($comprobantes as $Key => $factura) {

    if($factura['sent'] === 'no') {
        if (!$razon->enviarComprobante($factura['comprobanteId'], 'Responsable CxC', false))
            echo 'ERROR: Ha ocurrido un error al enviar comprobante ' . $factura['serie'] . $factura['folio'] . " al Responsable de CxC." . chr(13) . chr(10);
    }
    if (SEND_FACT_CUSTOMER == 'SI' && $factura['sentCliente'] === 'No') {
        if (!$razon->enviarComprobante($factura['comprobanteId'], 'Cliente', false))
            echo 'ERROR: Ha ocurrido un error al enviar comprobante '.$factura['serie'].$factura['folio']." al cliente.".chr(13).chr(10);
    }

}
$log .= "------------ FIN DE CRONJON ".date("Y-m-d H:i:s")." -------------------".chr(13).chr(10);
$log .= "------------------------------------------------- ----------------------------".chr(13).chr(10);
echo $log;
?>
