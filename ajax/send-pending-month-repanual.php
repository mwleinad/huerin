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
foreach($employees as $key=>$itemEmploye){
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados();
    $persons = $util->ConvertToLineal($subordinados,'personalId');
    array_push($persons,$itemEmploye['personalId']);
    $formValues['persons'] = $persons;
    $formValues['atrasados'] = 1;
    $contracts =  $contract->BuscarContractV2($formValues,true);
    if(empty($contracts))
        continue;

    $idClientes = array();
    $idContracts = array();
    $contratosClte = array();
    foreach($contracts as $res){
        $contractId = $res['contractId'];
        $customerId = $res['customerId'];
        if(!in_array($customerId,$idClientes))
            $idClientes[] = $customerId;
        if(!in_array($contractId,$idContracts)){
            $idContracts[] = $contractId;
            $contratosClte[$customerId][] = $res;
        }

    }//foreach
    $clientes = array();
    //	print_r($idClientes);
    //	print_r($idContracts);
    //	print_r($contratosClte);
    foreach($idClientes as $customerId){
        $customer->setCustomerId($customerId);
        $infC = $customer->Info();
        $infC['contracts'] = $contratosClte[$customerId];
        $clientes[] = $infC;

    }//foreach
    $resClientes = array();
    $clteAtrasados =  array();
    $keyClteAtrasados = 0;
    foreach($clientes as $clte){

        $contratos = array();
        $contratosAtrasados=array();
        $keyContractAtrasados = 0;
        foreach($clte['contracts'] as $con){

            //Checamos Permisos
            $resPermisos = explode('-',$con['permisos']);
            foreach($resPermisos as $res){
                $value = explode(',',$res);

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
            foreach($con['servicios'] as $serv){

                $servicio->setServicioId($serv['servicioId']);
                $infServ = $servicio->Info();

                $noCompletados = 0;
                for($ii = 1; $ii <= 12; $ii++){
                    $statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

                    $month = date("m");
                    if($ii < $month){
                        if($statusColor["class"] == "PorIniciar" || $statusColor["class"] == "Iniciado"||$statusColor["class"] == "PorCompletar")
                        {
                            $noCompletados++;
                        }
                    }

                    //Si es Servicio de Domicilio Fiscal, que no lleve colores
                    if($statusColor['tipoServicioId'] == 16)
                        $statusColor['class'] = '';

                    if($statusColor['tipoServicioId'] == 34)
                        $statusColor['class'] = '';

                    if($statusColor['tipoServicioId'] == 24)
                        $statusColor['class'] = '';

                    if($statusColor['tipoServicioId'] == 37)
                        $statusColor['class'] = '';

                    $serv['instancias'][$ii] = $statusColor;
                }

                $tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
                $deptoId = $tipoServicio->GetField('departamentoId');

                $serv['responsable'] = $permisos[$deptoId];

                if($formValues['atrasados'])
                {
                    if($noCompletados > 0)
                    {
                        $serviciosAtrasados[$keyServAtrasado] = $serv;
                        $keyServAtrasado++;
                        $servicios[] = $serv;
                    }
                }
                else
                {
                    $servicios[] = $serv;
                }

            }//foreach
            $con['instanciasServicio'] = $servicios;
            $con['instanciasServicioAtrasados'] = $serviciosAtrasados;
            $contratos[] = $con;
            if(!empty($serviciosAtrasados))
            {
                $contratosAtrasados[$keyContractAtrasados] = $con;
                $keyContractAtrasados++;
            }

        }//foreach
        $clte['contracts'] = $contratos;
        $resClientes[] = $clte;
        if(!empty($contratosAtrasados))
        {
            $cltA['contracts'] = $contratosAtrasados;
            $clteAtrasados[$keyClteAtrasados] = $cltA;
            $keyClteAtrasados++;
        }

    }//foreach
    $cleanedArray = array();
    foreach($resClientes as $key => $cliente)
    {
        foreach($cliente["contracts"] as $keyContract => $contract)
        {
            foreach($contract["instanciasServicio"] as $keyServicio => $servicio)
            {
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

    $cleanedArrayAtrasados = array();
    foreach($clteAtrasados as $key => $clientea)
    {
        foreach($clientea["contracts"] as $keyContracta => $contracta)
        {
            foreach($contracta["instanciasServicioAtrasados"] as $keyServicioa => $servicioa)
            {
                $card2["comentario"] = $servicioa["comentario"];
                $card2["servicioId"] = $servicioa["servicioId"];
                $card2["nameContact"] = $clientea["nameContact"];
                $card2["tipoPersonal"] = $servicioa["responsable"]["tipoPersonal"];
                $card2["responsable"] = $servicioa["responsable"]["name"];
                $card2["name"] = $contracta["name"];
                $card2["instanciasServicio"] = $servicioa["instancias"];;
                $card2["nombreServicio"] = $servicioa["nombreServicio"];;
                $cleanedArrayAtrasados[] = $card2;
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
    $smarty->assign("cleanedArray", $cleanedArray);
    $contents = $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio.tpl');
    $html = $contents;
    $html = str_replace('$','', $html);
    $html = str_replace(',','', $html);
    $fileName = $itemEmploye['personalId']."REPORTE-AL-".date('Y-m-d');
    $excel->ConvertToExcel($html, 'xlsx',false,$fileName);

    $smarty->assign("cleanedArray", $cleanedArrayAtrasados);
    $contents2 = $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio.tpl');
    $html2 = $contents2;
    $html2 = str_replace('$','', $html2);
    $html2 = str_replace(',','', $html2);
    $fileName2 = 'ATRASADOS'.$itemEmploye['personalId']."REPORTE-AL-".date('Y-m-d');
    $excel->ConvertToExcel($html2, 'xlsx',false,$fileName2);

exit;
}