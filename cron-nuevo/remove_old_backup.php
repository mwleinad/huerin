<?php
ini_set('memory_limit','3G');
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
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

/*///////array_map('unlink',glob("/var/www/html/sendFiles/backup/*.gz")); riesgoso */

$directorio = opendir(DOC_DIR_BACKUP);
while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
{
 if(end(explode('.',$archivo))=='gz'){
     unlink(DOC_DIR_BACKUP.$archivo);
 }
}



