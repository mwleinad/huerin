<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('document-general');	
	/* End Session Control */
	
	$documents = $docGral->Enumerate();
		
	$smarty->assign("documents", $documents);
	$smarty->assign('mainMnu','catalogos');

?>