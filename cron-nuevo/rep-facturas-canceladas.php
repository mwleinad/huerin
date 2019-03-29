<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 30/01/2018
 * Time: 11:12 PM
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

$sql = "SELECT CONCAT('',a.serie,a.folio) as folio,date(a.fecha) as fecha,b.name as razon,c.nameContact as cliente,a.total as monto,a.motivoCancelacion,a.fechaPedimento FROM comprobante a 
        LEFT JOIN contract b ON a.userId=b.contractId 
        LEFT JOIN customer c ON b.customerId=c.customerId
        WHERE a.empresaId=21 AND date(a.fechaPedimento)>=DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY
        AND date(a.fechaPedimento) <= LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) AND a.status='0' AND a.tipoDeComprobante IN(1,3,4) ORDER BY  a.fecha DESC ";
$db->setQuery($sql);
$invoices = $db->GetResult($sql);
$db->setQuery('SELECT DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY from comprobante where 1 LIMIT 1');
$initMonth = $db->GetSingle();
$des = explode('-',$initMonth);
$mes = (int)$des[1];

//crear el zip que enviara todos los xmls

$zip =  new ZipArchive();
$file_name = 'XMLS-'.strtoupper($util->GetMonthByKey($mes)).'.ZIP';
$file_zip =DOC_ROOT.'/sendFiles/'.$file_name;
if($zip->open($file_zip,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)===true) {
    echo 'Creado en' . $file_zip . '\n';
    foreach ($payments as $kp => $vp) {
        $archivo_xml = "SIGN_" . $vp['empresaId'] . '_' . $vp['serief'] . '_' . $vp['foliof'] . '.xml';
        $enlace_xml = DOC_ROOT . '/empresas/' . $vp['empresaId'] . '/certificados/' . $vp['rfcId'] . '/facturas/xml/' . $archivo_xml;
        if (file_exists($enlace_xml)) {
            $zip->addFile($enlace_xml, 'XML-CFDI/'.$archivo_xml);
        }
        //comprobar si hay complementos, tambien se adjunta en el zip
        if($vp['comprobantePagoId']>0){
            $db->setQuery("SELECT folio,serie,empresaId,rfcId FROM comprobante WHERE comprobanteId='".$vp['comprobantePagoId']."'");
            $complemento = $db->GetRow();
            if(!empty($complemento)){
                $archivo_xml_comp = "SIGN_" . $complemento['empresaId'] . '_' . $complemento['serie'] . '_' . $complemento['folio'] . '.xml';
                $enlace_xml_comp = DOC_ROOT . '/empresas/' . $complemento['empresaId'] . '/certificados/' . $complemento['rfcId'] . '/facturas/xml/' . $archivo_xml_comp;
                if (file_exists($enlace_xml_comp))
                    $zip->addFile($enlace_xml_comp, 'XML-COMP/'.$archivo_xml_comp);
            }

        }
    }
    $zip->close();
}
else {
    echo 'Error creando en'.$file_zip.'\n';
}
$html = '<html>
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
						vertical-align: center;
						border-collapse: collapse;
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #686DAB;
						background: #686DAB;
						color: #FFFFFF;
						vertical-align: center;
						text-align: center;
						height: 50px;
					}
				</style>
			</head>
			';
$smarty->assign("registros", $invoices);
$smarty->assign('mes',$util->GetMonthByKey($mes));
$smarty->assign('anio',date('Y'));
$contents = $smarty->fetch(DOC_ROOT . '/templates/lists/rep-facturas-canceladas.tpl');
$html .= $contents;
//$html = str_replace('$', '', $html);
$html = str_replace(',', '', $html);

$file = 'BHSC FACTURAS-CANCELADAS-'.strtoupper($util->GetMonthByKey($mes));
$excel->ConvertToExcel($html, 'xlsx', false,$file,true);
$subject= $file;
$body   = " SE HACE LLEGAR EL REPORTE DE FACTURA CANCELADA DEL MES DE ".strtoupper($util->GetMonthByKey($mes))." DEL AÑO ".date('Y')." 
          <br><br>
          Este correo se genero automaticamente favor de no responder";
$sendmail = new SendMail;

if(REP_STATUS=='test')
    $to = array(EMAIL_DEV=>'Desarrollador');
else
    $to = array('rzetina@braunhuerin.com.mx'=>'ROGELIO ZETINA',EMAIL_DEV=>'Desarrollador');

$attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";
$sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", $file_zip, $file_name,'noreply@braunhuerin.com.mx' , "FACTURACION PLATAFORMA") ;
echo "reporte enviado correctamente".chr(13).chr(10);
unlink($attachment);
