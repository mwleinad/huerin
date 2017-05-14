<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('contract-category');	
	/* End Session Control */
	
	$categories = $contCat->Enumerate();
	$smarty->assign("categories", $categories);
	$smarty->assign('mainMnu','catalogos');
	
?>