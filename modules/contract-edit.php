<?php

	/* Start Session Control - Don't Remove This */
     $user->allowAccess(2);//level 1
     $user->allowAccess(62);//level 2
     $user->allowAccess(64);//level 3
	/* End Session Control */
	if($_SESSION['User']['roleId']==4){
		header('Location: '.WEB_ROOT.'/customer-only');
	}

	if($_POST)
	{
		if($_POST["type"] == "Persona Moral")
		{
			$_POST['regimenId'] = $_POST['regimenIdMoral'];
		}
		$contract->setContractId($_GET['contId']);
		$contract->setCustomerId($_GET['id']);
		$contract->setType($_POST['type']);
        $contract->setFacturador($_POST['facturador']);
        $contract->setName($_POST['name']);
		$contract->setRfc($_POST['rfc']);
		$contract->setSociedadId($_POST['sociedadId']);
		$contract->setRegimenId($_POST['regimenId']);
        if(isset($_POST['actividad_comercial']))
            $contract->setActividadComercialId($_POST['actividad_comercial']);
        //direccion fiscal
        $contract->setAddress($_POST['address']);
        $contract->setNoExtAddress($_POST['noExtAddress']);
        $contract->setNoIntAddress($_POST['noIntAddress']);
        $contract->setColoniaAddress($_POST['coloniaAddress']);
        $contract->setMunicipioAddress($_POST['municipioAddress']);
        $contract->setEstadoAddress($_POST['estadoAddress']);
        $contract->setPaisAddress($_POST['paisAddress']);
        $contract->setCpAddress($_POST['cpAddress']);
        $contract->setMetodoDePago($_POST['metodoDePago']);
        $contract->setNoCuenta($_POST['noCuenta']);

        //direccion comercial
        $contract->setDireccionComercial($_POST['direccionComercial']);
        //responsables de area (no se validan si son obligatorios aunque si deberia)
        $contract->setEncargadoCuenta($_POST['encargadoCuenta']);
        $contract->setResponsableCuenta($_POST['responsableCuenta']);
        $contract->setPermisos($_POST['permisos'],$_POST['responsableCuenta']);
        $contract->setAuxiliarCuenta($_POST['auxiliarCuenta']);

        //datos de contacto
        if(isset($_POST['nameContactoAdministrativo']))
            $contract->setNameContactoAdministrativo($_POST['nameContactoAdministrativo']);
        if(isset($_POST['emailContactoAdministrativo']))
            $contract->setEmailContactoAdministrativo($_POST['emailContactoAdministrativo']);
        if(isset($_POST['telefonoContactoAdministrativo']))
            $contract->setTelefonoContactoAdministrativo($_POST['telefonoContactoAdministrativo']);
        if(isset($_POST['nameContactoContabilidad']))
            $contract->setNameContactoContabilidad($_POST['nameContactoContabilidad']);
        if(isset($_POST['emailContactoContabilidad']))
            $contract->setEmailContactoContabilidad($_POST['emailContactoContabilidad']);
        if(isset($_POST['telefonoContactoContabilidad']))
            $contract->setTelefonoContactoContabilidad($_POST['telefonoContactoContabilidad']);
        if(isset($_POST['nameRepresentanteLegal']))
            $contract->setNameRepresentanteLegal($_POST['nameRepresentanteLegal']);
        if(isset($_POST['nameContactoDirectivo']))
            $contract->setNameContactoDirectivo($_POST['nameContactoDirectivo']);
        if(isset($_POST['emailContactoDirectivo']))
            $contract->setEmailContactoDirectivo($_POST['emailContactoDirectivo']);
        if(isset($_POST['telefonoContactoDirectivo']))
            $contract->setTelefonoContactoDirectivo($_POST['telefonoContactoDirectivo']);
        if(isset($_POST['telefonoCelularDirectivo']))
            $contract->setTelefonoCelularDirectivo($_POST['telefonoCelularDirectivo']);

        //contraseñas
        if(isset($_POST['claveFiel']))
		    $contract->setClaveFiel($_POST['claveFiel']);
        if(isset($_POST['claveCiec']))
		    $contract->setClaveCiec($_POST['claveCiec']);
        if(isset($_POST['claveIdse']))
		    $contract->setClaveIdse($_POST['claveIdse']);
        if(isset($_POST['claveIsn']))
		    $contract->setClaveIsn($_POST['claveIsn']);
        if(isset($_POST['claveSip']))
            $contract->setClaveSip($_POST['claveSip']);

        if($_POST['use_alternative_rz_for_invoice'] === '1') {
            $contract->setUseAlternativeRzForInvoice(1);
            $contract->setAlterntiveRzId($_POST['alternative_rz_id']);
        }
        $contract->setQualification($_POST['qualification']);


		$contract->UpdateMyContract();
		$contract->setContractId($_GET["contId"]);
		$contractInfo = $contract->Info();
		$okMsg = "1";
	  	$smarty->assign("msgOk", $okMsg);
		//header("Location:".WEB_ROOT."/contract/id/".$contractInfo['customerId']);

	}
	$contract->setContractId($_GET["contId"]);
	$contractInfo = $contract->Info();
	//si no existe contrato se regresa al menu inicio
	if(empty($contractInfo))
        header("Location:".WEB_ROOT);

	$smarty->assign("contractInfo", $contractInfo);
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

    //Obtenemos los facturadores
    $emisores = $rfc->listEmisores(false);
    $smarty->assign("emisores", $emisores);

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

	$filtros['depExcluidos'] ='mensajeria';
	$departamentos = $departamentos->Enumerate($filtros);
	$smarty->assign("departamentos", $departamentos);

    $sectores = $catalogue->ListSectores();
    $smarty->assign("sectores", $sectores);

    $subsectores = $catalogue->ListSubsectores($contractInfo['sector_id']);
    $smarty->assign("subsectores", $subsectores);

    $actividades_comerciales = $catalogue->ListActividadesComerciales($contractInfo['subsector_id']);
    $smarty->assign("actividades_comerciales", $actividades_comerciales);

	//Checamos los permisos para eliminar DOCs y Archivos

	$permisoDel = array('Gerente','Socio','Asistente');

	$allowDelete = 0;
	if(in_array($User['tipoPersonal'], $permisoDel))
		$allowDelete = 1;

	$smarty->assign('allowDelete',$allowDelete);

?>