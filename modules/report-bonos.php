<?php

	$user->allowAccess();

	$DEPARTAMENTOS = $departamentos->Enumerate();

	$TRIMESTRE = $arrayName = array(
		'0' => array('fecha' => '01 02 03' , 'fechaNombre' => 'Enero'.' - Marzo'),
		'1' => array('fecha' => '04 05 06' , 'fechaNombre' => 'Abril'.' - Junio'),
		'2' => array('fecha' => '07 08 09' , 'fechaNombre' => 'Julio'.' - Septiembre'),
		'3' => array('fecha' => '10 11 12' , 'fechaNombre' => 'Octubre'.' - Diciembre'),
	);

	if ($_POST['personalId'] != '') {
		$INFO = $reportebonos->DATOS_REPORTE_BONO();
	}

	$smarty->assign("TRIMESTRE", $TRIMESTRE);
	$smarty->assign("DATOS", $INFO['DATOS']);
	$smarty->assign("DEPARTAMENTOS", $DEPARTAMENTOS);
?>