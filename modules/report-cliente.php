<?php

    $year = date('Y');
	$formValues['subordinados'] = false;
	$formValues['respCuenta'] = false;
	$formValues['departamentoId'] = false;
	$formValues['cliente'] = $_SESSION['User']["username"];
	$formValues['atrasados'] = false;

	//Actualizamos la clase del workflow, porque al generar los workflows la clase esta vacia (campo Class)
	$contracts = array();
    $contracts = $contract->BuscarContract($formValues, true);
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
	foreach($idClientes as $customerId){

		$customer->setCustomerId($customerId);
		$infC = $customer->Info();

		$infC['contracts'] = $contratosClte[$customerId];

		$clientes[] = $infC;

	}//foreach
	$resClientes = array();
	foreach($clientes as $clte){
		$contratos = array();
		foreach($clte['contracts'] as $con){
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
			$servicios = array();
			foreach($con['servicios'] as $serv){
                $isParcial =  false;
				$servicio->setServicioId($serv['servicioId']);
				$infServ = $servicio->Info();
				$noCompletados = 0;
                if($serv['servicioStatus']=='bajaParcial')
                    $isParcial = true;

                $serv['instancias'] = $instanciaServicio->getInstanciaByServicio($serv['servicioId'],$year,$serv['inicioOperaciones'],$isParcial);
                if(!$serv['instancias'])
                    continue;
                $atrasados = $instanciaServicio->getInstanciaAtrasado($serv['servicioId'],$year,$serv['inicioOperaciones'],$isParcial);
                $noCompletados = count($atrasados);

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

	/*$personalOrdenado = $personal->ArrayOrdenadoPersonal();
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
	}*/
	$smarty->assign("nameContact", $_SESSION["User"]["username"]);

	$clientesMeses = array();
	$smarty->assign("year", date("Y"));
	$smarty->assign("cleanedArray", $cleanedArray);
	$smarty->assign("clientes", $resClientes);
	$smarty->assign("clientesMeses", $clientesMeses);
	$smarty->assign("DOC_ROOT", DOC_ROOT);

    $departamentos = $departamentos->Enumerate();
    $smarty->assign("departamentos", $departamentos);

    $personals = $personal->Enumerate();
    $smarty->assign("personals", $personals);

?>