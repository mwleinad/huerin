<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	$user->allowAccess('customer');

	extract($_POST);
	$period = $_POST['period'];
			$year = $_POST['year'];

			$formValues['subordinados'] = $_POST['deep'];
			$formValues['respCuenta'] = $_POST['responsableCuenta'];
			$formValues['departamentoId'] = $_POST["departamentoId"];
			$formValues['cliente'] = $_POST["rfc"];

			//Actualizamos la clase del workflow, porque al generar los workflows la clase esta vacia (campo Class)

			$sql = "UPDATE instanciaServicio SET class = 'PorIniciar'
					WHERE class = ''";
			$db->setQuery($sql);
			$db->UpdateData();

			$contracts = array();
			if($User['tipoPersonal'] == 'Asistente' || $User['tipoPersonal'] == 'Socio' || $User['tipoPersonal'] == 'Gerente'){

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
						$nomPers = $personal->GetNameById();

						$permisos[$idDepto] = $nomPers;
						$permisos2[$idDepto] = $idPersonal;
					}

					//$personal->setPersonalId($con['responsableCuenta']);
					//$con['responsable'] = $personal->Info();

					$servicios = array();
					foreach($con['servicios'] as $serv){

						$servicio->setServicioId($serv['servicioId']);
						$infServ = $servicio->Info();

						$sumaTotal = 0;

						if($period == "efm"){
							for($ii = 1; $ii <= 3; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

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
								if($statusColor['class'] == "Completo" || $statusColor['class'] == "CompletoTardio"){
									$sumaTotal += $infServ['costo'];
								}
							}
						}elseif($period == "amj"){
							for($ii = 4; $ii <= 6; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

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
								if($statusColor['class'] == "Completo" || $statusColor['class'] == "CompletoTardio"){
									$sumaTotal += $infServ['costo'];
								}
							}
						}elseif($period == "jas"){
							for($ii = 7; $ii <= 9; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

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
								if($statusColor['class'] == "Completo" || $statusColor['class'] == "CompletoTardio"){
									$sumaTotal += $infServ['costo'];
								}
							}
						}elseif($period == "ond"){
							for($ii = 10; $ii <= 12; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

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
								if($statusColor['class'] == "Completo" || $statusColor['class'] == "CompletoTardio"){
									$sumaTotal += $infServ['costo'];
								}
							}
						}else{
							for($ii = 1; $ii <= 12; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , date('Y'));

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
								if($statusColor['class'] == "Completo" || $statusColor['class'] == "CompletoTardio"){
									$sumaTotal += $infServ['costo'];
								}
							}
						}
						/*for($ii = 1; $ii <= 12; $ii++){
							$statusColor = $workflow->StatusByMonth($serv['servicioId'], $ii , $year);

							//Si es Servicio de Domicilio Fiscal, que no lleve colores
							if($statusColor['tipoServicioId'] == 16)
								$statusColor['class'] = '';

							$serv['instancias'][$ii] = $statusColor;
						}*/

						$serv['sumatotal'] = $sumaTotal;
						$serv['costo'] = $infServ['costo'];

						$tipoServicio->setTipoServicioId($infServ['tipoServicioId']);
						$deptoId = $tipoServicio->GetField('departamentoId');

						$serv['responsable'] = $permisos[$deptoId];

						$servicios[] = $serv;

					}//foreach

					$con['instanciasServicio'] = $servicios;
					$contratos[] = $con;

				}//foreach

				$clte['contracts'] = $contratos;

				$resClientes[] = $clte;
			//echo serialize($resClientes)." {} ";
			}//foreach
			//die();
			$alfabeto = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,Ñ,O,P,Q,R,S,T,U,V,W,X,Y,Z";
			$abcdario = explode(",", $alfabeto);

			$filtroOrden = $_POST['ordenAZ'];
			if (count($resClientes) > 0) {
				foreach ($abcdario as $keyLetra => $letra) {
					foreach ($resClientes as $key1 => $row1) {
						foreach ($resClientes[$key1]['contracts'] as $key2 => $row2) {
							foreach ($resClientes[$key1]['contracts'][$key2]['instanciasServicio'] as $key3 => $row3) {
								if ($filtroOrden == "C. Asignado") {
									$letraInicialFiltro = strtoupper($resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['responsable'][0]);
								}elseif ($filtroOrden == "Cliente") {
									$letraInicialFiltro = strtoupper($resClientes[$key1]['nameContact'][0]);
								}elseif ($filtroOrden == "Razon Social") {
									$letraInicialFiltro = strtoupper($resClientes[$key1]['contracts'][$key2]['name'][0]);
								}
								if($letraInicialFiltro == $letra){
									$resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['TIPO_ORDEN'] = $filtroOrden;
									$resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['LETRA'] = $letra;
									$resClientes[$key1]['contracts'][$key2]['instanciasServicio'][$key3]['POCICION'] = $keyLetra;
								}
							}
						}
					}
				}
			}
			// echo "<pre>";
			// print_r($resClientes);
			// echo "</pre>";
			// exit;

			if($period == "efm"){
				$monthNames = array("Ene", "Feb", "Mar");
			}elseif($period == "amj"){
				$monthNames = array("Abr", "May", "Jun");
			}elseif($period == "jas"){
				$monthNames = array("Jul", "Ago", "Sep");
			}elseif($period == "ond"){
				$monthNames = array("Oct", "Nov", "Dic");
			}else{
				$monthNames = array("Ene", "Feb", "Mar","Abr", "May", "Jun","Jul", "Ago", "Sep","Oct", "Nov", "Dic");
			}


			$html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						border-collapse: collapse;
					}
				</style>
			</head>
			';

			$clientesMeses = array();
			$smarty->assign("abcdario", $abcdario);
			$smarty->assign("nombreMeses", $monthNames);
			$smarty->assign("clientes", $resClientes);
			$smarty->assign("clientesMeses", $clientesMeses);
			$smarty->assign("EXEL", "SI");
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$html .= $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio-bono.tpl');



	$name = 'Clientes_con_Razones_Sociales';
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$name.".xls");
	header("Pragma: no-cache");
	echo "\xEF\xBB\xBF";
	header("Expires: 0");

	echo $html;


	exit;

?>