<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess('customer');	
	/* End Session Control */
	
	$customers = $customer->Enumerate();
		
	$smarty->assign("customers", $customers);
	$smarty->assign('mainMnu','contratos');

?>