<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 02/03/2018
 * Time: 12:37 PM
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

$sql = "SELECT * FROM personal WHERE tipoPersonal NOT IN('socio','asistente') AND active='1' ORDER BY personalId ASC ";
$db->setQuery($sql);
$employees = $db->GetResult($sql);
$year = date('Y');

foreach($employees as $key => $itemEmployed){
    $persons = array();
    $deptos =  array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    $deptos  = $util->ConvertToLineal($subordinados, 'dptoId');

    array_unshift($persons, $itemEmploye['personalId']);
    array_unshift($deptos, $itemEmploye['departamentoId']);
    $deptos = array_unique($deptos);
}
