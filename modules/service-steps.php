<?php
	
	/* Start Session Control - Don't Remove This */
    /* Star Session Control Modules*/
    $user->allowAccess(1);  //level 1
    $user->allowAccess(24);  //level 2
    $user->allowAccess(28);//level 3
    /* end Session Control Modules*/
	
	$step->setServicioId($_GET["id"]);
	$steps = $step->Enumerate();
		
	$smarty->assign("steps", $steps);
	$smarty->assign("id", $_GET["id"]);
	$smarty->assign('mainMnu','contratos');

?>