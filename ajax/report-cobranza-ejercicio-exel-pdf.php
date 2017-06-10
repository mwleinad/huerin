<?php
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// exit;

	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');
	session_start();

	ini_set("memory_limit","500M");

	// $trimestre = explode(' ',$_POST['trimestre']);
	$anio = $_POST['anio'];
	$cliente = $_POST['cliente'];
	// echo "<pre>";
	// print_r($_POST);
	// exit;


	$reporteCobranzaEjercicio->setAnio($anio);
	$reporteCobranzaEjercicio->setCliente($cliente);

	// $reporteCobranzaEjercicio->setMesUno($trimestre[0]);
	// $reporteCobranzaEjercicio->setMesDos($trimestre[1]);
	// $reporteCobranzaEjercicio->setMesTres($trimestre[2]);

	// $reporteCobranzaEjercicio->setPersonalId($_POST['personalId']);
	// $reporteCobranzaEjercicio->setDepartamentoId($_POST['departamentoId']);

	// if ($_POST['personalId'] == "" || $_POST['personalId'] == 0) {
	// 	echo "<o style='color:red'>Selecciona Supervisor.....</o><br>";
	// }
	// if ($_POST['departamentoId'] == "") {
	// 	echo "<o style='color:red'>Selecciona Departamento.....</o><br>";
	// }

	// if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
		$INFO = $reporteCobranzaEjercicio->EnumerateClientesCobranza();
		// echo "ok[#]";
		$smarty->assign("DATOS", $INFO);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$html = $smarty->fetch(DOC_ROOT.'/templates/lists/report-cobranza-ejercicio.tpl');
	// }




	$excel->ConvertToExcel($html, $_POST["type"]);

	if(!$_POST["type"])
	{
		$_POST["type"] = "xlsx";
	}
	// if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
		echo "ok[#]";
		echo WEB_ROOT."/download.php?file=".WEB_ROOT."/exportar.".$_POST["type"];
	// }


?>