<?php
if(!isset($_SESSION)){
    session_start();
}
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
$personal->setPersonalId($_POST["responsableCuenta"]);
$results = $personal->GenerateReportExpediente();
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
$smarty->assign("results", $results);
$html .= $smarty->fetch(DOC_ROOT.'/templates/lists/report-exp-employe.tpl');
$name = 'reporte_expedientes_empleados';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$name.".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;
exit;