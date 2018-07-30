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

$sql = "SELECT personalId  FROM personal WHERE active='1'  ORDER BY personalId ASC";
$db->setQuery($sql);
$personas = $db->GetResult();
$expedientes =  $expediente->Enumerate();
$expedientes = $util->ConvertToLineal($expedientes,'expedienteId');
$llave = array_search(13,$expedientes);

unset($expedientes[$llave]);
foreach($personas as $key=>$person){
    $local = $expedientes;
    $sqli="";
    $find=array();
    $sqle ="SELECT expedienteId FROM personalExpedientes WHERE personalId='".$person['personalId']."' ";
    $db->setQuery($sqle);
    $finds = $db->GetResult();
    $finds = $util->ConvertToLineal($finds,'expedienteId');
    foreach($finds as $fn){
        $k = array_search($fn,$local);
        unset($local[$k]);
    }

    if(count($local)<=0){
        echo "ID :".$person['personalId']."<br>";
        echo "En orden<br><br>";
        continue;
    }

    $sqli = 'REPLACE INTO personalExpedientes(personalId,expedienteId) VALUES';

    foreach($local as $exp){
        if($exp===end($local))
            $sqli .="(".$person['personalId'].",".$exp.");";
        else
            $sqli .="(".$person['personalId'].",".$exp."),";
    }

    $db->setQuery($sqli);
    $db->UpdateData();
    echo "ID :".$person['personalId']."<br>";
    echo "consulta :".$sqli."<br><br>";
}