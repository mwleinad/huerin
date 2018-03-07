<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 02/03/2018
 * Time: 12:37 PM
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

echo 'Inicio ejecucion : '.date('Y-m-d H:i:s',time())."\n";
$sql = "SELECT * FROM personal WHERE (departamentoId=21 OR personalId=65) AND active='1' ORDER BY personalId ";
$db->setQuery($sql);
$employees = $db->GetResult($sql);
$year = date('Y');

$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
					WHERE class = ''";
$db->setQuery($sql);
$db->UpdateData();

foreach($employees as $key => $itemEmploye){
    $persons = array();
    $deptos =  array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    $deptos  = $util->ConvertToLineal($subordinados, 'dptoId');

    array_unshift($persons, $itemEmploye['personalId']);
    array_unshift($deptos, $itemEmploye['departamentoId']);
    $deptos = array_unique($deptos);
    $formValues['respCuenta'] =  $persons;
    $contracts = $contractRep->BuscarContract($formValues, true);
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
            $personal->setPersonalId($con['responsableCuenta']);
            $con['responsable'] = $personal->Info();

            $serv = array();
            $statusColor =  $workflow->GetStatusByComprobante($con['contractId'], $year);
            $con['instanciasServicio'] =$statusColor['serv'];

            if($formValues['atrasados'] && $statusColor['noComplete'] > 0)
            {
                $contratos[] = $con;
            }
            elseif(!$formValues['atrasados'])
            {
                $contratos[] = $con;
            }
        }//foreach
        $clte['contracts'] = $contratos;

        $resClientes[] = $clte;

    }//foreach

    $cleanedArray = array();
    foreach($resClientes as $key => $cliente)
    {
        foreach($cliente["contracts"] as $keyContract => $contract)
        {
            $card["comentario"] = $contract["comentario"];
            $card["contractId"] = $contract["contractId"];
            $card["nameContact"] = $cliente["nameContact"];
            $card["tipoPersonal"] = $contract["responsable"]["tipoPersonal"];
            $card["responsable"] = $contract["responsable"]["name"];
            $card["name"] = $contract["name"];
            $card["instanciasServicio"] = $contract["instanciasServicio"];;
            //$card["nombreServicio"] = $servicio["nombreServicio"];;
            $cleanedArray[] = $card;
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
    $departamentos->setDepartamentoId($itemEmploye['departamentoId']);
    $depto =  $departamentos->GetNameById();
    $file = strtoupper(substr($depto,0,2))."-REP-COBRANZA-".trim(strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId']);
    $smarty->assign("cleanedArray", $sortedArray);
    $smarty->assign("DOC_ROOT", DOC_ROOT);

    $html = $smarty->fetch(DOC_ROOT.'/templates/lists/report-cobranza-new.tpl');
    $excel->ConvertToExcel($html,'xlsx',false,$file,true,500);

    $subject= $file;
    $body   = "<pre>SE HACE LLEGAR EL REPORTE DE COBRANZA DE LAS RAZONES SOCIALES QUE TIENE A SU CARGO DE MANERA DIRECTA O INDIRECTA POR MEDIO DE SUS SUBORDINADOS.
                <br><br>Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;
    if(REP_STATUS=='test')
        $to = array(EMAIL_DEV=>'Desarrollador');
    else
        $to = array($itemEmploye["email"]=>$itemEmploye['name'],EMAIL_DEV=>'Desarrollador');

    $toName = $itemEmploye['name'];
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";

    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "REPORTE COBRANZA") ;
    unlink($attachment);
    echo "Reporte enviado a ".$itemEmploye['name']."\n";
}
echo 'Final ejecucion : '.date('Y-m-d H:i:s',time());
