<?php
	
	/* Start Session Control - Don't Remove This */
	//$user->allowAccess('customer');	
	/* End Session Control */
//	$clientes = $customer->Enumerate();
	//$smarty->assign("clientes", $clientes);


	$cuentas = $contract->Enumerate($User["userId"]);
	$smarty->assign("cuentas", $cuentas);

	$servicios = $servicio->EnumerateActive("subordinado", $User["userId"], 0);

	$smarty->assign("servicios", $servicios);
	$smarty->assign('mainMnu','servicios');

?>