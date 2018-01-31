<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 31/01/2018
 * Time: 10:40 AM
 */
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

$sql = "SELECT * FROM personal WHERE tipoPersonal NOT IN('socio','asistente') 
        AND (lastSendEmail < DATE(NOW()) OR lastSendEmail IS NULL) ORDER BY personalId ASC LIMIT 3";
$db->setQuery($sql);
$employees = $db->GetResult($sql);
