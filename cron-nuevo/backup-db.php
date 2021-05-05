<?php
ini_set('memory_limit','3G');
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
$docRoot = $_SERVER['DOCUMENT_ROOT'];
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

$backup =  new Backup();
$sufijo = date("Y-m-d H:i:s");
$sufijo =  str_replace(" ","-",$sufijo);
$sufijo =  str_replace(":","_",$sufijo);
$sufijo =  "bk_".$sufijo.".sql.gz";
$backup->setCustomNameBackup($sufijo);
if($backup->CreateBackup()){
    $send =  new SendMail();
    $mails = ['isc061990@gmail.com'=>"Hector", "isc061990@outlook.com"=>'Dev'];
    $body = "Se ha creado el respaldo ". $sufijo. " de la base de datos de plataforma \n";
    $body .="en la ruta siguiente : ". DOC_ROOT.DIR_BACKUP."/".$sufijo." \n";
    $send->PrepareMultiple("Confirmacion de respaldo de bd", $body, $mails,"","","","","","admin@braunhuerin.com.mx","Respaldo DB Plataforma");
}
else{
    echo "Respaldo no realizado";
}

