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
					foreach($con['servicios'] as $serv){
						
						//PERSONAL
						
						$sql = 'SELECT tipoServ.departamentoId, tipoServ.costoVisual FROM servicio serv
								LEFT JOIN tipoServicio tipoServ ON tipoServ.tipoServicioId = serv.tipoServicioId
								WHERE serv.servicioId = "'.$serv['servicioId'].'"';
						$util->DB()->setQuery($sql);
						$rowTipoServ = $util->DB()->GetRow();
						$departamentoId = $rowTipoServ["departamentoId"];
						$costoVisual = $rowTipoServ["costoVisual"];
						
						$resPermisos = explode('-',$con['permisos']);
						
						$personalId = 0;
						foreach($resPermisos as $res2){
							$value = explode(',',$res2);
							
							$idPersonal = $value[1];
							$idDepto = $value[0];
							
							if($idDepto == $departamentoId)
								$personalId = $idPersonal;
							
						}
						
						$personal->setPersonalId($personalId);		
						$infP = $personal->Info();
						
						if($infP['tipoPersonal'] == 'Gerente' || $infP['tipoPersonal'] == 'Socio'){
							$serv['gerente'] = $infP['name'];
						}elseif($infP['tipoPersonal'] == 'Supervisor'){
						
							$serv['supervisor'] = $infP['name'];
							
							$personal->setPersonalId($infP['jefeInmediato']);
							$serv['gerente'] = $personal->GetNameById();
							
						}
                        elseif($infP['tipoPersonal'] == 'Asistente'){
                            $serv['auxiliar'] = $infP['name'];

                            $personal->setPersonalId($infP['jefeInmediato']);
                            $jGer = $personal->Info();
                            $personal->setPersonalId($jGer['personalId']);
                            $serv['gerente'] = $personal->GetNameById();

                        }elseif($infP['tipoPersonal'] == 'Contador'){
						
							$serv['contador'] = $infP['name'];

							$personal->setPersonalId($infP['jefeInmediato']);
                            $jSup = $personal->Info();
							$personal->setPersonalId($jSup['personalId']);
							$serv['supervisor'] = $personal->GetNameById();
							
							$personal->setPersonalId($jSup['jefeInmediato']);
							$serv['gerente'] = $personal->GetNameById();
							
						}elseif($infP['tipoPersonal'] == 'Auxiliar'){
						
							$serv['auxiliar'] = $infP['name'];

                            $contadorId = $infP['jefeInmediato']==0?$infP['personalId']:$infP['jefeInmediato'];
                            $personal->setPersonalId($contadorId);
                            $jCont = $personal->Info();

							$personal->setPersonalId($jCont['personalId']);
							$serv['contador'] = $personal->GetNameById();

                            $supervisorId = $jCont['jefeInmediato']==0?$jCont['personalId']:$jCont['jefeInmediato'];
                            $personal->setPersonalId($supervisorId);
                            $jSup = $personal->Info();

							$personal->setPersonalId($jSup['personalId']);
							$serv['supervisor'] = $personal->GetNameById();

                            $gerenteId = $jSup['jefeInmediato']==0?$jSup['personalId']:$jSup['jefeInmediato'];
                            $personal->setPersonalId($gerenteId);
                            $jGer = $personal->Info();

							$personal->setPersonalId($jGer['personalId']);
							$serv['gerente'] = $personal->GetNameById();

						}elseif(empty($infP)){
                            $serv['auxiliar'] = 'N/E';
                            $serv['contador'] = 'N/E';
                            $serv['supervisor'] = 'N/E';
                            $serv['gerente'] = 'N/E';
                        }
						
						//END PERSONAL
						
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
}

?>