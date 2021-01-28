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

$sql = "SELECT date(log.fecha) as fecham,log.*,personal.name as usuario FROM log 
        LEFT JOIN personal ON log.personalId=personal.personalId  
        WHERE date(log.fecha)>=DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY
        AND date(log.fecha) <= LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ORDER BY  log.fecha DESC ";
$db->setQuery($sql);
$logs = $db->GetResult($sql);
$bitacora= array();
$mes='';

foreach($logs as $key=>$value) {
    if($value['action']=="")
        continue;
    $tipo = "";
    switch ($value['action']) {
        case 'Insert':
            $tipo = 'Alta';
            if($value['tabla']=='contract')
                $descripcion = 'Alta de una razon social o contrato';
            elseif($value['tabla']=='servicio')
                $descripcion = 'Se agrega un servicio al cliente';

            $showChnages= false;
            break;
        case 'Update':
            $tipo = 'Modificacion';
            if($value['tabla']=='contract')
                $descripcion = 'Actualizacion de informacion del cliente';
            elseif($value['tabla']=='servicio')
                $descripcion = 'Actualizacion de un servicio al cliente';
            $showChnages = true;
            break;
        case 'Delete':
            $tipo = 'Eliminacion';
            if($value['tabla']=='contract')
                $descripcion = 'Eliminacion de una razon social';
            elseif($value['tabla']=='servicio')
                $descripcion = 'Eliminacion de un servicio del cliente';
            $showChnages  = false;
            break;
        case 'Reactivacion':
            $tipo ='Reactivacion';
            if($value['tabla']=='contract')
                $descripcion = 'Reactivacion de una razon social del cliente';
            elseif($value['tabla']=='servicio')
                $descripcion = 'Reactivacion de servicio del cliente';
            $showChnages=false;
            break;
        case 'Baja':
            $tipo ='Baja';
            if($value['tabla']=='contract')
                $descripcion = 'Baja de una razon social del cliente';
            elseif($value['tabla']=='servicio')
                $descripcion = 'Baja de servicio asignado al cliente';
            $showChnages = false;
        break;
        default:
            $tipo =$value['action'];
            $showChnages = false;
        break;
    }

    $card =  $value;
    $card['tipo'] = $tipo;
    $newValue = unserialize($value['newValue']);
    $oldValue = unserialize($value['oldValue']);
    if(empty($newValue))
        $newValue =  array();
    if(empty($oldValue))
        $oldValue =  array();
    switch($value['tabla']){
        case 'contract':
            $sql = 'SELECT contract.permisos,customer.nameContact,contract.name FROM contract
                    LEFT JOIN customer ON contract.customerId=customer.customerId
                    WHERE contract.contractId='.$value["tablaId"].' ';
            $db->setQuery($sql);
            $contrato = $db->GetRow();
            $permisos = preg_split("/-/",$contrato['permisos']);
            foreach($permisos as $pm){
                $split = explode(',',$pm);
                if($split[0] == 1) {
                    $personal->setPersonalId($split[1]);
                    $resposable = $personal->Info();
                }
            }

            $card['name'] = $contrato['name'];
            $card['nameContact'] = $contrato['nameContact'];
            $card['respContabilidad'] = $resposable['name'];
            $card['servicio'] = 'NA';

            $old=array();
            $new = array();
            if($showChnages){
                foreach($oldValue as $keyo =>$valueo)
                {
                    if($valueo!=$newValue[$keyo])
                    {
                        $old[$keyo]=$valueo;
                        $new[$keyo]=$newValue[$keyo];
                    }
                }
                if(empty($old))
                    $descripcion = 'Edicion de registro sin modificar informacion';

                $card['oldValue']=$old;
                $card['newValue']=$new;

            }else{
                $card['oldValue']=array();
                $card['newValue']=array();
            }

        break;
        case 'servicio':
            $sql =  'SELECT contract.permisos,customer.nameContact,contract.name FROM  servicio
                     LEFT JOIN contract ON servicio.contractId=contract.contractId
                     LEFT JOIN customer ON contract.customerId=customer.customerId
                     WHERE servicioId='.$value["tablaId"].'
                    ';
            $db->setQuery($sql);
            $contrato = $db->GetRow();
            $permisos = preg_split("/-/",$contrato['permisos']);
            foreach($permisos as $pm){
                $split = explode(',',$pm);
                if($split[0] == 1) {
                    $personal->setPersonalId($split[1]);
                    $resposable = $personal->Info();
                }
            }
            $servicio->setServicioId($value['tablaId']);
            $serv = $servicio->Info();
            $card['name'] = $contrato['name'];
            $card['nameContact'] = $contrato['nameContact'];
            $card['respContabilidad'] = $resposable['name'];
            $card['servicio'] = $serv['nombreServicio'];

            $old=array();
            $new = array();
            if($showChnages){
                foreach($oldValue as $keyo =>$valueo)
                {
                    if($valueo!=$newValue[$keyo])
                    {
                        $old[$keyo]=$valueo;
                        $new[$keyo]=$newValue[$keyo];
                    }
                }
                if(empty($old))
                    $descripcion = 'Edicion de registro sin modificar informacion';
                $card['oldValue']=$old;
                $card['newValue']=$new;
            }else{
                $card['oldValue']=array();
                $card['newValue']=array();
            }


        break;

    }
    $card['descripcion'] = $descripcion;
    $bitacora[] = $card;
    $mes = date('m',$value['fecham']);
}
$smarty->assign("mes", $mes);
$smarty->assign("registros", $bitacora);
$contents = $smarty->fetch(DOC_ROOT . '/templates/lists/report-bitacora.tpl');
$html = $contents;
$html = str_replace('$', '', $html);
$html = str_replace(',', '', $html);
$file = 'BITACORA-'.strtoupper($util->GetMonthByKey($mes));
$excel->ConvertToExcel($html, 'xlsx', false,$file);

$subject= $file;
$body   = " SE HACE LLEGAR EL REPORTE DE BITACORA CORRESPONDIENTE AL MES DE ".strtoupper($util->GetMonthByKey($mes))."
          <br><br>
          Este correo se genero automaticamente favor de no responder";
$sendmail = new SendMail;

$to = 'jhuerin@braunhuerin.com.mx';
$toName = 'JACOBO EDUARDO HUERIN ROMANO';
$attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";

$sendmail->Prepare($subject, $body, $to, $toName, $attachment, $file.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "BITACORA") ;
echo "reporte bitacora enviado";
unlink($attachment);
