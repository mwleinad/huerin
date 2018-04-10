<?php

    /* Star Session Control Modules*/
    $user->allowAccess(4);  //level 1
    $user->allowAccess(121);//level 2
    /* end Session Control Modules*/
	if(!$_GET["tipo"])
	{
		$_GET["tipo"] = "Activos";
	}

	if($_SESSION["search"]["customer"])
	{
		$_POST["valur"] = $_SESSION["search"]["customerName"];
		$customers = $customer->SuggestCustomerCatalog("", "subordinado", 0, "activo");

		//$customers = $customer->Search("subordinado", "activo");
		$smarty->assign("customers", $customers);

		$smarty->assign("customerNameSearch", $_SESSION["search"]["customerName"]);

		unset($_SESSION["search"]);
	}

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
	
	//print_r($customers);	
	$smarty->assign("tipo", $_GET["tipo"]);
	$smarty->assign('mainMnu','cxc');

?>