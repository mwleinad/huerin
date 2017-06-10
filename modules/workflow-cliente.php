<?php
	
	/* Start Session Control - Don't Remove This */
	//$user->allowAccess('customer');	
	/* End Session Control */

	if($_POST && $_FILES)
	{
		$workflow->UploadControl();
	}
	$workflow->setInstanciaServicioId($_GET["id"]);
	$myWorkflow = $workflow->Info();
//	print_r($myWorkflow);
//	print_r($myWorkflow);

	$smarty->assign("dia", date("d"));
		
	$smarty->assign("myWorkflow", $myWorkflow);
	$smarty->assign('mainMnu','servicios');
	
	if($myWorkflow["customerId"] != $User["userId"])
	{
		header("Location:".WEB_ROOT);
	}

	$smarty->assign("stepId", $_POST["stepId"]);

?>