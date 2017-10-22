<<<<<<< HEAD
<?php
//exit;
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
include_once(DOC_ROOT.'/libraries33.php');

include_once(DOC_ROOT.'/services/AutomaticCfdi.php');
$automaticCfdi = new AutomaticCfdi();
$timeStart = date("d-m-Y").' a las '.date('H:i:s');

if (!isset($_SESSION))
{
    session_start();
}
$_SESSION['empresaId'] = 15;

$mask = DOC_ROOT.'/temp/15_A_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$mask = DOC_ROOT.'/temp/20_B_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
if(date("d") < 25)
{
    $automaticCfdi->CreateServiceInvoices();
}

$time = date("d-m-Y").' a las '.date('H:i:s');
$entry = "Cron ejecutado desde ".$timeStart." hasta $time Hrs.";
$file = DOC_ROOT."/cron/facturas.txt";
$open = fopen($file,"w");

if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
}

=======
<?php
//exit;
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
include_once(DOC_ROOT.'/libraries33.php');

include_once(DOC_ROOT.'/services/AutomaticCfdi.php');
$automaticCfdi = new AutomaticCfdi();
$timeStart = date("d-m-Y").' a las '.date('H:i:s');

if (!isset($_SESSION))
{
    session_start();
}
$_SESSION['empresaId'] = 15;

$mask = DOC_ROOT.'/temp/15_A_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
$mask = DOC_ROOT.'/temp/20_B_*.*';
$array = glob($mask);
array_map('unlink', glob($mask));
if(date("d") < 25)
{
    $automaticCfdi->CreateServiceInvoices();
}

$time = date("d-m-Y").' a las '.date('H:i:s');
$entry = "Cron ejecutado desde ".$timeStart." hasta $time Hrs.";
$file = DOC_ROOT."/cron/facturas.txt";
$open = fopen($file,"w");

if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
}

>>>>>>> 08559b24205dbd52e0581045a0fddca3f4d53fec
?>