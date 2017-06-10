<?php
	
	/* Start Session Control - Don't Remove This */
	//$user->allowAccess('contract');
	/* End Session Control */
	
	$okMsg = $_SESSION['msgOk'];
	$_SESSION['msgOk'] = 0;
			
	//Obtenemos los Tipos de Contrato
	$categories = $contCat->Enumerate();
	
	$val = split('-',$_GET['id']);
	
	$id = $val[0];
	$status = $val[1];
		
	$resContracts = $contract->Enumerate($id, $status);
	
	//print_r($resContracts);
	$customer->setCustomerId($_GET["id"]);
	$infoCustomer = $customer->Info();
	
	//session
	$_SESSION["search"]["customer"] = $infoCustomer["customerId"];
	$_SESSION["search"]["customerName"] = $infoCustomer["nameContact"];

	$smarty->assign("infoCustomer", $infoCustomer);
				
	$contracts = array();
	foreach($resContracts as $key => $val){
		$card = $val;
		
		$customer->setCustomerId($val['customerId']);
		$card['customer'] = $customer->GetNameById();
		
		$contCat->setContCatId($val['contCatId']);
		$card['tipo'] = $contCat->GetNameById();
		
		$personal->setPersonalId($val['responsableCuenta']);
		$card['nomResp'] = $personal->GetNameById();
		
		$permisos = array();
		$permisos2 = array();
		$resPermisos = explode('-',$val['permisos']);
		foreach($resPermisos as $res){
			$value = explode(',',$res);
			
			$idPersonal = $value[1];
			$idDepto = $value[0];
			
			$personal->setPersonalId($idPersonal);
			$nomPers = $personal->GetNameById();
			
			$permisos[$idDepto] = $nomPers;
			$permisos2[$idDepto] = $idPersonal;
		}		
		$card['responsables'] = $permisos;
		$card['responsables2'] = $permisos2;
				
		$card['status'] = ucfirst($card['status']);
		
		$contracts[$key] = $card;	
		
	}//foreach
	
	$departamentos = $departamentos->Enumerate();
	$empleados = $personal->Enumerate();			
	
	$smarty->assign("departamentos", $departamentos);	
	$smarty->assign("id", $_GET["id"]);	
	$smarty->assign("msgOk", $okMsg);	
	$smarty->assign("categories", $categories);
	$smarty->assign("contracts", $contracts);
	$smarty->assign('mainMnu','contratos');
	$smarty->assign("empleados", $empleados);

?>