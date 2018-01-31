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

$sql = "SELECT a.paymentDate,concat_ws('',b.serie,b.folio) as factura,a.amount as deposito,d.nameContact,c.name,e.name as responsable FROM payment a 
        LEFT JOIN comprobante b ON a.comprobanteId=b.comprobanteId
        LEFT JOIN contract c ON b.userId=c.contractId 
        LEFT JOIN customer d ON c.customerId=d.customerId
        LEFT JOIN personal e ON c.responsableCuenta=e.personalId
        WHERE date(a.paymentDate)>=DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY
        AND date(a.paymentDate) <= LAST_DAY(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ORDER BY  a.paymentDate DESC ";
$db->setQuery($sql);
$payments = $db->GetResult($sql);
$db->setQuery('SELECT DATE_ADD(CURDATE(),INTERVAL -1 MONTH) - INTERVAL DAYOFMONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) - 1 DAY from payment limit 1');
$initMonth = $db->GetSingle();
$mes = date('m',$initMonth);

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
$smarty->assign("registros", $payments);
$smarty->assign('mes',$util->GetMonthByKey($mes));
$contents = $smarty->fetch(DOC_ROOT . '/templates/lists/rep-cobranza.tpl');
$html .= $contents;
//$html = str_replace('$', '', $html);
$html = str_replace(',', '', $html);

$file = 'COBRANZA-'.strtoupper($util->GetMonthByKey($mes));
$excel->ConvertToExcel($html, 'xlsx', false,$file,true);
