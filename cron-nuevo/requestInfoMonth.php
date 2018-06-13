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
/*
 * Este cron envia a la razon social un correo solicitando la informacion para dar inicio a las actividades en la plafatorma
 * Condiciones:
 * El primer envio se realiza el 01 de cada mes
 * Se enviara solo si la razon tiene instancia creada en el mes corriente
 * Despues del primer envio reenviarlo cada 3 dias hasta que acabe el mes siempre y cuando tenga pendiente por cubrir el mes corriente
 *
 */
    $sendmail = new SendMail();
    $sql ="SELECT c.name,c.contractId,b.servicioId FROM instanciaServicio a 
        INNER JOIN servicio b ON a.servicioId=b.servicioId AND b.status='activo'
        INNER JOIN contract c ON c.contractId=b.contractId AND c.activo='Si'
        WHERE a.status='activa' AND a.class IN('PorIniciar')  AND MONTH(a.date)=".date('m')." AND YEAR(a.date)=".date('Y')."
        GROUP BY b.contractId ORDER BY c.NAME ASC";
    $db->setQuery($sql);
    $result  = $db->GetResult();
    if(empty($result)){
        $logBody ="<p>No se encontro razones sociales atrasadas en la recepcion de informacion correspondiente al mes de ".$util->GetMonthByKey(5)." de ".date('Y')."</p>";
        $sendmail->Prepare('LOG SOLICITUD MENSUAL DE INFO',$logBody,'avisos@braunhuerin.com.mx','','','','','','noreply@braunhuerin.com.mx','CRON EMPTY');
        exit;
    }

    $razon = new Razon();
    $correosGeneral=array();
    foreach($result as $key=>$value){
        $correos=array();
        $razon->setContractId($value['contractId']);
        $correos = $razon->getEmailContractByArea('administracion');
        if(is_array($correos['allEmails']))
            $correosGeneral = array_merge($correosGeneral,$correos['allEmails']);

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
        $body .="<p>A efectos de iniciar nuestros trabajos de registro y proceso de su información financiera correspondientes al mes de ".$util->GetMonthByKey(5)." de ".date('Y').", agradeceremos nos informen la fecha en que podemos contar con la documentación electrónica del mes (XML y PDF), tal como se señala en los archivos adjuntos, estas deben incluir los estados de cuenta bancarios originales.</p>";
        $body .="<p>Agradeciendo su apoyo, reciban un cordial saludo.</p>";
        $body .="<p>Cabe señalar que el envío de la documentación e información, debe ser dentro de los 5 primeros días de mes.</p>";
        $body .="<p>Agradeciendo su apoyo, reciban un cordial saludo.</p></div>";

        $adjunto =DOC_ROOT."/REGLAS_DE_PAPELERIA.docx";
        echo count($correosFinal);
        $correosFinal=array();
        $sendmail->PrepareMultipleNotice($subject,utf8_decode($body),$correosFinal,'',$adjunto,'REGLAS_DE_PAPELERIA.docx','','','noreply@braunhuerin.com.mx','BRAUN&HUERIN',true);
    }
