<?php

	$user->allowAccess();

	$departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);

	//$clientes = $customer->Enumerate();
	$smarty->assign("clientes", $clientes);
	$smarty->assign("search", $_SESSION["search"]);
	
	//$clientes = $workflow->EnumerateWorkflows($clientes, date("m"), date("Y"));
	$smarty->assign("clientes", $clientes);
	$smarty->assign('mainMnu','reportes');

	if($_SESSION["search"]["month"])
	{
		$month = $_SESSION["search"]["month"];
	}
	else
	{
		$month = date("m");
	}

	if($_SESSION["search"]["year"])
	{
		$year = $_SESSION["search"]["year"];
	}
	else
	{
		$year = date("Y");
	}
	
	$smarty->assign("month", $month);
	$smarty->assign("year", $year);
	
	$personals = $personal->Enumerate();
	$smarty->assign("personals", $personals);
	// $DEPARTAMENTOS = $departamentos->Enumerate();

	// $TRIMESTRE = $arrayName = array(
	// 	'0' => array('fecha' => '01 02 03' , 'fechaNombre' => 'Enero'.' - Marzo'),
	// 	'1' => array('fecha' => '04 05 06' , 'fechaNombre' => 'Abril'.' - Junio'),
	// 	'2' => array('fecha' => '07 08 09' , 'fechaNombre' => 'Julio'.' - Septiembre'),
	// 	'3' => array('fecha' => '10 11 12' , 'fechaNombre' => 'Octubre'.' - Diciembre'),
	// );

	// if ($_POST['personalId'] != '') {
	// 	$INFO = $reportebonos->DATOS_REPORTE_BONO();
	// }

	// $smarty->assign("TRIMESTRE", $TRIMESTRE);
	$smarty->assign("DATOS", $INFO);
	// $smarty->assign("DEPARTAMENTOS", $DEPARTAMENTOS);
?>