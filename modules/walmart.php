<?php

	/* Start Session Control - Don't Remove This */
	$user->allowAccess('personal');	
	/* End Session Control */
	
	$wallmarts = $wallmart->Enumerate();
		
	$smarty->assign("wallmarts", $wallmarts);
	$smarty->assign('mainMnu','catalogos');

?>