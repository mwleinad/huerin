<?php

	/* Star Session Control Modules*/
    $user->allowAccess(1);
	$user->allowAccess(8);
	/* End Session Control */
	$personals = $personal->Enumerate();

	$smarty->assign("personals", $personals);
	$smarty->assign("work_teams", $workTeam->Enumerate());
	$smarty->assign('mainMnu','catalogos');

?>
