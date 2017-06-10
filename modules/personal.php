<?php

	/* Start Session Control - Don't Remove This */
	$user->allowAccess('personal');	
	/* End Session Control */
	
	$personals = $personal->Enumerate();
		
	$smarty->assign("personals", $personals);
	$smarty->assign('mainMnu','catalogos');

?>