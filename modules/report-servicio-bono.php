<?php

    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(163);//level 2
    /* end Session Control Modules*/
	
	$departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);
	
	//$clientes = $customer->Enumerate();
	$smarty->assign("clientes", $clientes);
	$smarty->assign("search", $_SESSION["search"]);
	
	//$clientes = $workflow->EnumerateWorkflows($clientes, date("m"), date("Y"));
	$smarty->assign("clientes", $clientes);
	$smarty->assign('mainMnu','reportes');

    $unlimited = $rol->accessAnyContract();
    $smarty->assign('unlimited',$unlimited);

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
	
	if($_SESSION["search"])
	{
		$_POST["responsableCuenta"] = $_SESSION["search"]["responsableCuenta"];
		
		$myCustomer = $customer->InfobyName($_SESSION["search"]["rfc"]);
		
		if($myCustomer["customerId"])
		{
			$id = $myCustomer["customerId"];
		}
		else
		{
			$id = 0;
		}
		
		$clientes = $customer->Enumerate("subordinado", $id);
			
		$clientes = $workflow->EnumerateWorkflows($clientes, $month, $year);
		//print_r($clientes);
		$smarty->assign("clientes", $clientes);
	}
	unset($_SESSION["search"]);
?>