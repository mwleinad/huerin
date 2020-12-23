<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 14/02/2018
 * Time: 10:29 AM
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

$filtroOrden="Cliente";

$sql = "SELECT * FROM personal WHERE (puesto like'%gerente%' OR  puesto like'%Gerente%' OR puesto like'%Supervisor%' OR  puesto like'%supervisor%') AND active='1'
         ORDER BY personalId";
$db->setQuery($sql);
$employees = $db->GetResult($sql);

$util->DB()->setQuery('SELECT * FROM tipoDocumento WHERE status="1" ORDER BY nombre ASC ');
$tiposDocumentos = $util->DB()->GetResult();
echo 'Inicio ejecucion : '.date('Y-m-d H:i:s',time()).chr(10).chr(13);
foreach($employees as $key=>$itemEmploye) {
    $persons = array();
    $contracts =  array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados();
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    array_unshift($persons, $itemEmploye['personalId']);
    $contracts = $contractRep->SearchOnlyContract($persons,true);
    foreach($contracts as $kc=>$itemContract){
       $documento->setContractId($itemContract['contractId']);
       $documentos = $documento->GetDocContract($itemContract,$itemEmploye['departamentoId']);
       if($documentos['noFiles']<=0 || $documentos['totalTipos']<=0)
       {
           unset($contracts[$kc]);
           continue;
       }
       $resPermisos = explode('-', $itemContract['permisos']);
        foreach ($resPermisos as $res) {
            $value = explode(',', $res);
            $idPersonal = $value[1];
            $idDepto = $value[0];
            if($itemEmploye['departamentoId']==$idDepto){
                $personal->setPersonalId($idPersonal);
                $nomPers = $personal->GetNameById();
                break;
            }
        }
       $contracts[$kc]['responsableArea'] = $nomPers;
       $contracts[$kc]['documentos'] = $documentos['docs'];
    }
    if(count($contracts)<=0)
        continue;

    $departamentos->setDepartamentoId($itemEmploye['departamentoId']);
    $depto  =  $departamentos->GetNameById();
    $smarty->assign('depto',$depto);
    $smarty->assign('tiposDocumentos',$tiposDocumentos);
    $smarty->assign('contracts',$contracts);
    $smarty->assign('personal',strtoupper($itemEmploye['name']));
    $contents = $smarty->fetch(DOC_ROOT.'/templates/lists/report-docpermanente.tpl');

    $html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						vertical-align: center;
						border-collapse: collapse;
					}
					.greenBox {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background:#00dd00;
						color: #FFFFFF;
						vertical-align: center;
						
					}
					.redBox {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background:#F00;
						color: #FFFFFF;
						vertical-align: center;
						
					}
					.grayBox {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background:#ffffff;
						color: #000000;
						vertical-align: center;
						
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:14px;
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
    $html .=$contents;
    $file = strtoupper(substr($depto,0,2)).'-DOCSPENDIENTES-'.strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId'];
    $excel->ConvertToExcel($html, 'xlsx', false,$file,true);

    $subject= $file;
    $body   = "ESTIMADO USUARIO : SE HACE LLEGAR EL REPORTE DE DOCUMENTOS PENDIENTES POR SUBIR A PLATAFORMA DE LAS RAZONES SOCIALES QUE ESTAN BAJO SU RESPONSABILIDAD
               DIRECTA O INDIRECTAMENTE MEDIANTE SUS SUBORDINADOS.
          <br><br>
               Este correo se genero automaticamente favor de no responder";
    if(REP_STATUS=='test')
        $to = array(EMAIL_DEV=>'Desarrollador');
    else
        $to = array($itemEmploye["email"]=>$itemEmploye['name'],EMAIL_DEV=>'Desarrollador');

    $toName = $itemEmploye["name"];
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";
    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ENVIOS AUTOMATICOS") ;
    unlink($attachment);
    echo "REPORTE DE DOCUMENTOS PENDIENTES POR SUBIR ENVIADO A : ".$itemEmploye['email'].chr(10).chr(13);

}
echo 'Final ejecucion : '.date('Y-m-d H:i:s',time()).chr(10).chr(13);
