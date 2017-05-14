<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('document-basic');	
	/* End Session Control */
	
	$documents = $docBasic->Enumerate();
		
	$smarty->assign("documents", $documents);
	$smarty->assign('mainMnu','catalogos');

?>