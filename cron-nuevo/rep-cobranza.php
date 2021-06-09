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
$docRoot = $_SERVER['DOCUMENT_ROOT'];
define('DOC_ROOT', $docRoot);

include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');


$mes=(int) date('m', strtotime(date('Y-m-d')."-1 month"));
$sql = "select rfcId, razonSocial, rfc from rfc where activo= 'si' ";
$db->setQuery($sql);
$emisores = $db->GetResult();

foreach($emisores as $key => $var) {
    $sql = "SELECT a.paymentDate,concat_ws('',b.serie,b.folio) as factura,a.amount as importe,a.deposito,d.nameContact,c.name,e.name as responsable,
        b.xml,b.empresaId ,b.serie as serief,b.folio as foliof,b.rfcId,a.comprobantePagoId FROM payment a 
        LEFT JOIN comprobante b ON a.comprobanteId=b.comprobanteId
        LEFT JOIN contract c ON b.userId=c.contractId 
        LEFT JOIN customer d ON c.customerId=d.customerId
        LEFT JOIN personal e ON c.responsableCuenta=e.personalId
        WHERE b.rfcId='".$var['rfcId']."' AND date(a.paymentDate)>=DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY
        AND date(a.paymentDate) <= LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ORDER BY  a.paymentDate DESC ";
    $db->setQuery($sql);
    $payments = $db->GetResult($sql);

    //crear el zip que enviara todos los xmls
    $zip = new ZipArchive();
    $file_name = 'XMLS-' . strtoupper($util->GetMonthByKey($mes)) . '.ZIP';
    $file_zip = DOC_ROOT . '/sendFiles/' . $file_name;
    if ($zip->open($file_zip, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) === true) {
        foreach ($payments as $kp => $vp) {
            $archivo_xml = "SIGN_" . $vp['empresaId'] . '_' . $vp['serief'] . '_' . $vp['foliof'] . '.xml';
            $enlace_xml = DOC_ROOT . '/empresas/' . $vp['empresaId'] . '/certificados/' . $vp['rfcId'] . '/facturas/xml/' . $archivo_xml;
            if (file_exists($enlace_xml)) {
                $zip->addFile($enlace_xml, 'XML-CFDI/' . $archivo_xml);
            }
            //comprobar si hay complementos, tambien se adjunta en el zip
            if ($vp['comprobantePagoId'] > 0) {
                $db->setQuery("SELECT folio,serie,empresaId,rfcId FROM comprobante WHERE comprobanteId='" . $vp['comprobantePagoId'] . "'");
                $complemento = $db->GetRow();
                if (!empty($complemento)) {
                    $archivo_xml_comp = "SIGN_" . $complemento['empresaId'] . '_' . $complemento['serie'] . '_' . $complemento['folio'] . '.xml';
                    $enlace_xml_comp = DOC_ROOT . '/empresas/' . $complemento['empresaId'] . '/certificados/' . $complemento['rfcId'] . '/facturas/xml/' . $archivo_xml_comp;
                    if (file_exists($enlace_xml_comp))
                        $zip->addFile($enlace_xml_comp, 'XML-COMP/' . $archivo_xml_comp);
                }
            }
        }
        $zip->close();
    }
    $html = '<html>
			<head>
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
    $smarty->assign("registros", $payments);
    $smarty->assign('mes', $util->GetMonthByKey($mes));
    $smarty->assign('razonSocial', $var['razonSocial']);
    $contents = $smarty->fetch(DOC_ROOT . '/templates/lists/rep-cobranza.tpl');
    $html .= $contents;
    $html = str_replace(',', '', $html);

    $file = 'COBRANZA-' . strtoupper($util->GetMonthByKey($mes));
    $excel->ConvertToExcel($html, 'xlsx', false, $file, true);
    $subject = $file;
    $body = "Reporte de cobranza de la empresa ".strtoupper($var['razonSocial'])." del mes de " . strtoupper($util->GetMonthByKey($mes)) . "
             <br><br>
             Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;

    if (REP_STATUS == 'test')
        $to = array(EMAIL_DEV => 'Desarrollador');
    else
        $to = array('rzetina@braunhuerin.com.mx' => 'ROGELIO ZETINA', EMAIL_DEV => 'Desarrollador');

    $attachment = DOC_ROOT . "/sendFiles/" . $file . ".xlsx";
    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file . ".xlsx", $file_zip, $file_name, 'noreply@braunhuerin.com.mx', "REPORTE DE COBRANZA");
    echo "reporte enviado correctamente";
    unlink($attachment);
    unlink($file_zip);
}
