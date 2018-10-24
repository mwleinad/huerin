<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/04/2018
 * Time: 06:37 PM
 */

if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
define('DOC_ROOT', $docRoot);
session_save_path("/tmp");
include_once(DOC_ROOT.'/init_cron.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/constants.php');
include_once(DOC_ROOT.'/libraries33.php');
include_once(DOC_ROOT.'/services/invoice.class.php');
$invoice = new Invoice();
$timeStart = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);;

if (!isset($_SESSION))
{
    session_start();
}
$_SESSION['empresaId'] = IDEMPRESA;
$mask = DOC_ROOT.'/temp/15_A_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$mask = DOC_ROOT.'/temp/20_B_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$entry ="";
$res = $invoice->CreateInvoicesAutomatic();
$entry .=$res['log'];
$time = date("d-m-Y").' a las '.date('H:i:s');
$entry .= "Cron ejecutado desde ".$timeStart." hasta $time Hrs.".chr(13).chr(10);;
$file = DOC_ROOT."/sendFiles/logInvoices.txt";
$open = fopen($file,"w");
if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
    //enviar por correo el log solo si se crearon facturas
    if($res['totalContract']>0){
        $sendmail = new SendMail;
        $sendmail->Prepare('LOG INVOICES','Logs invoices','isc061990@outlook.com','HBKRUZPE',$file,'logInvoices.txt','','',FROM_MAIL);
    }

}