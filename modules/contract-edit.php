<?php

	/* Start Session Control - Don't Remove This */
     $user->allowAccess(2);//level 1
     $user->allowAccess(62);//level 2
     $user->allowAccess(64);//level 3
	/* End Session Control */

	if($_POST)
	{
		//print_r($_POST);
		if($_POST["type"] == "Persona Moral")
		{
			$_POST['regimenId'] = $_POST['regimenIdMoral'];
		}
		$contract->setContractId($_GET['contId']);
		$contract->setCustomerId($_GET['id']);
		$contract->setType($_POST['type']);
		$contract->setRfc($_POST['rfc']);
		$contract->setTelefono($_POST['telefono']);
		$contract->setSociedadId($_POST['sociedadId']);
		$contract->setRegimenId($_POST['regimenId']);
		$contract->setNombreComercial($_POST['nombreComercial']);
		$contract->setDireccionComercial($_POST['direccionComercial']);
		$contract->setNameContactoAdministrativo($_POST['nameContactoAdministrativo']);
		$contract->setEmailContactoAdministrativo($_POST['emailContactoAdministrativo']);
		$contract->setTelefonoContactoAdministrativo($_POST['telefonoContactoAdministrativo']);
		$contract->setNameContactoContabilidad($_POST['nameContactoContabilidad']);
		$contract->setEmailContactoContabilidad($_POST['emailContactoContabilidad']);
		$contract->setTelefonoContactoContabilidad($_POST['telefonoContactoContabilidad']);
		$contract->setNameContactoDirectivo($_POST['nameContactoDirectivo']);
		$contract->setEmailContactoDirectivo($_POST['emailContactoDirectivo']);
		$contract->setTelefonoContactoDirectivo($_POST['telefonoContactoDirectivo']);
		$contract->setClaveFiel($_POST['claveFiel']);
		$contract->setClaveCiec($_POST['claveCiec']);
		$contract->setClaveIdse($_POST['claveIdse']);
		$contract->setClaveIsn($_POST['claveIsn']);
		$contract->setName($_POST['name']);
		$contract->setAddress($_POST['address']);
		$contract->setTelefonoCelularDirectivo($_POST['telefonoCelularDirectivo']);

		$contract->setNoExtComercial($_POST['noExtComercial']);
		$contract->setNoIntComercial($_POST['noIntComercial']);
		$contract->setColoniaComercial($_POST['coloniaComercial']);
		$contract->setMunicipioComercial($_POST['municipioComercial']);
		$contract->setEstadoComercial($_POST['estadoComercial']);
		$contract->setCpComercial($_POST['cpComercial']);

		$contract->setNoExtAddress($_POST['noExtAddress']);
		$contract->setNoIntAddress($_POST['noIntAddress']);
		$contract->setColoniaAddress($_POST['coloniaAddress']);
		$contract->setMunicipioAddress($_POST['municipioAddress']);
		$contract->setEstadoAddress($_POST['estadoAddress']);
		$contract->setPaisAddress($_POST['paisAddress']);
		$contract->setCpAddress($_POST['cpAddress']);

		$contract->setMetodoDePago($_POST['metodoDePago']);
		$contract->setNoCuenta($_POST['noCuenta']);

		$contract->setEncargadoCuenta($_POST['encargadoCuenta']);
		$contract->setCobrador($_POST['cobrador']);
		$contract->setResponsableCuenta($_POST['responsableCuenta']);
		$contract->setPermisos($_POST['permisos'],$_POST['responsableCuenta']);
		$contract->setAuxiliarCuenta($_POST['auxiliarCuenta']);

		$contract->setFacturador($_POST['facturador']);

		$contract->UpdateMyContract();

		$contract->setContractId($_GET["contId"]);
		$contractInfo = $contract->Info();

		$okMsg = "1";
	  	$smarty->assign("msgOk", $okMsg);
		//header("Location:".WEB_ROOT."/contract/id/".$contractInfo['customerId']);

	}


	$contract->setContractId($_GET["contId"]);
	$contractInfo = $contract->Info();
	$smarty->assign("contractInfo", $contractInfo);
//	print_r($contractInfo);

	foreach(explode("-",$contractInfo['permisos']) as $key=>$value)
	{
		$z=explode(",",$value);
		$permisos[$z[0]]=$z[1];

	}
	$smarty->assign("permisos", $permisos);

	$customer->setCustomerId($contractInfo["customerId"]);
	$infoCustomer = $customer->Info();
	$smarty->assign("infoCustomer", $infoCustomer);

	//Obtenemos los accionistas
	$accionista->setContractId($contractInfo["customerId"]);
	$accionistas = $accionista->Enumerate();
	$smarty->assign("accionistas", $accionistas);

	//Obtenemos los sociedades
	$sociedades = $sociedad->EnumerateAll();
	$smarty->assign("sociedades", $sociedades);

	//Obtenemos los regimenes
	$regimenes = $regimen->EnumerateAll("fisica");
	$smarty->assign("regimenes", $regimenes);

	$regimenesMoral = $regimen->EnumerateAll("moral");
	$smarty->assign("regimenesMoral", $regimenesMoral);

	//Obtenemos la fecha actual para habilitar el calendario
	$cal['min'] = date('Ymd',strtotime('-5 years'));
	$cal['max'] = date('Ymd');

	//Obtenemos la fecha actual para habilitar el calendario
	$calO['min'] = date('Ymd');
	$calO['max'] = date('Ymd',strtotime('+5 years'));

	$smarty->assign("cal", $cal);
	$smarty->assign("calO", $calO);
	$smarty->assign('mainMnu','contratos');

	//documentos
	$documento->setContractId($_GET["contId"]);
	$documentos = $documento->Enumerate();
	$smarty->assign("documentos", $documentos);

	$requerimiento->setContractId($_GET["contId"]);
	$requerimientos = $requerimiento->Enumerate();
	$smarty->assign("requerimientos", $requerimientos);

	$archivo->setContractId($_GET["contId"]);
	$archivos = $archivo->Enumerate();
	$smarty->assign("archivos", $archivos);

	$impuesto->setContractId($_GET["contId"]);
	$impuestos = $impuesto->EnumerateContract();
	$smarty->assign("impuestos", $impuestos);

	$obligacion->setContractId($_GET["contId"]);
	$obligaciones = $obligacion->EnumerateContract();
	$smarty->assign("obligaciones", $obligaciones);

	$empleados = $personal->EnumerateAll();
	$smarty->assign("empleados", $empleados);

	$departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);

	//Checamos los permisos para eliminar DOCs y Archivos

	$permisoDel = array('Gerente','Socio','Asistente');

	$allowDelete = 0;
	if(in_array($User['tipoPersonal'], $permisoDel))
		$allowDelete = 1;

	$smarty->assign('allowDelete',$allowDelete);

?>