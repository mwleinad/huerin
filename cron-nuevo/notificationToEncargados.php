<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 20/07/2018
 * Time: 12:05 PM
 * Cron para envio de notificacion a encargados de area
 * - se envia desde el encargado hasta el gerente
 * - se envia los servicios con las que cuenta atraso
 * - se desglosa por servicio los meses atrasados
 *
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



$createPdf = new CreatePdfNotification();
$mail = new SendMail();
//descomponer los permisos en una tabla para hacer consultas directas
$qp = "select personalId,email,name from personal where active='1' and personalId NOT IN(".IDHUERIN.",".IDBRAUN.",32)";
$db->setQuery($qp);
$contadores =  $db->GetResult();
$timeStart = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);
//CREATE VIEW INSTANCIASERVICIO WHERE DATE >=2017-01-01
$qs = "CREATE OR REPLACE VIEW instanciasTemp AS SELECT a.instanciaServicioId,a.date,a.servicioId,a.class,a.status FROM instanciaServicio a INNER JOIN servicio b ON a.servicioId=b.servicioId AND b.status='activo' WHERE a.status!='baja' AND a.date >='2017-01-01' group by a.date,a.servicioId ORDER BY date ASC " ;
$db->setQuery($qs);
$db->ExecuteQuery();
$idContracts = array();
$contractsSevices = array();

foreach($contadores as $key=>$value){
    //resetear array por cada contador.
    $contractsSevices=array();
    $idContracts = array();
    //obtener subordinados de cada empleaod si existe
    $personal->setPersonalId($value['personalId']);
    $subordinados = $personal->Subordinados();
    $personas = $util->ConvertToLineal($subordinados, 'personalId');
    array_unshift($personas, $value['personalId']);
    /*$subq ="SELECT a.name as razon,a.contractId FROM contract a INNER JOIN customer c ON a.customerId=c.customerId AND c.active='1' INNER JOIN contractPermiso b  ON a.contractId=b.contractId AND b.personalId IN(".implode(',',$personas).") WHERE a.activo='Si' GROUP BY b.contractId";
    $db->setQuery($subq);
    $contratos = $db->GetResult();*/
    $contratos = $contractRep->SearchOnlyContract($personas,true);
    if(empty($contratos))
        continue;

    $idContratos =  $util->ConvertToLineal($contratos,'contractId');
    //obtener servicios atrasados
    $qs = "SET lc_time_names = 'es_MX'";
    $db->setQuery($qs);
    $db->ExecuteQuery();
    $sql ="SELECT servicioId, contractId,nombreServicio,GROUP_CONCAT(meses separator '|')  as dtm,razon FROM (
          SELECT a.servicioId,d.contractId,c.nombreServicio,CONCAT(year(a.date),':',GROUP_CONCAT(DISTINCT MONTHNAME(a.date) ORDER BY date ASC)) as meses,d.name as razon  FROM instanciasTemp a 
          INNER JOIN servicio b ON a.servicioId=b.servicioId  and b.status='activo'
          INNER JOIN contract d ON b.contractId=d.contractId AND d.contractId IN(".implode(',',$idContratos).")
          INNER JOIN tipoServicio c ON b.tipoServicioId=c.tipoServicioId AND c.status='1' AND c.periodicidad!='Eventual'
          WHERE a.class IN('PorIniciar','PorCompletar','Iniciado') AND a.date<=(LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -2 MONTH))) GROUP BY a.servicioId,YEAR(a.date)  ORDER BY YEAR(a.date) DESC,MONTH(a.date)) tmpInstans GROUP BY servicioId";
    $db->setQuery($sql);
    $contracts = $db->GetResult();
    $count =0;
    foreach($contracts as $kc=>$vc){
        $contractId = $vc['contractId'];
        if(!in_array($contractId,$idContracts)){
            array_push($idContracts,$contractId);
            $contractsSevices[$contractId]['razon'] =$vc['razon'];
            $vc['dtm']=explode('|',$vc['dtm']);
            $contractsSevices[$contractId]["servicios"] .= $vc['nombreServicio'];;
            $complemento="";
            foreach($vc['dtm'] as $m){
                $itm = explode(':',$m);
                $complemento .='<p>'.$itm[0]."   |  ".$itm[1].'<p>';
            }
            $contractsSevices[$contractId]["servicios"] .= $complemento;
        }else{
            $vc['dtm']=explode('|',$vc['dtm']);
            $contractsSevices[$contractId]["servicios"] .= $vc['nombreServicio'];
            $complemento="";
            foreach($vc['dtm'] as $m){
                $itm = explode(':',$m);
                $complemento .='<p>'.$itm[0]."   |  ".$itm[1].'</p>';
            }
            $contractsSevices[$contractId]["servicios"] .= $complemento;
        }
        $count++;
    }
    if($createPdf->CreateFileNotificationToEncargados($contractsSevices,$value['personalId'])){
       $nameFile = "pendientes_".$value['personalId'].".pdf";
        $file=DOC_ROOT."/sendFiles/".$nameFile;
        $subjetc ="OPINION NEGATIVA DE  DECLARACIONES Y OPINIONES";
        $body ="<p>Estimado usuario:".$value['name']."</p>";
        $body .="<p>Le informamos que la contabilidad y declaraciones fiscales de los clientes presentes en el documento adjunto. Tiene pendientes.</p>";
        $body .="<p>Revisar archivo adjunto para mas informacion.</p><br>";
        $body .="<p>No responder a este correo,Gracias!!</p></div>";
        $enviara=array(EMAIL_DEV=>'correo1');
        $mail->PrepareMultipleNotice($subjetc,$body,$enviara,'',$file,$nameFile,"","","noreply@braunhuerin.com.mx",'NOTIFICACION PLATAFORMA');
        unlink($file);
    }
    break;
}
$time = date("d-m-Y").' a las '.date('H:i:s');
echo  "Cron ejecutado desde ".$timeStart." hasta $time Hrs.".chr(13).chr(10);