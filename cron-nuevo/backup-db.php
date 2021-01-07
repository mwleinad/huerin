<?php
ini_set('memory_limit','3G');
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

$backup =  new Backup();
$sufijo = date("Y-m-d H:i:s");
$sufijo =  str_replace(" ","-",$sufijo);
$sufijo =  str_replace(":","_",$sufijo);
$sufijo =  $sufijo.".sql.gz";
$backup->setCustomNameBackup("huerin_".$sufijo);
if($backup->CreateBackup()){
    $backup->SendBackupToEmail();
}
else{
    echo "Respaldo no realizado";
}

