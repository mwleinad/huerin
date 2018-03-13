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

$db->setQuery("SELECT * FROM contract WHERE activo='si' ORDER BY name ASC ");
$results = $db->GetResult();

$currentDate = date("Y-m-d");
$newdate = strtotime ( "-1 month" , strtotime ( $currentDate ) ) ;
$newdate = date ( 'Y-m-d' , $newdate );
$total=0;
foreach($results as $key=>$value) {
    $razon = new Razon;
    $razon->setContractId($value['contractId']);
    $correos = $razon->getEmailContractByArea('administracion', false);
    $body="";
    //obtener comprobantes de la razon social
    $db->setQuery("SELECT CONCAT_WS('',a.serie,a.folio) as folioSerie,a.total,a.fecha,SUM(b.amount) as totalPagos FROM comprobante a 
               LEFT JOIN payment b ON a.comprobanteId=b.comprobanteId WHERE a.status='1' AND a.userId='".$value['contractId']."' AND tiposComprobanteId IN(1,3,4)
               AND DATE(a.fecha)>'2018-01-01' AND DATE(a.fecha)<(CURDATE() - INTERVAL DAYOFMONTH(CURDATE()) - 1 DAY) GROUP BY b.comprobanteId ORDER BY a.userId ASC");
   // echo $db->getQuery();
    $comprobantes = $db->GetResult();


    if(empty($comprobantes))
        continue;

    $pendientes = array();
    foreach($comprobantes as $kcom=>$compro){
        if($compro['total']>$compro['totalPagos']){
          $pendientes[]=$compro;
        }
    }
    if(empty($pendientes))
        continue;

    $body .= "<pre><br><br>Estimado Cliente: ".$value["name"]."<br><br>";
    $body .= "A continuacion le enviamos una relaci&oacute;n de facturas que tenemos pendiente por liquidar : <br><br>";
    $body .="<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" >";
    $body .="<tr><td>Factura</td><td>Monto pendiente</td>
             </tr>";
    foreach($pendientes as $ky=>$cmp){
        $monto = $cmp['total']-$cmp['amount'];
        $body .="<tr>";
        $body .="<td>".$cmp["folioSerie"]."</td>";
        $body .="<td>$ ".number_format($monto, 2, ".", ",")."</td>";
        $body .="</tr>";
    }
    $body .="</table>";
    $body .="<br><br>Agradecemos nos proporcione su comprobante de pago lo antes posible, para conciliar.";
    $body .="<br><br>Gracias.";
    $emails = array();
    if(REP_STATUS=='test'){
        $emails=array(EMAIL_DEV=>'Desarrollador');
        $todoMail ="";
        foreach($correos['allEmails'] as $val){
            $todoMail .= ", ".$val;
        }
        $body .="<br><br>copia a ".$todoMail;
    }
    else{
        foreach($correos['allEmails'] as $val){
            $emails[$val] = utf8_decode($correos["name"]);
        }
    }

    $sendmail->PrepareMultiple('FACTURAS PENDIENTES',$body,$emails,'','','','','',FROM_MAIL,'COBRANZA B&H');
    echo 'Correo enviado.'.$value['contractId'].'\n';
    $total++;
}
echo "Total de clientes con adeudo = ".$total;