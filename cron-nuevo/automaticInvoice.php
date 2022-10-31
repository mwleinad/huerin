<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html" && $_SERVER['DOCUMENT_ROOT'] != "/var/www/qplatform/public_html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."";
    session_save_path("C:/laragon/tmp");
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

if (!isset($_SESSION))
{
    session_start();
}
$current =  date("Y-m-d");
$firstDay =  $util->getFirstDate($current);
if($current==$firstDay)
{
    if(strtotime(date('H:i:s'))<strtotime('02:00:00')){
        echo "ejecutado antes de las 02:00:00 del dia ".$firstDay.chr(13);
        exit;
    }
}

$_SESSION['empresaId'] = IDEMPRESA;
$mask = DOC_ROOT.'/temp/15_A_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$mask = DOC_ROOT.'/temp/20_B_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$entry =  " inicio ". date("Y-m-d H:i:s").chr(13).chr(10);
$invoiceService->GenerateInvoices();
$entry  .=" fin ". date("Y-m-d H:i:s").chr(13).chr(10);
echo $entry;
