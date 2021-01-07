<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 06/07/2018
 * Time: 02:24 AM
 */
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

$timeStart = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);;
$personal->setPersonalId(201);
$subordinados = $personal->Subordinados();
$subsLineal = $util->ConvertToLineal($subordinados,'personalId');
$subs = implode(',',$subsLineal);
/*
$contracts = $contractRep->BuscarContractV2($subsLineal,true);

echo count($contracts);*/

    $query = "CALL getListRazonSocial('','','','Activos','',201,'".$subs."',0);";
    $db->setQuery($query);
    $result = $db->GetResult();
    dd($result);
    echo count($result);

$time = date("d-m-Y").' a las '.date('H:i:s');
echo "Cron ejecutado desde ".$timeStart." hasta $time Hrs.".chr(13).chr(10);


