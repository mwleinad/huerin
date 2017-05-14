<?php
	
	/* Start Session Control - Don't Remove This */
	$user->allowAccess();	
	/* End Session Control */

//( [type] => search [correo] => [texto] => [cliente] => 17 [rfc] => ALICIA BRUCKMAN GOLDMAN [responsableCuenta] => 0 [departamentoId] => [year] => 2016 [_] => )
	$year = date('Y');

	$formValues['subordinados'] = false;
	$formValues['respCuenta'] = false;
	$formValues['departamentoId'] = false;
	$formValues['cliente'] = $_SESSION['User']["username"];
	$formValues['atrasados'] = false;

	//Actualizamos la clase del workflow, porque al generar los workflows la clase esta vacia (campo Class)

	$contracts = array();
	if($User['tipoPersonal'] == 'Asistente' || $User['tipoPersonal'] == 'Socio'){

		//Si seleccionaron TODOS
		if($formValues['respCuenta'] == 0){

			$personal->setActive(1);
			$socios = $personal->ListSocios();

			foreach($socios as $res){

				$formValues['respCuenta'] = $res['personalId'];
				$formValues['subordinados'] = 1;

				$resContracts = $contract->BuscarContract($formValues, true);

				$contracts = @array_merge($contracts, $resContracts);


			}//foreach

		}else{
			$contracts = $contract->BuscarContract($formValues, true);
		}

	}else{
		$contracts = $contract->BuscarContract($formValues, true);

	}//else
	//echo count($contracts);
	//print_r($contracts);
	$idClientes = array();
	$idContracts = array();
	$contratosClte = array();
	foreach($contracts as $res){

		$contractId = $res['contractId'];
		$customerId = $res['customerId'];

		if(!in_array($customerId,$idClientes))
			$idClientes[] = $customerId;

		if(!in_array($contractId,$idContracts)){
			$idContracts[] = $contractId;
			$contratosClte[$customerId][] = $res;
		}

	}//foreach

	$clientes = array();
	//	print_r($idClientes);
	//	print_r($idContracts);
	//	print_r($contratosClte);
	foreach($idClientes as $customerId){

		$customer->setCustomerId($customerId);
		$infC = $customer->Info();

		$infC['contracts'] = $contratosClte[$customerId];

		$clientes[] = $infC;

	}//foreach
	$resClientes = array();
	foreach($clientes as $clte){
		//echo "jere";

		$contratos = array();
		foreach($clte['contracts'] as $con){
			//echo "jere2";

			//Checamos Permisos
			$resPermisos = explode('-',$con['permisos']);
			foreach($resPermisos as $res){
				$value = explode(',',$res);

				$idPersonal = $value[1];
				$idDepto = $value[0];

				$personal->setPersonalId($idPersonal);
				$nomPers = $personal->GetDataReport();

				$permisos[$idDepto] = $nomPers;
				$permisos2[$idDepto] = $idPersonal;
			}

			//$personal->setPersonalId($con['responsableCuenta']);
			//$con['responsable'] = $personal->Info();

			$servicios = array();
			foreach($con['servicios'] as $serv){

				$servicio->setServicioId($serv['servicioId']);
				$infServ = $servicio->Info();

				$noCompletados = 0;
				for($ii = 1; $ii <= 12; $ii++){
					$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

					$month = date("m");
					if($ii < $month){
						if($statusColor["class"] == "PorIniciar" || $statusColor["class"] == "Iniciado")
						{
							$noCompletados++;
						}
					}

					//Si es Servicio de Domicilio Fiscal, que no lleve colores
					if($statusColor['tipoServicioId'] == 16)
						$statusColor['class'] = '';

					if($statusColor['tipoServicioId'] == 34)
						$statusColor['class'] = '';

					if($statusColor['tipoServicioId'] == 24)
						$statusColor['class'] = '';

					if($statusColor['tipoServicioId'] == 37)
						$statusColor['class'] = '';

					$serv['instancias'][$ii] = $statusColor;
				}

				$tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
				$deptoId = $tipoServicio->GetField('departamentoId');

				$serv['responsable'] = $permisos[$deptoId];

				if($formValues['atrasados'])
				{
					if($noCompletados > 0)
					{
						$servicios[] = $serv;
					}
				}
				else
				{
					$servicios[] = $serv;
				}

			}//foreach
			$con['instanciasServicio'] = $servicios;

			$contratos[] = $con;

		}//foreach
		$clte['contracts'] = $contratos;

		$resClientes[] = $clte;

	}//foreach

	$cleanedArray = array();

	//$cleanedArray = $resClientes;

	foreach($resClientes as $key => $cliente)
	{
		foreach($cliente["contracts"] as $keyContract => $contract)
		{
			foreach($contract["instanciasServicio"] as $keyServicio => $servicio)
			{
				$card["comentario"] = $servicio["comentario"];
				$card["servicioId"] = $servicio["servicioId"];
				$card["nameContact"] = $cliente["nameContact"];
				$card["tipoPersonal"] = $servicio["responsable"]["tipoPersonal"];
				$card["responsable"] = $servicio["responsable"]["name"];
				$card["name"] = $contract["name"];
				$card["rfc"] = $contract["rfc"];
				$card["instanciasServicio"] = $servicio["instancias"];;
				$card["nombreServicio"] = $servicio["nombreServicio"];;
				$cleanedArray[] = $card;
			}
		}
	}

	$personalOrdenado = $personal->ArrayOrdenadoPersonal();

	$sortedArray = array();
	foreach($personalOrdenado as $personalKey => $personalValue)
	{
		foreach($cleanedArray as $keyCleaned => $cleanedArrayValue)
		{
			if($personalValue["name"] == $cleanedArrayValue["responsable"])
			{
				$sortedArray[] = $cleanedArrayValue;
				unset($cleanedArrayValue[$keyCleaned]);
			}
		}
	}

	$smarty->assign("nameContact", $_SESSION["User"]["username"]);

	$clientesMeses = array();
	$smarty->assign("year", date("Y"));
	$smarty->assign("cleanedArray", $sortedArray);
	$smarty->assign("clientes", $resClientes);
	$smarty->assign("clientesMeses", $clientesMeses);
	$smarty->assign("DOC_ROOT", DOC_ROOT);

$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);

$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);

//$smarty->display(DOC_ROOT.'/templates/lists/report-servicio.tpl');
	
?>