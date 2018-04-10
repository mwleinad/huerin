<?php
	
	/* Start Session Control - Don't Remove This */
    $user->allowAccess(2);
    $user->allowAccess(62);
    $user->allowAccess(66);

	/* End Session Control */
	
	$contractId = intval($_GET['contId']);
	
//	$customer->setCustomerId($_GET["id"]);
//	$infoCustomer = $customer->Info();
//	$smarty->assign("infoCustomer", $infoCustomer);
	
	//Obtenemos los accionistas
	$accionista->setContractId($_GET["id"]);
	$accionistas = $accionista->Enumerate();
	$smarty->assign("accionistas", $accionistas);

	//Obtenemos los sociedades
	$sociedades = $sociedad->EnumerateAll();
	$smarty->assign("sociedades", $sociedades);

	//Obtenemos los regimenes
	$regimenes = $regimen->EnumerateAll();
	$smarty->assign("regimenes", $regimenes);
	
	$smarty->assign('mainMnu','contratos');

	$contract->setContractId($_GET["contId"]);
	$infoRazonSocial = $contract->Info();
	$smarty->assign("infoRazonSocial", $infoRazonSocial);
	$smarty->assign("contractInfo", $infoRazonSocial);
	
	//Requerimientos
	$requerimiento->setContractId($_GET["contId"]);
	$requerimientos = $requerimiento->Enumerate();
	$smarty->assign("requerimientos", $requerimientos);
	
	//documentos
	$documento->setContractId($_GET["contId"]);
	$documentos = $documento->Enumerate();
	$smarty->assign("documentos", $documentos);

	$archivo->setContractId($_GET["contId"]);
	$archivos = $archivo->Enumerate();
	$smarty->assign("archivos", $archivos);	
	
	$impuesto->setContractId($_GET["contId"]);
	$impuestos = $impuesto->EnumerateContract();
	$smarty->assign("impuestos", $impuestos);

	$obligacion->setContractId($_GET["contId"]);
	$obligaciones = $obligacion->EnumerateContract();
	$smarty->assign("obligaciones", $obligaciones);
	
	$deptos = $departamentos->Enumerate(true);			
	$smarty->assign("departamentos", $deptos);
	
	$permisos = $contract->UsuariosConPermiso($infoRazonSocial['permisos'],$infoRazonSocial['responsableCuenta']);			
	$smarty->assign("permisos", $permisos);
	
	$empleados = $personal->Enumerate();			
	$smarty->assign("empleados", $empleados);
	
	//Checamos los permisos para eliminar DOCs y Archivos
	
	$permisoDel = array('Gerente','Socio','Asistente');
	
	$allowDelete = 0;
	if(in_array($User['tipoPersonal'], $permisoDel))
		$allowDelete = 1;

	$smarty->assign('allowDelete',$allowDelete);
	
?>