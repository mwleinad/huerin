<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess("report-documentacion-permanente");	
	/* End Session Control */
  	
	$personals = $personal->Enumerate();
  	$departamentos = $departamentos->Enumerate();
	
	$smarty->assign("personals", $personals);
	$smarty->assign("departamentos", $departamentos);
	$smarty->assign('mainMnu','reportes');
	
?>