<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html" && $_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html")
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

if (!isset($_SESSION))
{
    session_start();
}
$current =  date("Y-m-d");
$firstDay =  $util->getFirstDate($current);

echo "permiso para eejcutr correcto ".$firstDay."\n";

