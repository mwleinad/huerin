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

$sql = "SELECT personalId  FROM personal WHERE active='1' and personalId=259 ORDER BY personalId ASC";
$db->setQuery($sql);
$personas = $db->GetResult();
foreach($personas as $key=>$person){
    $sqle ="SELECT expedienteId,path FROM personalExpedientes WHERE personalId='".$person['personalId']."' ";
    $db->setQuery($sqle);
    $findExpedientes = $db->GetResult();
    foreach($findExpedientes as $k=>$v){
        $nameFile = "employe_file".$person['personalId'].$v['expedienteId'].".pdf";
        $file = DOC_ROOT."/expedientes/".$person["personalId"]."/".$nameFile;
        if(file_exists($file)){
            $extensionActual =  end(explode(".",$v["path"]));
            if($v["path"]==""||$extensionActual!='pdf'){
                $fecha = date("Y-m-d",filemtime($file));
                $sql = "update personalExpedientes set path='$nameFile',fecha='$fecha' where personalId='".$person["personalId"]."' and expedienteId='".$v["expedienteId"]."'   ";
                $db->setQuery($sql);
                $db->UpdateData();
               }
        }
    }
}