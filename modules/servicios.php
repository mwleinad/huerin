<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('customer');	
	/* End Session Control */
//	$clientes = $customer->Enumerate();
//	$smarty->assign("clientes", $clientes);

//	$cuentas = $contract->Enumerate($clientes[0]["customerId"]);
//	$smarty->assign("cuentas", $cuentas);
	
	//print_r($_SESSION);
  
  $okMsg = $_SESSION['msgOk'];
	unset($_SESSION['msgOk']);
  
	if($_SESSION["search"]["contract"] && $_SESSION["search"]["customer"])
	{
		$servicios = $servicio->EnumerateActive("subordinado",$_SESSION["search"]["customer"], $_SESSION["search"]["contract"]);
		$smarty->assign("customerNameSearch", $_SESSION["search"]["contractName"]);
		
		unset($_SESSION["search"]);
	}
	$personals = $personal->Enumerate();
	$smarty->assign("personals", $personals);
  $departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);
	$smarty->assign("servicios", $servicios);
	$smarty->assign('mainMnu','servicios');  
  $smarty->assign("msgOk", $okMsg);	