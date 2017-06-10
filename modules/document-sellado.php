<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('document-sellado');	
	/* End Session Control */
	
	$documents = $docSellado->Enumerate();
		
	$smarty->assign("documents", $documents);
	$smarty->assign('mainMnu','catalogos');

?>