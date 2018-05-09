<?php

	$cuentas = $contract->Enumerate($User["userId"]);
	$smarty->assign("cuentas", $cuentas);

	$servicios = $servicio->EnumerateActive("subordinado", $User["userId"], 0);

	$smarty->assign("servicios", $servicios);
	$smarty->assign('mainMnu','servicios');

?>