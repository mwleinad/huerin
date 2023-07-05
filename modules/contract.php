<?php

    /* Star Session Control Modules*/
    $user->allowAccess(2);  //level 1
    $user->allowAccess(62);//level 2
    /* end Session Control Modules*/

	$okMsg = $_SESSION['msgOk'];
	$_SESSION['msgOk'] = 0;

	//Obtenemos los Tipos de Contrato
	$categories = $contCat->Enumerate();
	//si por alguna razon el usuario cliente quisiera ingresar con otro id obligar a que sea exclusivo de el.
     if($_SESSION['User']['level'] >= 100)
         $_GET['id']= $_SESSION['User']['userId'];

	$val = explode('-',$_GET['id']);

	$id = $val[0];
	$status = $val[1];
	$resContracts = $contract->Enumerate($id, $status);

	$customer->setCustomerId($_GET["id"]);
	$infoCustomer = $customer->Info();

	$_SESSION["search"]["customer"] = $infoCustomer["customerId"];
	$_SESSION["search"]["customerName"] = $infoCustomer["nameContact"];

	$smarty->assign("infoCustomer", $infoCustomer);
	$departamentos = $departamentos->Enumerate();

	$smarty->assign("departamentos", $departamentos);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign("msgOk", $okMsg);
	$smarty->assign("categories", $categories);
	$smarty->assign("contracts", $resContracts);
	$smarty->assign('mainMnu','contratos');


?>
