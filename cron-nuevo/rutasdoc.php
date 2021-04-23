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
    $response = $cancelation->getStatus($row['rfc_e'], $row['rfc_r'], $row['uuid'], $row['total']);
    $cancelation->processCancelation($row, $response);
    dd($response);
    echo "UUID: ".$row['uuid']." => ".$response['status'].chr(13).chr(10);
}
