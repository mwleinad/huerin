<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/04/2018
 * Time: 06:37 PM
 */
//si el dia que se ejecuta es mayor que los dias 25 de cada mes se anula
if(date('d')>=25)
{
    echo "ejecutado en los ultimos dias de mes, se anula tarea".chr(13);
    exit;
}
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
    session_save_path("C:/xampp/tmp");
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
include_once(DOC_ROOT.'/services/invoice.class.php');

if (!isset($_SESSION))
{
    session_start();
}
$current =  date("Y-m-d");
$firstDay =  $util->getFirstDate($current);
if($current==$firstDay)
{
    if(strtotime(date('H:i:s'))<strtotime('02:30:00')){
        echo "ejecutado antes de las 02:30:00 del dia ".$firstDay.chr(13);
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