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

$sql = "SELECT * FROM personal WHERE (puesto like'%gerente%' OR  puesto like'%Gerente%' OR puesto like'%supervisor%' OR  puesto like'%Supervisor%') 
         ORDER BY personalId ASC";
$db->setQuery($sql);
$employees = $db->GetResult($sql);
foreach($employees as $key=>$itemEmploye){
    $persons = array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');;

    array_unshift($persons, $itemEmploye['personalId']);
    $contracts = $contractRep->SearchOnlyContract($persons, true);
    foreach ($contracts as $kc=>$vc){
        $personal->setPersonalId($vc['respContabilidad']);
        $contracts[$kc]['responsableContabilidad'] = $personal->GetNameById();
        $personal->setPersonalId($vc['respJuridico']);
        $contracts[$kc]['responsableJuridico'] = $personal->GetNameById();
    }
    $sortedArray = $util->orderMultiDimensionalArray($contracts,'nameContact');
    if(count($sortedArray)<=0)
        continue;

    //se comprueba que la ultima notifiacion de vencimiento ya haya pasado una semana
    if($itemEmploye['lastSendArchivo']!='0000-00-00' && $itemEmploye['lastSendArchivo']!=''){
        $last = strtotime('+1 week',strtotime($itemEmploye['lastSendArchivo']));
        $addweek = date('Y-m-d',$last);
        if(date('Y-m-d')<$addweek)
        {
            echo "No se envia correo a ".$itemEmploye['name'].": ultimo envio ".$itemEmploye['lastSendArchivo'];echo "<br>";
            continue;
        }
    }
    $html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 15px "Trebuchet MS";
						font-size:15px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 14px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						vertical-align: center;
						border-collapse: collapse;
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #686DAB;
						background: #686DAB;
						color: #FFFFFF;
						vertical-align: center;
						text-align: center;
						height: 50px;
					}
				</style>
			</head>
			';
    $smarty->assign("namePersonal", $itemEmploye['name']);
    $smarty->assign("registros", $sortedArray);
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html .= $smarty->fetch(DOC_ROOT.'/templates/lists/rep-fiel.tpl');
    $file = "ARCHIVOS-".strtoupper(substr($itemEmploye['name'],0,6));
    $excel->ConvertToExcel($html, 'xlsx', false, $file,true,100);

    $subject= $file;

    $body   = " SE HACE LLEGAR EL REPORTE DE ARCHIVOS VENCIDOS O PROXIMO A VENCER DE CLIENTES BAJO SU RESPONSABILIDAD
          <br><br>
          Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;

    $to = $itemEmploye['email'];
    $toName = $itemEmploye['name'];
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";

    $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ARCHIVOS") ;
    $up = 'UPDATE personal SET lastSendArchivo=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
    $db->setQuery($up);
    $db->UpdateData();
    echo "Reporte enviado a ".$itemEmploye['name'].": ultimo envio ".$itemEmploye['lastSendArchivo'].", envio reciente ".date('Y-m-d');echo "<br>";
    echo "<br>";
    unlink($attachment);
}
