<?php

    /* Star Session Control Modules*/
    $user->allowAccess(2);  //level 1
    $user->allowAccess(62);//level 2
    /* end Session Control Modules*/
	
	$okMsg = $_SESSION['msgOk'];
	$_SESSION['msgOk'] = 0;
			
	//Obtenemos los Tipos de Contrato
	$categories = $contCat->Enumerate();
	//si por alguna razon el usuario cliente quisiera ingresar con otro id obligar a que sea exclusivo de el.
     if($_SESSION['User']['roleId']==4)
         $_GET['id']= $_SESSION['User']['userId'];
	
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
        $parciales = $customer->GetServicesByContract($val["contractId"],'bajaParcial');
        if(count($parciales)>0&&$card['activo']=='Si')
            $card["haveTemporal"] = 1;

		$card['status'] = ucfirst($card['status']);
		//comprobar si esta activo y tiene parciales

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