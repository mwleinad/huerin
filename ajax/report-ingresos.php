<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
switch($_POST["type"])
{
	case 'search':
			echo 'ok[#]';
			$formValues['subordinados'] = $_POST['subordinados'];			
			$formValues['respCuenta'] = $_POST['responsableCuenta'];
			$formValues['departamentoId'] = $_POST["departamentoId"];
			$formValues['cliente'] = $_POST["rfc"];
			$idContrato = $_POST['contractId'];
			$contracts = array();
			//este archivo contiene los filtros segun sea el rol, se usa en varios apartado cambiarlos por este archivo
            include_once(DOC_ROOT.'/ajax/filter.php');
			$idClientes = array();
			$idContracts = array();
			$contratosClte = array();
			foreach($contracts as $res){
								
				$contractId = $res['contractId'];
				$customerId = $res['customerId'];
				
				if($idContrato > 0 && $contractId != $idContrato)
					continue;
				
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
			
			$totalPeriodo = 0;
			$totalCosto = 0;
			$resClientes = array();
			foreach($clientes as $clte){
				
				$contratos = array();
				foreach($clte['contracts'] as $con){
										
					//Checamos Permisos
					$resPermisos = explode('-',$con['permisos']);
					foreach($resPermisos as $res){
						$value = explode(',',$res);
						
						$idPersonal = $value[1];
						$idDepto = $value[0];
						
						$personal->setPersonalId($idPersonal);
						$nomPers = $personal->GetNameById();
						
						$permisos[$idDepto] = $nomPers;
						$permisos2[$idDepto] = $idPersonal;
					}	
										
					$servicios = array();
					foreach($con['servicios'] as $serv) {

                        //PERSONAL

                        $sql = 'SELECT tipoServ.departamentoId, tipoServ.costoVisual FROM servicio serv
								LEFT JOIN tipoServicio tipoServ ON tipoServ.tipoServicioId = serv.tipoServicioId
								WHERE serv.servicioId = "' . $serv['servicioId'] . '"';
                        $util->DB()->setQuery($sql);
                        $rowTipoServ = $util->DB()->GetRow();
                        $departamentoId = $rowTipoServ["departamentoId"];
                        $costoVisual = $rowTipoServ["costoVisual"];

                        $resPermisos = explode('-', $con['permisos']);

                        $personalId = 0;
                        foreach ($resPermisos as $res2) {
                            $value = explode(',', $res2);

                            $idPersonal = $value[1];
                            $idDepto = $value[0];

                            if ($idDepto == $departamentoId)
                                $personalId = $idPersonal;

                        }

                        $personal->setPersonalId($personalId);
                        $infP = $personal->Info();
                        $role = $rol->getInfoByData($infP);
                        $rolArray = explode(' ',$role['name']);
                        $needle = trim($rolArray[0]);
                        switch($needle){
                            case 'Sistemas':
                            case 'Gestoria':
                            case 'Supervisor':
                                $needle='supervisor';
                                break;
                            case 'Asistente':
                            case 'Cuentas':
                            case 'Contador':
                                $needle='contador';
                                break;
                            case 'Recepcion':
                            case 'Auxiliar':
                                $needle='auxiliar';
                                break;
                            case 'Coordinador':
                            case 'Socio':
                                $needle='socio';
                                break;
                            case 'Gerente':
                                $needle='gerente';
                                break;
                        }
                        if(!empty($infP)){
                            $jefes = array();
                            $personal->findDeepJefes($personalId, $jefes,true);
                            $serv['contador'] = $jefes['Contador'];
                            $serv['supervisor'] = $jefes['Supervisor'];
                            $serv['gerente'] = $jefes['Gerente'];
                            $serv['jefeMax'] = $jefes['Socio'];
                            $serv[$needle] = $jefes['me'];
                        }else {
                            $serv['auxiliar'] = 'N/E';
                            $serv['contador'] = 'N/E';
                            $serv['supervisor'] = 'N/E';
                            $serv['gerente'] = 'N/E';
                        }
						$servicio->setServicioId($serv['servicioId']);
						$infServ = $servicio->Info();
																		
						$tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
						$deptoId = $tipoServicio->GetField('departamentoId');
						
						$serv['responsable'] = $permisos[$deptoId];
						$serv['costo'] = number_format($infServ['costo'],2,'.','');
						
						if($serv['periodicidad'] == 'Mensual')
							$costoMens = $infServ['costo'];
						elseif($serv['periodicidad'] == 'Anual')
							$costoMens = $infServ['costo'] / 12;
						elseif($serv['periodicidad'] == 'Bimestral')
							$costoMens = $infServ['costo'] / 2;
							
						$costoMens = number_format($costoMens,2,'.','');
						$serv['costoMens'] = number_format($costoMens,2);
						$serv['costoVisual'] = number_format($costoVisual,2);
						
						$totalPeriodo += $serv['costo'];
						$totalMens += $costoMens;
						
						$servicios[] = $serv;
						
					}//foreach
					$con['instanciasServicio'] = $servicios;
																				
					$contratos[] = $con;
					
				}//foreach
				$clte['contracts'] = $contratos;
				
				$resClientes[] = $clte;
				
			}//foreach
			$smarty->assign('totalMens', $totalMens);
			$smarty->assign('totalPeriodo', $totalPeriodo);
			$smarty->assign("clientes", $resClientes);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-ingresos.tpl');
			
		break;
    case 'searchAltasBajas':
        echo 'ok[#]';
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['departamentoId'] = $_POST["departamentoId"];
        $formValues['statusSearch'] = $_POST["statusSearch"];
        $formValues['tipoSearch'] = $_POST["tipoSearch"];
        $formValues['cliente'] = $_POST["rfc"];
        $year = $_POST["year"];
        $idContrato = $_POST['contractId'];
        $contracts = array();
        //este archivo contiene los filtros segun sea el rol, se usa en varios apartado cambiarlos por este archivo
        include_once(DOC_ROOT.'/ajax/filter.php');
        $idClientes = array();
        $idContracts = array();
        $contratosClte = array();
        foreach($contracts as $res){
            $contractId = $res['contractId'];
            $customerId = $res['customerId'];
            if($idContrato > 0 && $contractId != $idContrato)
                continue;

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
        $totalPeriodo = 0;
        $totalCosto = 0;
        $resClientes = array();
        foreach($clientes as $clte){
            $contratos = array();
            foreach($clte['contracts'] as $con){
                switch($_POST["tipoSearch"]) {
                    case 'contract':
                        $conId = $con["contractId"];
                        $db->setQuery("select DATE(fecha) from contractChanges where contractId='$conId' order by contractChangesId ASC limit 1");
                        $con["fechaAlta"] = $db->GetSingle();
                        if (!$util->isValidateDate($con["fechaAlta"], "Y-m-d"))
                            continue 2;

                        $db->setQuery("select DATE(fecha) from contractChanges where contractId='$conId' and activo='No' order by contractChangesId ASC limit 1");
                        $con["fechaBaja"] = $db->GetSingle();

                        switch ($_POST["statusSearch"]) {
                            case 'activo':
                                $firstExplode = explode("-", $con["fechaAlta"]);
                                $mes = (int)$_POST["month"];
                                if ($mes > 0) {
                                    if ($mes != (int)$firstExplode[1])
                                        continue 2;
                                }
                                if ((int)$year > 0) {
                                    if ((int)$_POST["year"] != (int)$firstExplode[0])
                                        continue 2;
                                }
                            break;
                            case 'baja':
                                if (!$util->isValidateDate($con["fechaBaja"], "Y-m-d"))
                                    continue 2;

                                $lastExplode = explode("-", $con["fechaBaja"]);
                                $mes = (int)$_POST["month"];
                                if ($mes > 0) {
                                    if ($mes != (int)$lastExplode[1])
                                        continue 2;
                                }
                                if ((int)$year > 0) {
                                    if ((int)$_POST["year"] != (int)$lastExplode[0])
                                        continue 2;
                                }
                            break;
                        }
                        $contratos[] = $con;

                    break;
                    case 'service':
                        $encargados = $contractRep->encargadosCustomKey('departamentoId', 'name', $con['contractId']);
                        $encargados2 = $contractRep->encargadosCustomKey('departamentoId', 'personalId', $con['contractId']);
                        $servicios = array();
                        foreach ($con['servicios'] as $serv) {
                            $servId = $serv["servicioId"];
                            //encontrar fech alta
                            $db->setQuery("select DATE(fecha) from historyChanges where servicioId='$servId' order by historyChangesId ASC limit 1");
                            $serv["fechaAlta"] = $db->GetSingle();
                            if (!$util->isValidateDate($serv["fechaAlta"], "Y-m-d"))
                                continue;

                            //encontrar fecha de baja
                            switch ($serv["status"]) {
                                case 'activo':
                                case 'readonly':
                                    $serv["movimiento"] = "Alta";
                                    break;
                                case 'baja':
                                    $serv["movimiento"] = "Baja";
                                    if (!$util->isValidateDate($serv['fechaBaja'], "Y-m-d")) {
                                        $db->setQuery("select max(date) from instanciaServicio where servicioId='" . $serv['servicioId'] . "' ");
                                        $serv["fechaBaja"] = $db->GetSingle();
                                    }
                                    break;
                                case 'bajaParcial':
                                    $serv["movimiento"] = "Baja";
                                    if (!$util->isValidateDate($serv['lastDateWorkflow'], "Y-m-d")) {
                                        $db->setQuery("select max(date) from instanciaServicio where servicioId='" . $serv['servicioId'] . "' ");
                                        $serv["fechaBaja"] = $db->GetSingle();
                                    } else
                                        $serv["fechaBaja"] = $serv['lastDateWorkflow'];
                                    break;

                            }
                            switch ($_POST["statusSearch"]) {
                                case 'activo':
                                    $firstExplode = explode("-", $serv["fechaAlta"]);
                                    $mes = (int)$_POST["month"];
                                    if ($mes > 0) {
                                        if ($mes != (int)$firstExplode[1])
                                            continue 2;
                                    }
                                    if ((int)$year > 0) {
                                        if ((int)$_POST["year"] != (int)$firstExplode[0])
                                            continue 2;
                                    }
                                    break;
                                case 'baja':
                                    $lastExplode = explode("-", $serv["fechaBaja"]);
                                    $mes = (int)$_POST["month"];
                                    if ($mes > 0) {
                                        if ($mes != (int)$lastExplode[1])
                                            continue 2;
                                    }
                                    if ((int)$year > 0) {
                                        if ((int)$_POST["year"] != (int)$lastExplode[0])
                                            continue 2;
                                    }
                                    break;
                            }
                            $departamentoId = $serv["departamentoId"];
                            $costoVisual = $serv["costoVisual"];
                            $personalId = $encargados2[$departamentoId];
                            $personal->setPersonalId($personalId);
                            $infP = $personal->Info();
                            $role = $rol->getInfoByData($infP);
                            $rolArray = explode(' ', $role['name']);
                            $needle = trim($rolArray[0]);
                            switch ($needle) {
                                case 'Sistemas':
                                case 'Gestoria':
                                case 'Supervisor':
                                    $needle = 'supervisor';
                                    break;
                                case 'Asistente':
                                case 'Cuentas':
                                case 'Contador':
                                    $needle = 'contador';
                                    break;
                                case 'Recepcion':
                                case 'Auxiliar':
                                    $needle = 'auxiliar';
                                    break;
                                case 'Coordinador':
                                case 'Socio':
                                    $needle = 'socio';
                                    break;
                                case 'Gerente':
                                    $needle = 'gerente';
                                    break;
                            }
                            if (!empty($infP)) {
                                $jefes = array();
                                $personal->findDeepJefes($personalId, $jefes, true);
                                $serv['contador'] = $jefes['Contador'];
                                $serv['supervisor'] = $jefes['Supervisor'];
                                $serv['gerente'] = $jefes['Gerente'];
                                $serv['jefeMax'] = $jefes['Socio'];
                                $serv[$needle] = $jefes['me'];
                            } else {
                                $serv['auxiliar'] = 'N/E';
                                $serv['contador'] = 'N/E';
                                $serv['supervisor'] = 'N/E';
                                $serv['gerente'] = 'N/E';
                            }
                            $serv['responsable'] = $encargados[$departamentoId];
                            $serv['costo'] = number_format($serv['costo'], 2, '.', '');

                            if ($serv['periodicidad'] == 'Mensual')
                                $costoMens = $serv['costo'];
                            elseif ($serv['periodicidad'] == 'Anual')
                                $costoMens = $serv['costo'] / 12;
                            elseif ($serv['periodicidad'] == 'Bimestral')
                                $costoMens = $serv['costo'] / 2;

                            $costoMens = number_format($costoMens, 2, '.', '');
                            $serv['costoMens'] = number_format($costoMens, 2);
                            $serv['costoVisual'] = number_format($costoVisual, 2);
                            $totalPeriodo += $serv['costo'];
                            $totalMens += $costoMens;
                            $servicios[] = $serv;
                        }//foreach
                        $con['instanciasServicio'] = $servicios;
                        if (count($servicios) > 0)
                            $contratos[] = $con;
                    break;
                }
            }//foreach
            $clte['contracts'] = $contratos;
            if (count($contratos) > 0)
                $contratos[] = $con;
            $resClientes[] = $clte;

        }//foreach
        $smarty->assign('totalMens', $totalMens);
        $smarty->assign('totalPeriodo', $totalPeriodo);
        $smarty->assign("clientes", $resClientes);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        switch($_POST["tipoSearch"]){
            case 'contract':
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down-contract.tpl');
            break;
            case 'service':
                $smarty->display(DOC_ROOT.'/templates/lists/report-up-down.tpl');
            break;
        }

        break;
}

?>