<?php

	/* Start Session Control - Don't Remove This */
    $user->allowAccess(2);
    $user->allowAccess(62);
    $user->allowAccess(63);
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
		//informacion basica
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
        $contract->setPermisos($_POST['permisos']);
        $contract->setEncargadoCuenta($_POST['encargadoCuenta']);
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
            if($_POST['alternative_rz_id'] === '0') {
                $contract->setAlternativeRz($_POST['alternativeRz']);
                $contract->setAlternativeRfc($_POST['alternativeRfc']);
                $contract->setAlternativeCp($_POST['alternativeCp']);
            }
        } else {
            $contract->setUseAlternativeRzForInvoice(0);
            $contract->setSeparateInvoice(1);
        }
        $contract->setQualification($_POST['idTipoClasificacion']);

		$contract->Save();
		header("Location:".WEB_ROOT."/contract/id/".$_GET['id']);
	}
    $contract->setCustomerId($_GET['id']);
	$contractBase = $contract->getInfoLastContract();
    $smarty->assign("bse", $contractBase);
    if(!empty($contractBase))
    {
        $arrayPermisos = explode("-",$contractBase['permisos']);
        $allPerm = array();
       foreach($arrayPermisos as $vp){
           list($dep,$per) = explode(',',$vp);
           if($dep==21||$dep==1||$dep==22||$dep==26)
             $allPerm[$dep] = $per;
       }
        $smarty->assign("allPerm", $allPerm);
    }


	$customer->setCustomerId($_GET["id"]);
	$infoCustomer = $customer->Info();
	$smarty->assign("infoCustomer", $infoCustomer);

	//Obtenemos los accionistas
	$accionista->setContractId($_GET["id"]);
	$accionistas = $accionista->Enumerate();
	$smarty->assign("accionistas", $accionistas);

    //Obtenemos los facturadores
    $emisores = $rfc->listEmisores();
    $smarty->assign("emisores", $emisores);

	//Obtenemos los sociedades
	$sociedades = $sociedad->EnumerateAll();
	$smarty->assign("sociedades", $sociedades);

	//Obtenemos los regimenes
	$regimenes = $regimen->EnumerateAll(2);
	$smarty->assign("regimenes", $regimenes);

	$regimenesMoral = $regimen->EnumerateAll(1);
	$smarty->assign("regimenesMoral", $regimenesMoral);


    $empleados = $personal->EnumerateAll();
    $smarty->assign("empleados", $empleados);

    $filtros['depExcluidos'] ='mensajeria';
    $departamentos = $departamentos->Enumerate($filtros);
    $key_dep_account =  array_search(1, array_column($departamentos, 'departamentoId'));
    $dep_account = $departamentos[$key_dep_account];
    unset($departamentos[$key_dep_account]);
    array_unshift($departamentos, $dep_account);
    $smarty->assign("departamentos", $departamentos);
    $smarty->assign('departament_responsable', $personal->GetPersonalGroupByDepartament());

    $smarty->assign('formas_pago', $catalogo->formasDePago());

    $sectores = $catalogue->ListSectores();
    $smarty->assign("sectores", $sectores);

    $clasificaciones = $catalogue->ListClasificacion();
    $smarty->assign("clasificaciones", $clasificaciones);

	//Obtenemos la fecha actual para habilitar el calendario
	$cal['min'] = date('Ymd',strtotime('-5 years'));
	$cal['max'] = date('Ymd');

	//Obtenemos la fecha actual para habilitar el calendario
	$calO['min'] = date('Ymd');
	$calO['max'] = date('Ymd',strtotime('+5 years'));

	$smarty->assign("cal", $cal);
	$smarty->assign("calO", $calO);
	$smarty->assign('mainMnu','contratos');
?>
