<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	$user->allowAccess(164);

	extract($_POST);
	$period = $_POST['period'];
			$year = $_POST['year'];
            $mesesBase = array(0=>array(),1=>array(),2=>array());
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
            include_once(DOC_ROOT.'/ajax/filter.php');
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
                        $isParcial =  false;
                        if($serv['servicioStatus']=="bajaParcial")
                            $isParcial=true;
                        switch($period)
                        {
                            case 'efm':
                                $meses = array(1,2,3);
                                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);
                                $sumaTotal = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                break;
                            case 'amj':
                                $meses = array(4,5,6);
                                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);
                                $sumaTotal = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                break;
                            case 'jas':
                                $meses = array(7,8,9);
                                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);
                                $sumaTotal = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                break;
                            case 'ond':
                                $meses = array(10,11,12);
                                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);
                                $sumaTotal = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                break;
                            default:
                                $meses = array(1,2,3,4,5,6,7,8,9,10,11,12);
                                $temp = $instanciaServicio->getBonoTrimestre($serv['servicioId'],$year,$meses);
                                $serv['instancias'] = array_replace_recursive($mesesBase,$temp);
                                $sumaTotal = $instanciaServicio->getSumaBonoTrimestre($serv['servicioId'],$year,$meses,$serv['inicioOperaciones'],$isParcial);
                                break;
                        }
                        if(empty($temp))
                            continue;
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



	$name = 'reporte_de_bonos';
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$name.".xls");
	header("Pragma: no-cache");
	echo "\xEF\xBB\xBF";
	header("Expires: 0");

	echo $html;


	exit;

?>