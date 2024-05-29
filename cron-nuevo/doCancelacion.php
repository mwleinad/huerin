<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');
$db->setQuery("SELECT * FROM pending_cfdi_cancel WHERE status = 'pending'");
$result = $db->GetResult();
foreach($result as $key => $row) {

    $db->setQuery("SELECT  
            xml, 
            userId,
            empresaId,
            rfcId
            FROM comprobante
			WHERE comprobanteId = " . $row['cfdi_id']);

    $comp = $db->GetRow();

    if(!$comp)
        continue;

    $xml = $comp["xml"];
    $rfcActivo = $comp["rfcId"];

    $xmlReaderService = new XmlReaderService;
    $xmlPath = DOC_ROOT . "/empresas/" . $comp["empresaId"] . "/certificados/" . $rfcActivo . "/facturas/xml/SIGN_" . $xml . ".xml";
    if(!is_file($xmlPath))
        continue;

    $xmlData = $xmlReaderService->execute($xmlPath, $comp['empresaId']);
    $rfcProvCertif = (string)$xmlData['timbreFiscal']['RfcProvCertif'];

    $response = $cancelation->getStatus($row['rfc_e'], $row['rfc_r'], $row['uuid'], $row['total']);
    if (!$response) {
        echo date('Y-m-d H:i:s') . ", UUID: " . $row['uuid'] . " => ha ocurrido un error,se intentara nuevamente mas tarde " . chr(13) . chr(10);
        continue;
    }

    $cancelation->processCancelation($row, $response);
    echo date('Y-m-d H:i:s') . ", UUID: " . $row['uuid'] . " => " . $response->get_sat_statusResult->sat->EstatusCancelacion . chr(13) . chr(10);

}
