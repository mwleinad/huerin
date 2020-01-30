<?php
if(!isset($_SESSION)){
    session_start();
}
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$data = $reportebonos->generateReportBonosWhitLevel($_POST);
$period = $_POST['period'];
if($period== "efm"){
    $monthNames = array("Ene", "Feb", "Mar");
}elseif($period == "amj"){
    $monthNames = array("Abr", "May", "Jun");
}elseif($period == "jas"){
    $monthNames = array("Jul", "Ago", "Sep");
}elseif($period == "ond"){
    $monthNames = array("Oct", "Nov", "Dic");
}else{
    $monthNames = array("Ene", "Feb", "Mar","Abr", "May", "Jun","Jul", "Ago", "Sep","Oct", "Nov", "Dic");
}
$html = '<html>
			<head>
				<title>reporte de bonos-cobranza</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
						text-align: left;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						border-collapse: collapse;
					}
				</style>
			</head>
			';

$smarty->assign("nombreMeses", $monthNames);
$smarty->assign("data", $data);
$smarty->assign("EXCEL", "SI");
$smarty->assign("DOC_ROOT", DOC_ROOT);
$html .= $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio-bono-order-rol.tpl');

$name = 'reporte_de_bonos_cobranza';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$name.".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;
exit;