<?php
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

$sql = "SELECT * FROM personal WHERE tipoPersonal NOT IN('socio','asistente') ORDER BY personalId ASC";
$db->setQuery($sql);
$employees = $db->GetResult($sql);

$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
					WHERE class = ''";
$db->setQuery($sql);
$db->UpdateData();

$year = '2017';
$persons = array();
$arrayBase =  array();

for ($ii = 1; $ii <= 12; $ii++) {
   $arrayBase[$ii]=array();
}
foreach($employees as $key=>$itemEmploye) {
    if($itemEmploye['personalId']==53){
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados();

    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    array_push($persons, $itemEmploye['personalId']);
    $formValues['persons'] = $persons;
    $formValues['atrasados'] =  1;
    $contracts = $contract->BuscarContractV2($formValues, true);
    if (empty($contracts))
    {
        $up = 'UPDATE personal SET lastSendEmail=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
        $db->setQuery($up);
        $db->UpdateData();
        continue;
    }


    $idClientes = array();
    $idContracts = array();
    $contratosClte = array();
    foreach ($contracts as $res) {
        $contractId = $res['contractId'];
        $customerId = $res['customerId'];
        if (!in_array($customerId, $idClientes))
            $idClientes[] = $customerId;
        if (!in_array($contractId, $idContracts)) {
            $idContracts[] = $contractId;
            $contratosClte[$customerId][] = $res;
        }

    }//foreach
    $clientes = array();
    foreach ($idClientes as $customerId) {
        $customer->setCustomerId($customerId);
        $infC = $customer->Info();
        $infC['contracts'] = $contratosClte[$customerId];
        $clientes[] = $infC;

    }//foreach

    $resClientes = array();
    $clteAtrasados = array();
    $keyClteAtrasados = 0;
    foreach ($clientes as $clte) {
        $contratos = array();
        $contratosAtrasados = array();
        $keyContractAtrasados = 0;
        foreach ($clte['contracts'] as $con) {
            //Checamos Permisos
            $resPermisos = explode('-', $con['permisos']);
            foreach ($resPermisos as $res) {
                $value = explode(',', $res);

                $idPersonal = $value[1];
                $idDepto = $value[0];

                $personal->setPersonalId($idPersonal);
                $nomPers = $personal->GetDataReport();

                $permisos[$idDepto] = $nomPers;
                $permisos2[$idDepto] = $idPersonal;
            }

            //$personal->setPersonalId($con['responsableCuenta']);
            //$con['responsable'] = $personal->Info();
            $servicios = array();
            $serviciosAtrasados = array();
            $keyServAtrasado = 0;
            foreach ($con['servicios'] as $serv) {
                $servicio->setServicioId($serv['servicioId']);
                $infServ = $servicio->Info();

                $noCompletados = $instanciaServicio->getInstanciaAtrasado($serv['servicioId'],$year);
                $temp = $instanciaServicio->getInstanciaByServicio($serv['servicioId'],$year);
                $serv['instancias'] = array_replace_recursive($arrayBase,$temp);

                $tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
                $deptoId = $tipoServicio->GetField('departamentoId');
                $serv['responsable'] = $permisos[$deptoId];
                $servicios[] = $serv;

                /*if(!empty($noCompletados))
                    $servicios[] = $serv;
                else
                    continue ;*/
            }//foreach

            $con['instanciasServicio'] = $servicios;
            $contratos[] = $con;

        }//foreach}
        $clte['contracts'] = $contratos;
        $resClientes[] = $clte;
    }//foreach
    $cleanedArray = array();
    foreach ($resClientes as $key => $cliente) {
        foreach ($cliente["contracts"] as $keyContract => $contract) {
            foreach ($contract["instanciasServicio"] as $keyServicio => $servicio) {
                $card["comentario"] = $servicio["comentario"];
                $card["servicioId"] = $servicio["servicioId"];
                $card["nameContact"] = $cliente["nameContact"];
                $card["tipoPersonal"] = $servicio["responsable"]["tipoPersonal"];
                $card["responsable"] = $servicio["responsable"]["name"];
                $card["name"] = $contract["name"];
                $card["instanciasServicio"] = $servicio["instancias"];;
                $card["nombreServicio"] = $servicio["nombreServicio"];;
                $cleanedArray[] = $card;
            }
        }
    }
    /*$personalOrdenado = $personal->ArrayOrdenadoPersonal();
    $sortedArray = array();
    foreach($personalOrdenado as $personalKey => $personalValue)
    {
        foreach($cleanedArray as $keyCleaned => $cleanedArrayValue)
        {
            if($personalValue["name"] == $cleanedArrayValue["responsable"])
            {
                $sortedArray[] = $cleanedArrayValue;
                unset($cleanedArrayValue[$keyCleaned]);
            }
        }
    }*/
    $last = $util->getLastDayMonth(date('Y'), date('m'));

    $smarty->assign("cleanedArray", $cleanedArray);
    $contents2 = $smarty->fetch(DOC_ROOT . '/templates/lists/report-servicio.tpl');
    $html2 = $contents2;
    $html2 = str_replace('$', '', $html2);
    $html2 = str_replace(',', '', $html2);
    $fileNameDay = $itemEmploye['personalId'] . "REPORTE-ANUAL" . date('Y-m-d');
    $excel->ConvertToExcel($html2, 'xlsx', false, $fileNameDay);

    $subject = "REPORTE ANUAL DE CUENTAS";
    $body = "ESTIMADO USUARIO ESTE ES EL RESUMEN DE LAS CUENTAS QUE TIENE ASIGNADOS Y LA DE SUS SUBORDINADOS..(ES TO ES UN PRUEBA HACER CASO OMISO)";
    $sendmail = new SendMail;

    $to = $itemEmploye["email"];
    $toName = $itemEmploye["name"];
    $attachment = DOC_ROOT . "/sendFiles/".$fileNameDay.".xlsx";

    $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileNameDay.".xlsx", $attachment2, $fileName2,'' , "ENVIOS AUTOMATICOS") ;
    $up = 'UPDATE personal SET lastSendEmail=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
    $db->setQuery($up);
    $db->UpdateData();
    unlink($attachment);
    exit;
    }
}