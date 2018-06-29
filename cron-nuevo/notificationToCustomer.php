<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 14/06/2018
 * Time: 02:31 PM
 * Este cron envia notificacion a la razon social del cliente  para nofiticarle el estado de sus servicios
 * Si esta al corriente enviarle notificacion positiva
 * Si tiene pendientes por cumplir enviarle una notificacion  negativa anexando en un archivo los meses en el cual tiene pendientes.
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

$db->setQuery('SET SESSION group_concat_max_len = 10240');
$db->ExecuteQuery();
$db->CleanQuery();
$sql ="SELECT a.servicioId,e.nombreServicio,c.contractId,c.name as razon,GROUP_CONCAT(DISTINCT a.date) as meses  FROM instanciaServicio a 
       INNER JOIN servicio b ON a.servicioId=b.servicioId AND b.status='activo'
       INNER JOIN contract c ON b.contractId=c.contractId AND c.activo='Si'
       INNER JOIN customer d ON c.customerId=d.customerId AND d.active='1'
       INNER JOIN tipoServicio e ON b.tipoServicioId=e.tipoServicioId AND e.status='1' AND e.periodicidad!='Eventual'
       WHERE NOT EXISTS(SELECT instanciaServicioId FROM instanciaServicio WHERE servicioId=a.servicioId AND status IN('activa','completa') AND class IN('Completo','CompletoTardio') AND date=a.date LIMIT 1 )
       AND a.class IN('PorIniciar','PorCompletar') AND a.date>='2017-01-01' AND a.date<=(LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -2 MONTH)))  GROUP BY a.servicioId ";
$db->setQuery($sql);
$result = $db->GetResult();
$contratos=array();
$idContracts=array();
$timeStart = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);
foreach($result as $key => $value){
    $meses=array();
  //comprobar que los clientes
    $contratoId = $value['contractId'];
    $servicioId = $value['servicioId'];
    if(!in_array($contratoId,$idContracts))
        array_push($idContracts,$contratoId);

    $contratos[$contratoId]["razon"]=$value['razon'];
    $contratos[$contratoId]["contractId"]=$value['contractId'];
    $contratos[$contratoId]['servicios'][$servicioId]['nombre'] = $value['nombreServicio'];
    $meses = explode(',',$value['meses']);
    rsort($meses);
    $contratos[$contratoId]['servicios'][$servicioId]['instancias'] = $meses;
}
$createPdf = new CreatePdfNotification();
$razon  = new Razon();
$mail = new SendMail();
$oe = array();
$enviarLog =false;
foreach($contratos as $kc=>$valc){
    $enviara = array();
    if($createPdf->CreateFileNotificationToContract($valc)){
        $nameFile = "pendientes_".$valc['contractId'].".pdf";
        $file=DOC_ROOT."/sendFiles/".$nameFile;
        //si se creo archivo enviar por correo
        $razon->setContractId($valc['contractId']);
        $mails = $razon->getEmailContractByArea('all');
        foreach($mails['allEmails'] as $key=>$correo){
            $enviara[$correo]=$mails['name'];
        }
        if(!empty($enviara))
        {
            $subjetc ="OPINION NEGATIVA DE SUS DECLARACIONES Y OPINIONES";
            $body ="<p>".$mails['name']."</p>";
            $body .="<p>Estimado cliente:</p>";
            $body .="<p>Le informamos que su contabilidad y declaraciones fiscales de acuerdo a nuestros controles y revision, tiene pendientes.</p>";
            $body .="<p>Revisar archivo adjunto para mas informacion.</p><br>";
            $body .="<p>No responder a este correo, favor de dirigirse con el encargado de su cuenta, Gracias!!</p></div>";

            if($valc===end($contratos))
                $enviarLog=true;

            $mail->PrepareMultipleNotice($subjetc,$body,$enviara,'',$file,$nameFile,"","","noreply@braunhuerin.com.mx",'NOTIFICACION BRAUN&HUERIN',$enviarLog);
            unlink($file);
        }
    }
}
$sql1 ="SELECT a.servicioId,d.nombreServicio,c.contractId,c.name as razon  FROM instanciaServicio a 
       INNER JOIN servicio b ON a.servicioId=b.servicioId AND b.status='activo'
       INNER JOIN contract c ON b.contractId=c.contractId AND c.activo='Si'
       INNER JOIN customer e ON c.customerId=e.customerId AND e.active='1' 
       INNER JOIN tipoServicio d ON b.tipoServicioId=d.tipoServicioId AND d.status='1' AND d.periodicidad!='Eventual'
       WHERE b.contractId NOT IN(".implode(',',$idContracts).")
       AND a.class IN('Completo','CompletoTardio') AND a.date>='2017-01-01' AND a.date<=(LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)))  GROUP BY b.contractId ";
$db->setQuery($sql1);
$enregla = $db->GetResult();
if(!is_array($enregla))
    $enregla=array();

$enviarLog=false;
$body ="";
foreach($enregla as $ks=>$valsp){
    $enviara = array();
    $razon->setContractId($valsp['contractId']);
    $mails = $razon->getEmailContractByArea('all');
    if(empty($mails))
        continue;

    foreach($mails['allEmails'] as $key=>$correo){
        $enviara[$correo]=$mails['name'];
    }
    if(!empty($enviara))
    {
        $subjetc ="OPINION POSITIVA DE SUS DECLARACIONES Y OPINIONES";
        $body ="<p>".$mails['name']."</p>";
        $body .="<p>Estimado cliente:</p>";
        $body .="<p>Le informamos que su contabilidad y declaraciones fiscales de acuerdo a nuestros controles y revision, se encuentra al corriente.</p>";
        $body .="<p>No responder a este correo.</p></div>";

        if($valc===end($enregla))
            $enviarLog=true;

      $mail->PrepareMultipleNotice($subjetc,$body,$enviara,'',$file,$nameFile,"","","noreply@braunhuerin.com.mx",'NOTIFICACION BRAUN&HUERIN',$enviarLog);
    }

}
echo "total contratos con pendientes : ".count($contratos)."<br>";
echo "total contratos sin pendientes : ".count($enregla)."<br>";

$time = date("d-m-Y").' a las '.date('H:i:s');
echo  "Cron ejecutado desde ".$timeStart." hasta $time Hrs.".chr(13).chr(10);