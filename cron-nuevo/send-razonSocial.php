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

$sql = "SELECT * FROM personal WHERE (departamentoId=21 OR personalId=65) AND active='1' ORDER BY personalId";
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

    $contracts = $contractRep->SearchOnlyContract($persons, true,true,$itemEmploye['personalId']);
    if(empty($contracts))
        continue;

    $departamentos->setDepartamentoId($itemEmploye['departamentoId']);
    $depto =  $departamentos->GetNameById();

    $smarty->assign("customers", $contracts);
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html = $smarty->fetch(DOC_ROOT.'/templates/lists/list-razonSocial.tpl');
    $file = strtoupper(substr($depto,0,2))."-RSOCIALES-".trim(strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId']);
    $excel->ConvertToExcel($html, 'xlsx', false, $file,true,500);

    $subject= $file;
    $body   = "Estimado usuario: se le hace llegar las razones sociales que tiene a su cargo en el area de administracion, de forma directa o indirectamente mediante sus subordinados.
               <br><br>
               Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;
    if(REP_STATUS=='test')
        $to = array(EMAIL_DEV=>'Desarrollador');
    else
        $to = array($itemEmploye['email']=>$itemEmploye['name'],EMAIL_DEV=>'Desarrollador');
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";
    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ADMINISTRADOR DE SISTEMA") ;
    echo "reporte enviado correctamente"."\n";
    unlink($attachment);
}