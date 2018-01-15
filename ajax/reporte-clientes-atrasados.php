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

$inicioFin = $util->inicio_fin_semana(date('Y-m-d'));

$sql = "SELECT * FROM personal WHERE tipoPersonal NOT IN('socio','asistente') 
        AND (lastSendEmail NOT BETWEEN '".$inicioFin['fechaInicio']."' AND '".$inicioFin['fechaFin']."' 
        OR lastSendEmail IS NULL) ORDER BY personalId ASC LIMIT 5";
$db->setQuery($sql);
$employees = $db->GetResult($sql);

$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
					WHERE class = ''";
$db->setQuery($sql);
$db->UpdateData();

$year = '2017';

$arrayBase =  array();
foreach($employees as $key=>$itemEmploye) {
        if(trim($itemEmploye['email']=='.'))
            continue;

        $persons = array();
        $personal->setPersonalId($itemEmploye['personalId']);
        $personal->setPersonalId($itemEmploye['personalId']);
        $subordinados = $personal->Subordinados();
        $persons = $util->ConvertToLineal($subordinados, 'personalId');
        array_unshift($persons, $itemEmploye['personalId']);
        $contracts = $contract->BuscarContractV2($persons,true);
        if(empty($contracts))
        {
            ECHO $up = 'UPDATE personal SET lastSendEmail=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
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
                    $temp = $instanciaServicio->getOnlyAtrasados($serv['servicioId']);
                    $serv['instancias'] = $temp;
                    $tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
                    $deptoId = $tipoServicio->GetField('departamentoId');
                    $serv['responsable'] = $permisos[$deptoId];
                    if(!empty($temp))
                    $servicios[] = $serv;
                    else
                        continue;
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
        foreach ($resClientes as $key => $custItem) {
            foreach ($custItem["contracts"] as $keyContract => $conta) {
                foreach ($conta["instanciasServicio"] as $keyServicio => $serva) {
                    $card["comentario"] = $serva["comentario"];
                    $card["servicioId"] = $serva["servicioId"];
                    $card["nameContact"] = $custItem["nameContact"];
                    $card["tipoPersonal"] = $serva["responsable"]["tipoPersonal"];
                    $card["responsable"] = $serva["responsable"]["name"];
                    $card["name"] = $conta["name"];
                    $card["instanciasServicio"] = $serva["instancias"];;
                    $card["nombreServicio"] = $serva["nombreServicio"];;
                    $cleanedArray[] = $card;
                }
            }
        }
        $last = $util->getLastDayMonth(date('Y'), date('m'));
        $smarty->assign("cleanedArray", $cleanedArray);
        $contents2 = $smarty->fetch(DOC_ROOT . '/templates/lists/report-clientes-atrasados.tpl');
        $html2 = $contents2;
        $html2 = str_replace('$', '', $html2);
        $html2 = str_replace(',', '', $html2);
        $fileNameDay = "REPORTE-CA -" .$itemEmploye['name']."(".date('Y-m-d').")";
        $excel->ConvertToExcel($html2, 'xlsx', false, $fileNameDay);

        $subject = "REPORTE SEMANAL";
        $body = "ESTIMADO USUARIO: A CONTINUACION SE LE HACE LLEGAR EL REPORTE DE CLIENTES ATRASADOS ASIGNADOS A SU CUENTA Y A LA DE SUS SUBORDINADOS (ES TO ES UN PRUEBA HACER CASO OMISO)
        <br>
        <br>
        <br>
        Este correo se genero automaticamente favor de no responder. ";
        $sendmail = new SendMail;

        $to = $itemEmploye["email"];
        $toName = $itemEmploye["name"];
        $attachment = DOC_ROOT . "/sendFiles/".$fileNameDay.".xlsx";

        $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileNameDay.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ENVIOS AUTOMATICOS") ;
        $up = 'UPDATE personal SET lastSendEmail=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
        $db->setQuery($up);
        $db->UpdateData();
        unlink($attachment);
}