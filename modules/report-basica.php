<?php

	/* Start Session Control - Don't Remove This */
	$user->allowAccess();
	/* End Session Control */

	$smarty->assign("clientes", $clientes);

	$smarty->assign("clientes", $clientes);
	$smarty->assign('mainMnu','reportes');
	$smarty->assign("search", $_SESSION["search"]);

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

		$clientes = $customer->Enumerate("propio", $id, 1);

		$clientes = $workflow->EnumerateWorkflows($clientes, $month, $year);
		//print_r($clientes);
		$smarty->assign("clientes", $clientes);
		$smarty->assign("id", $id);
	}

	//unset($_SESSION["search"]);

?>