<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('state');	
	/* End Session Control */
	
	$states = $state->Enumerate();
		
	$smarty->assign("states", $states);
	$smarty->assign('mainMnu','catalogos');

?>