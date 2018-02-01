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

foreach($employees as $key=>$itemEmploye) {
    if (!$util->ValidateEmail(trim($itemEmploye['email']))) {
        echo $itemEmploye['personalId'] . " correo no valido : " . $itemEmploye['email'];
        echo "<br>";
        $up = 'UPDATE personal SET lastSendEmail=" ' . date("Y-m-d") . ' " WHERE personalId=' . $itemEmploye["personalId"] . ' ';
        $db->setQuery($up);
        $db->UpdateData();
        continue;
    }
    $deptos = array();
    $persons = array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    $deptos = $util->ConvertToLineal($subordinados, 'dptoId');

    array_unshift($persons, $itemEmploye['personalId']);
    array_unshift($deptos, $itemEmploye['departamentoId']);
    $contracts = $contractRep->BuscarContractV2($persons, true, $deptos);

}
