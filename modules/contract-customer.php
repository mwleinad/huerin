<?php

    //comprobar que el cliente tenga acceso al modulo cliente y a sus razones sociales
    //aunque tenga permiso no puede ver de todo solo el de el.
    $user->allowAccess(2);  //level 1
    $user->allowAccess(62);//level 2
	$categories = $contCat->Enumerate();
	//obligar que el id sea exclusivamete del cliente
    $id = $_SESSION['User']['userId'];
	$resContracts = $contract->Enumerate($id, $status);
	$customer->setCustomerId($id);
	$infoCustomer = $customer->Info();
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