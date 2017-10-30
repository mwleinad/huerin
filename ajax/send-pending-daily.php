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
$year = '2017';
foreach($employees as $key=>$itemEmploye){
    if($itemEmploye['lastSendEmail']<date('Y-m-d')){
        $formValues['subordinados'] = 1;
        $formValues['respCuenta'] = $itemEmploye['personalId'];
        $formValues['departamentoId'] = '';
        $formValues['cliente'] = '';
        $formValues['atrasados'] = 1;
        $sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
					WHERE class = ''";
        $db->setQuery($sql);
        $db->UpdateData();
        $contracts = $contract->BuscarContract($formValues, true);

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
        foreach($clientes as $clte){

            $contratos = array();
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
                foreach($con['servicios'] as $serv){

                    $servicio->setServicioId($serv['servicioId']);
                    $infServ = $servicio->Info();

                    $noCompletados = 0;
                    for($ii = 1; $ii <= 12; $ii++){
                        $statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

                        $month = date("m");
                        if($ii < $month){
                            if($statusColor["class"] == "PorIniciar" || $statusColor["class"] == "Iniciado")
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
                            $servicios[] = $serv;
                        }
                    }
                    else
                    {
                        $servicios[] = $serv;
                    }

                }//foreach
                $con['instanciasServicio'] = $servicios;

                $contratos[] = $con;

            }//foreach
            $clte['contracts'] = $contratos;

            $resClientes[] = $clte;

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
        $personalOrdenado = $personal->ArrayOrdenadoPersonal();
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
        }
        $smarty->assign("cleanedArray", $sortedArray);
        $contents = $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio.tpl');



        $html = $contents;
        $html = str_replace('$','', $html);
        $html = str_replace(',','', $html);
        $fileName = $itemEmploye['personalId']."REPORTE-AL-".date('Y-m-d');
        $excel->ConvertToExcel($html, 'xlsx',false,$fileName);

        $subject = "REPORTE DE PENDIENTES AL  ".date('Y-m-d');
        $body = "SE ENVIA REPORTE DE CLIENTES.";
        $sendmail = new SendMail;

        $to = $itemEmploye["email"];
        $toName = $itemEmploye["name"];
        $attachment = DOC_ROOT."/sendFiles/".$fileName.".xlsx";

        $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName, $attachment2, $fileName2,'' , "ENVIOS AUTOMATICOS") ;
        $up = 'UPDATE personal SET lastSendEmail=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
        $db->setQuery($up);
        $db->UpdateData();
        exit;
    }

}
?>