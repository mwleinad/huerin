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

	$trimestre = explode(' ',$_POST['trimestre']);
	$anio = $_POST['anio'];


	$reportebonos->setAnio($anio);

	$reportebonos->setMesUno($trimestre[0]);
	$reportebonos->setMesDos($trimestre[1]);
	$reportebonos->setMesTres($trimestre[2]);

	$reportebonos->setPersonalId($_POST['personalId']);
	$reportebonos->setDepartamentoId($_POST['departamentoId']);
	if ($_POST['personalId'] == "" || $_POST['personalId'] == 0) {
		echo "<o style='color:red'>Selecciona Supervisor.....</o><br>";
	}
	if ($_POST['departamentoId'] == "") {
		echo "<o style='color:red'>Selecciona Departamento.....</o><br>";
	}

	if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
		$INFO = $reportebonos->DATOS_REPORTE_BONO();
	}

	$smarty->assign("DATOS", $INFO['DATOS']);
	$smarty->assign("DOC_ROOT", DOC_ROOT);

	if(!$_POST["type"]){
		$html = $smarty->fetch(DOC_ROOT.'/templates/lists/report-bonos-exel.tpl');
	}else{
		$html = $smarty->fetch(DOC_ROOT.'/templates/lists/report-bonos.tpl');
	}


	$excel->ConvertToExcel($html, $_POST["type"]);

	if(!$_POST["type"])
	{
		$_POST["type"] = "xlsx";
	}
	if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
		echo "ok[#]";
		echo WEB_ROOT."/download.php?file=".WEB_ROOT."/exportar.".$_POST["type"];
	}


?>