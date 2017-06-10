<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('customer');	
	/* End Session Control */
	
	$step->setServicioId($_GET["id"]);
	$steps = $step->Enumerate();
		
	$smarty->assign("steps", $steps);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','contratos');

?>