<?php

    /* Star Session Control Modules*/
    $user->allowAccess(6);  //level 1
    /* end Session Control Modules*/
	if($_POST && $_FILES && !$_POST["editArchivo"])
	{
		$departamentos->SubirArchivo();			
	}

	if($_POST && $_FILES && $_POST["editArchivo"])
	{
		$departamentos->ActualizarArchivo();			
	}


	$resDepartamentos = $departamentos->Enumerate();

	$smarty->assign("resDepartamentos", $resDepartamentos);
	$smarty->assign("id", $_GET["id"]);

	$departamentos->setDepartamentoId($_GET["id"]);
	$departamento = $departamentos->Info();

    //comprobar si el departamento pasado tiene permiso el rol
    $permisoId = $rol->GetPermisoByTitulo($departamento['departamento']);
    if($permisoId<=0)
        $permisoId=-1;

    $user->allowAccess($permisoId);

    $smarty->assign("departamento", $departamento);

	$archivos = $departamentos->Archivos();
	$smarty->assign("archivos", $archivos);
	
	$smarty->assign('mainMnu','archivos');
	
	if($User['roleId'] <= 2)
	{
		$smarty->assign("allowDelete", 1);
	}

/*	$uplToken = rand();
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
	$smarty->assign("workFlowId", $_GET["id"]);*/
	