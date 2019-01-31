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
$invoice = new Invoice();
$timeStart = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);;
//
if (!isset($_SESSION))
{
    session_start();
}
$current =  date("Y-m-d");
$firstDay =  $util->getFirstDate($current);
if($current==$firstDay)
{
    if(strtotime(date('H:i:s'))<strtotime('06:00:00')){
        echo "ejecutado antes de las 06:00:00 del dia ".$firstDay.chr(13);
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