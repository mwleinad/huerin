<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('customer');	
	/* End Session Control */
	
	$servicio->setContractId($_GET["id"]);
	$servicios = $servicio->Enumerate();
		
	$smarty->assign("servicios", $servicios);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','contratos');

?>