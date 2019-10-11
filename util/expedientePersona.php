<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/07/2018
 * Time: 12:33 PM
 */
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT."/libraries.php");

$sql = "SELECT expedienteId  FROM expedientes WHERE status='activo' and upper(name) not like'%INFONAVIT O FONACOT%'";
$db->setQuery($sql);
$expedientes = $db->GetResult();
$expedientes = $util->ConvertToLineal($expedientes,'expedienteId');

$sql = "SELECT personalId  FROM personal WHERE active='1' ORDER BY personalId ASC";
$db->setQuery($sql);
$personas = $db->GetResult();
$personUpdate = 0;
foreach($personas as $key=>$person){
    $up =  false;
    $personalId = $person["personalId"];
    $sqle ="SELECT expedienteId,path FROM personalExpedientes WHERE personalId='$personalId' ";
    $util->DBSelect($_SESSION['empresaId'])->setQuery("SELECT expedienteId from personalExpedientes WHERE personalId='$personalId' ");
    $arrayExp = $util->DBSelect($_SESSION['empresaId'])->GetResult();
    $expActual = $util->ConvertToLineal($arrayExp,'expedienteId');

    foreach($expedientes as $exp){
        if(in_array($exp,$expActual))
            continue;

        $sql =  "INSERT INTO personalExpedientes(personalId,expedienteId)VALUES($personalId,$exp)";
        $util->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $util->DBSelect($_SESSION['empresaId'])->InsertData();
        $up = true;
    }
    if($up)
        $personUpdate++;
}
echo "Actualizados $personUpdate";