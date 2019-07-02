<?php
if(!isset($_SESSION)){
    session_start();
}
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$year = $_POST['year'];
$formValues['subordinados'] = $_POST['subordinados'];
$formValues['respCuenta'] = $_POST['responsableCuenta'];
$formValues['cliente'] = $_POST["rfc"];
$formValues['year'] = $year;
$formValues['activos'] = true;
include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');
$data = $reportebonos->generateReportBonosJuridico($contracts,$_POST);

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

$smarty->assign("meses", $data["meses"]);
$smarty->assign("rowDevTotal", $data["rowDevTotal"]);
$smarty->assign("rowCobTotal", $data["rowCobTotal"]);
$smarty->assign("items", $data["items"]);
$smarty->assign("totalesAcumulados", $data["totalesAcumulados"]);
$smarty->assign("totDevVerXEncargado", $data["totDevVerXEncargado"]);
$smarty->assign("totCompVerXEncargado", $data["totCompVerXEncargado"]);
$smarty->assign("granTotalDevengado", array_sum($data["totDevVerXEncargado"]));
$smarty->assign("granTotalCompletado", array_sum($data["totCompVerXEncargado"]));
$html = $smarty->fetch(DOC_ROOT.'/templates/lists/report-cobranza-acumulada.tpl');

$name = 'reporte_de_bonos_juridico';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$name.".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;
exit;