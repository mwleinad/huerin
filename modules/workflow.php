<?php

    /* Star Session Control Modules*/
    $user->allowAccess(3);//level 1
    $user->allowAccess(100);//level 2s
    /* End Session Control */
    if($_POST && $_FILES)
	{
		if($_SESSION['uplToken'] == $_POST['uplToken']){
    		$workflow->setInstanciaServicioId($_GET["id"]);
			$workflow->UploadControl();			
		}
	}
	
	$uplToken = rand();
	$_SESSION['uplToken'] = $uplToken;
	
 	$workflow->setTipoOperacion('workflow');
  
	$workflow->setInstanciaServicioId($_GET["id"]);
	$myWorkflow = $workflow->Info();
	
	$result = $workflow->StatusById($_GET["id"]);
  	$db->setQuery("UPDATE instanciaServicio SET class = '".$result["class"]."' 
        		   WHERE instanciaServicioId = '".$_GET["id"]."'");
  	$db->UpdateData();
	
	$_SESSION["search"]["contract"] = $myWorkflow["contractId"];
	$_SESSION["search"]["customer"] = $myWorkflow["customerId"];
	$_SESSION["search"]["contractName"] = $myWorkflow["contractName"];
	$_SESSION["search"]["customerName"] = $myWorkflow["nameContact"];

	$smarty->assign("dia", date("d"));
		
	$smarty->assign("myWorkflow", $myWorkflow);
	$smarty->assign('mainMnu','servicios');
	
	$from = $_SESSION["search"]["from"];
	
	if(!$from){
		$from = "servicios";
	}
	
	$user->setUserId($User['userId']);
	$infU = $user->Info();
	$tipoPersonal = $infU['tipoPersonal'];
		
	$smarty->assign("from", $from);
	$smarty->assign("uplToken", $uplToken);
	$smarty->assign("tipoPersonal", $tipoPersonal);
	$smarty->assign("stepId", $_POST["stepId"]);
	$smarty->assign("workFlowId", $_GET["id"]);
	