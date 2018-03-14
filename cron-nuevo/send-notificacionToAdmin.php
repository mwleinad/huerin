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

    //encontrar las razones con adeudo
    $razonesAdeudo = array();
    foreach($contracts as $kc => $vc){
        //obtener comprobantes de la razon social
        $db->setQuery("SELECT CONCAT_WS('',a.serie,a.folio) as folioSerie,a.total,a.fecha,SUM(b.amount) as totalPagos FROM comprobante a 
               LEFT JOIN payment b ON a.comprobanteId=b.comprobanteId WHERE a.status='1' AND a.userId='".$vc['contractId']."' AND tiposComprobanteId IN(1,3,4)
               AND DATE(a.fecha)>'".INICIO_ADEUDO."' AND DATE(a.fecha)<(CURDATE() - INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY) GROUP BY b.comprobanteId ORDER BY a.fecha ASC");
        //echo $db->getQuery();
        $comprobantes = $db->GetResult();

        if(empty($comprobantes))
            continue;
        $pendientes = array();
        $totalAdeudo = 0;
        foreach($comprobantes as $kcom=>$compro){
            $resta = $compro['total']-$compro['totalPagos'];
            if($resta>0.1){
                $compro['pendiente'] = $resta;
                $totalAdeudo = $totalAdeudo+$compro['pendiente'];
                $pendientes[]=$compro;
            }
        }
        if(empty($pendientes))
            continue;


        $vc['numeroFactura'] = count($pendientes);
        $vc['montoTotal'] = number_format($totalAdeudo,2,".",",");
        $vc['factPendientes'] = $pendientes;
        $razonesAdeudo[] = $vc;
    }
    if(empty($razonesAdeudo))
        continue;
     //dd($razonesAdeudo);
     //exit;
    $departamentos->setDepartamentoId($itemEmploye['departamentoId']);
    $depto =  $departamentos->GetNameById();

    $smarty->assign("contracts", $razonesAdeudo);
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html = $smarty->fetch(DOC_ROOT.'/templates/lists/list-razonWhitAdeudo.tpl');
    $html = str_replace(',', '', $html);
    $file = strtoupper(substr($depto,0,2))."-RAZONESCONADEUDO-".trim(strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId']);
    $excel->ConvertToExcel($html, 'xlsx', false, $file,true,500);
    $subject= $file;
    $body   = "<pre><p>Estimado usuario: Adjunto a este correo encontrara un archivo excel el cual contiene la lista de clientes que cuentan con facturas</p>
               <p>pendiente por liquidar, emitidas a partir de la fecha ".INICIO_ADEUDO." hasta el mes anterior al actual. es decir las facturas emitidas</p>
               <p>del mes en curso no es tomado en cuenta.</p> <br><br>
               
               Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;
    if(REP_STATUS=='test')
        $to = array(EMAIL_DEV=>'Desarrollador');
    else
        $to = array($itemEmploye['email']=>$itemEmploye['name'],EMAIL_DEV=>'Desarrollador');
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";
    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "COBRANZA ADMIN") ;
    echo "reporte enviado correctamente"."\n";
    unlink($attachment);
}