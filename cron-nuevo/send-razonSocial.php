<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 02/03/2018
 * Time: 03:20 PM
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

$sql = "SELECT * FROM personal WHERE departamentoId=21 AND active='1' ORDER BY personalId";
$db->setQuery($sql);
$employees = $db->GetResult($sql);
foreach($employees as $key=>$itemEmploye){
    $persons = array();
    $deptos =  array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    $deptos  = $util->ConvertToLineal($subordinados, 'dptoId');

    array_unshift($persons, $itemEmploye['personalId']);
    array_unshift($deptos, $itemEmploye['departamentoId']);
    $deptos = array_unique($deptos);

    $contracts = $contractRep->SearchOnlyContract($persons, true);
    if(empty($contracts))
        continue;
    foreach($contracts as $ky=>$val){
        $personal->setPersonalId($val['respContabilidad']);
        $contracts[$ky]['respContabilidad']= $personal->GetNameById();
        $personal->setPersonalId($val['respNominas']);
        $contracts[$ky]['respNominas']= $personal->GetNameById();
        $personal->setPersonalId($val['respAdministracion']);
        $contracts[$ky]['respAdministracion']= $personal->GetNameById();
        $personal->setPersonalId($val['respJuridico']);
        $contracts[$ky]['respJuridico']= $personal->GetNameById();
        $personal->setPersonalId($val['respImss']);
        $contracts[$ky]['respImss']= $personal->GetNameById();
        $personal->setPersonalId($val['respMensajeria']);
        $contracts[$ky]['respMensajeria']= $personal->GetNameById();
        $personal->setPersonalId($val['respAuditoria']);
        $contracts[$ky]['respAuditoria']= $personal->GetNameById();
    }
    $smarty->assign("customers", $contracts);
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html = $smarty->fetch(DOC_ROOT.'/templates/lists/list-razonSocial.tpl');
    $file = strtoupper(substr($depto,0,2))."-RSOCIALES-".trim(strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId']);
    $excel->ConvertToExcel($html, 'xlsx', false, $file,true,500);

}