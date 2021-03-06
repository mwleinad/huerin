<?php
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
/*
 * Este cron envia a la razon social un correo solicitando la informacion para dar inicio a las actividades en la plafatorma
 * Condiciones:
 * El primer envio se realiza el 01 de cada mes y debe solicitar la informacion de un mes anterior es decir si se envia en julio debe solicitar la de junio
 * Se enviara solo si la razon tiene instancia creada en el mes corriente
 * Despues del primer envio reenviarlo cada 3 dias hasta que acabe el mes siempre y cuando tenga pendiente por cubrir el mes corriente
 *
 */
    $currentDate = date("Y-m-d");
    $newdate = strtotime ( '-1 month' , strtotime ( $currentDate ) ) ;
    $month = date ( 'm' , $newdate );
    $sendmail = new SendMail();
    $sql ="SELECT c.name,c.contractId,b.servicioId,c.permisos FROM instanciaServicio a 
        INNER JOIN servicio b ON a.servicioId=b.servicioId AND b.status='activo'
        INNER JOIN contract c ON c.contractId=b.contractId AND c.activo='Si'
        WHERE a.status='activa' AND a.class IN('PorIniciar')  AND MONTH(a.date)=MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH))  AND YEAR(a.date)=YEAR(DATE_ADD(CURDATE(),INTERVAL -1 MONTH))
        GROUP BY b.contractId ORDER BY c.NAME ASC";
    $db->setQuery($sql);
    $result  = $db->GetResult();
    if(empty($result)){
        $logBody ="<p>No se encontro razones sociales atrasadas en la recepcion de informacion correspondiente al mes de ".$util->GetMonthByKey((int)$month)." de ".date('Y')."</p>";
        $sendmail->Prepare('LOG SOLICITUD MENSUAL DE INFO',$logBody,EMAIL_DEV,'','','','','','noreply@braunhuerin.com.mx','CRON EMPTY');
        exit;
    }

    $razon = new Razon();
    $correosGeneral=array();
    $filtro = new ContractRep();
    $contratos = array();
    $count=1;
    foreach($result as $key=>$value){
        /*if(IDSUP){
            $personal->setPersonalId(IDSUP);
            $subordinados = $personal->Subordinados();
            $idSubordinados = $util->ConvertToLineal($subordinados, 'personalId');
            $continuar = $filtro->findPermission($value,$idSubordinados);
            if(!$continuar)
                continue;

            if(ITER_LIMIT){//se usa para pruebas limitar cantidad de contratos
                if($count>ITER_LIMIT)
                    break;
            }
        }
        array_push($contratos,$value['contractId']);*/
        $correos=array();
        $razon->setContractId($value['contractId']);
        $correos = $razon->getEmailContractByArea('administracion');
        if(is_array($correos['allEmails']))
            $correosGeneral = array_merge($correosGeneral,$correos['allEmails']);

        $razon->setContractId($value['contractId']);
        $correos = $razon->getEmailContractByArea('contabilidad');
        if(is_array($correos['allEmails']))
            $correosGeneral = array_merge($correosGeneral,$correos['allEmails']);

        $count++;
    }
    $correosGeneral = array_unique($correosGeneral);
    $correosFinal=array();
    if(!empty($correosGeneral)){
        foreach($correosGeneral as $var)
            $correosFinal[$var]='VARIOS';

        //preparar el cuerpo del mensajes
        $subject ="RECEPCION DE INFORMACION";
        $body .="<div style='width:550px;overflow-wrap: break-word; text-align: justify'>";
        $body .="<p>Buen dia estimados.</p>";
        $body .="<p>A efectos de iniciar nuestros trabajos de registro y proceso de su información financiera correspondientes al mes de ".$util->GetMonthByKey((int)$month)." de ".date('Y').", agradeceremos nos informen la fecha en que podemos contar con la documentación electrónica del mes (XML y PDF), tal como se señala en los archivos adjuntos, estas deben incluir los estados de cuenta bancarios originales.</p>";
        $body .="<p>Agradeciendo su apoyo, reciban un cordial saludo.</p>";
        $body .="<p>Cabe señalar que el envío de la documentación e información, debe ser dentro de los 5 primeros días de mes.</p>";
        $body .="<p>Agradeciendo su apoyo, reciban un cordial saludo.</p></br>";
        $body .="<p>No responder a este correo, favor de dirigirse con el encargado de su cuenta, Gracias!!</p></div>";

        $adjunto =DOC_ROOT."/REGLAS_DE_PAPELERIA.docx";
        $sendmail->PrepareMultipleNotice($subject,utf8_decode($body),$correosFinal,'',$adjunto,'REGLAS_DE_PAPELERIA.docx','','','noreply@braunhuerin.com.mx','BRAUN&HUERIN',true);
    }
