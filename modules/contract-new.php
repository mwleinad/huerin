<?php
	
	/* Start Session Control - Don't Remove This */
    $user->allowAccess(2);
    $user->allowAccess(62);
    $user->allowAccess(63);
	/* End Session Control */
	
	if($_POST)
	{
		if($_POST["type"] == "Persona Moral")
		{
			$_POST['regimenId'] = $_POST['regimenIdMoral'];
		}
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

		$contract->setNoExtAddress($_POST['noExtAddress']);
		$contract->setNoIntAddress($_POST['noIntAddress']);
		$contract->setColoniaAddress($_POST['coloniaAddress']);
		$contract->setMunicipioAddress($_POST['municipioAddress']);
		$contract->setEstadoAddress($_POST['estadoAddress']);
		$contract->setPaisAddress($_POST['paisAddress']);

		$contract->setEncargadoCuenta($_POST['encargadoCuenta']);
		$contract->setResponsableCuenta($_POST['responsableCuenta']);
		$contract->setPermisos($_POST['permisos'],$_POST['responsableCuenta']);
		$contract->setAuxiliarCuenta($_POST['auxiliarCuenta']);

		$contract->setCpComercial($_POST['cpComercial']);
		$contract->setCpAddress($_POST['cpAddress']);
		$contract->setFacturador($_POST['facturador']);


		$contract->setMetodoDePago($_POST['metodoDePago']);
		$contract->setNoCuenta($_POST['noCuenta']);

		$contract->Save();
		header("Location:".WEB_ROOT."/contract/id/".$_GET['id']);
		
	}

	$customer->setCustomerId($_GET["id"]);
	$infoCustomer = $customer->Info();
	$smarty->assign("infoCustomer", $infoCustomer);
	
	//Obtenemos los accionistas
	$accionista->setContractId($_GET["id"]);
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
	
	$empleados = $personal->Enumerate();			
	$smarty->assign("empleados", $empleados);
		$departamentos = $departamentos->Enumerate();
	$smarty->assign("departamentos", $departamentos);

?>