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

$filtroOrden="Cliente";

$year=2017;
$sql = "SELECT * FROM personal WHERE tipoPersonal NOT IN('socio','asistente') 
          AND (lastSendBono < DATE(NOW()) OR lastSendBono IS NULL) ORDER BY personalId ASC LIMIT 3";
$db->setQuery($sql);
$employees = $db->GetResult($sql);

$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
					WHERE class = ''";
$db->setQuery($sql);
$db->UpdateData();
$mesesBase = array(0=>array(),1=>array(),2=>array());
$meses = array();
switch(date('m')){
    case 1:
    case 2:
    case 3:
        $meses = array(1,2,3);
        $trimestre = array('Enero','Febrero','Marzo');
        $tri = "PRIMER TRIMESTRE";
    break;
    case 4:
    case 5:
    case 6:
        $meses = array(4,5,6);
        $trimestre = array('Abril','Mayo','Junio');
        $tri = "SEGUNDO TRIMESTRE";
        break;
    case 7:
    case 8:
    case 9:
        $meses = array(7,8,9);
        $trimestre = array('Julio','Agosto','Septiembre');
        $tri = "TERCER TRIMESTRE";
        break;
    case 10:
    case 11:
    case 12:
        $meses = array(10,11,12);
        $trimestre = array('Octubre','Noviembre','Diciembre');
        $tri = "CUARTO TRIMESTRE";
        break;
}
foreach($employees as $key=>$itemEmploye){
    $persons = array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados();
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    array_unshift($persons, $itemEmploye['personalId']);
    $contracts = $contractRep->BuscarContractV2($persons,true);
    if(empty($contracts))
    {
        $up = 'UPDATE personal SET lastSendBono=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
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
                $nomPers = $personal->GetNameById();

                $permisos[$idDepto] = $nomPers;
                $permisos2[$idDepto] = $idPersonal;
            }
            $servicios = array();
            $serviciosAtrasados = array();
            $keyServAtrasado = 0;
            foreach ($con['servicios'] as $serv) {
                $servicio->setServicioId($serv['servicioId']);
                $infServ = $servicio->Info();
                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses);
                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);

                $tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
                $deptoId = $tipoServicio->GetField('departamentoId');
                $serv['responsable'] = $permisos[$deptoId];
                $serv['costo'] = $infServ['costo'];
                $serv['sumatotal'] = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses);
                $servicios[] = $serv;
            }//foreach
            $con['instanciasServicio'] = $servicios;
            $contratos[] = $con;

        }//foreach}
        $clte['contracts'] = $contratos;
        $resClientes[] = $clte;
    }//foreach
    $alfabeto = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,Ñ,O,P,Q,R,S,T,U,V,W,X,Y,Z";
    $abcdario = explode(",", $alfabeto);

    if (count($resClientes) > 0) {
        foreach ($abcdario as $keyLetra => $letra) {
            foreach ($resClientes as $key1 => $row1) {
                foreach ($resClientes[$key1]['contracts'] as $key2 => $row2) {
                    foreach ($resClientes[$key1]['contracts'][$key2]['instanciasServicio'] as $key3 => $row3) {
                        if ($filtroOrden == "C. Asignado") {
                            $letraInicialFiltro = strtoupper($resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['responsable'][0]);
                        }elseif ($filtroOrden == "Cliente") {
                            $letraInicialFiltro = strtoupper($resClientes[$key1]['nameContact'][0]);
                        }elseif ($filtroOrden == "Razon Social") {
                            $letraInicialFiltro = strtoupper($resClientes[$key1]['contracts'][$key2]['name'][0]);
                        }
                        if($letraInicialFiltro == $letra){
                            $resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['TIPO_ORDEN'] = $filtroOrden;
                            $resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['LETRA'] = $letra;
                            $resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['POCICION'] = $keyLetra;
                        }
                    }
                }
            }
        }
    }
    $html= '<html>
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
						border-collapse: collapse;
					}
				</style>
			</head>
			';
    $smarty->assign("abcdario", $abcdario);
    $smarty->assign("nombreMeses", $trimestre);
    $smarty->assign("clientes", $resClientes);
    $smarty->assign("NODIV", 'SI');
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html .= $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio-bono.tpl');

    $fileName = "BONOS-".$tri."-".$itemEmploye['personalId'];
    $excel->ConvertToExcel($html, 'xlsx', false, $fileName,true);

    $subject='REPORTE BONOS '.$tri;
    $body = "ESTIMADO USUARIO: A CONTINUACION SE LE HACE LLEGAR EL REPORTE DE BONOS CORRESPONDIENTE AL ".$tri." DEL &Ntilde; ".DATE('Y')."
        <br>
        <br>
        <br>
        Este correo se genero automaticamente favor de no responder. ";
    $sendmail = new SendMail;

    $to = $itemEmploye["email"];
    $toName = $itemEmploye["name"];
    $attachment = DOC_ROOT . "/sendFiles/".$fileName.".xlsx";

    $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ENVIOS AUTOMATICOS") ;
    $up = 'UPDATE personal SET lastSendBono=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
    $db->setQuery($up);
    $db->UpdateData();
    unlink($attachment);
}