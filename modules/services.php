<?php
    /* Star Session Control Modules*/
    $user->allowAccess(2);//level 1
    $user->allowAccess(62);//level 2
    $user->allowAccess(86);//level 3
    /* End Session Control */
	
	$servicio->setContractId($_GET["id"]);
	$servicios = $servicio->Enumerate();
		
	$smarty->assign("servicios", $servicios);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','contratos');

?>