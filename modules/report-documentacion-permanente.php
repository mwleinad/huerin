<?php
	
	/* Start Session Control - Don't Remove This */
    $user->allowAccess(7);  //level 1
    $user->allowAccess(157);//level 2
	/* End Session Control */
    $rol->setRolId($User['roleId']);
    $unlimited = $rol->ValidatePrivilegiosRol(array('gerente','supervisor','contador','auxiliar'),array('Gerente de Juridico'));

	$personals = $personal->Enumerate();
  	$departamentos = $departamentos->Enumerate();

    $smarty->assign("unlimited", $unlimited);
	$smarty->assign("personals", $personals);
	$smarty->assign("departamentos", $departamentos);
	$smarty->assign('mainMnu','reportes');
	
?>