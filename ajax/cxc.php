<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');

	switch($_POST['type']){

	case "saveAddPayment":

			if(!$cxc->AddPayment($_POST["comprobanteId"], $_POST["metodoDePago"], $_POST["amount"]))
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{

				$id_comprobante = $_POST['comprobanteId'];

				$compInfo = $comprobante->GetInfoComprobante($id_comprobante);
				$user->setUserId($compInfo['userId'],1);
				$usr = $user->GetUserInfo();
				$nomRfc = $usr['rfc'];

				$serie = $compInfo['serie'];
				$folio = $compInfo['folio'];

	//			print_r($compInfo);
				$smarty->assign('id_comprobante', $id_comprobante);
				$smarty->assign('post', $compInfo);
				$smarty->assign('rfc', $nomRfc);
				$smarty->assign('serie', $serie);
				$smarty->assign('folio', $folio);
				$smarty->assign('DOC_ROOT', DOC_ROOT);

				$info = $user->Info();
				$smarty->assign("info", $info);
				echo 'ok[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				echo number_format($compInfo["saldo"],2);
				echo "[#]";
				foreach($_POST as $key => $val){
					$values[$key] = $val;
				}

				$comprobantes = array();
				$comprobantes = $cxc->SearchCuentasPorCobrar($values);
				$smarty->assign('comprobantes',$comprobantes);

				$total = 0;
				if($comprobantes["items"])
				{
					foreach($comprobantes["items"] as $res){
						if($res["tipoDeComprobante"] == "ingreso")
						{
							$total += $res['total'];
							$payments += $res['payment'];
							$saldo += $res['saldo'];
						}
					}
				}

				$smarty->assign('totalFacturas',$totalFacturas);
				$smarty->assign('total',$total);
				$smarty->assign('payments',$payments);
				$smarty->assign('saldo',$saldo);

				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/cxc.tpl');
				echo "[#]";
				echo number_format($usr["cxcSaldoFavor"], 2);
			}
		break;
	case "deletePayment":
			$payment = $cxc->PaymentInfo($_POST["id"]);

			$cxc->DeletePayment($_POST["id"]);

			$id_comprobante = $payment['comprobanteId'];

			$compInfo = $comprobante->GetInfoComprobante($id_comprobante);
			$user->setUserId($compInfo['userId'],1);
			$usr = $user->GetUserInfo();
			$nomRfc = $usr['rfc'];

			$serie = $compInfo['serie'];
			$folio = $compInfo['folio'];

//			print_r($compInfo);
			$smarty->assign('id_comprobante', $id_comprobante);
			$smarty->assign('post', $compInfo);
			$smarty->assign('rfc', $nomRfc);
			$smarty->assign('serie', $serie);
			$smarty->assign('folio', $folio);
			$smarty->assign('DOC_ROOT', DOC_ROOT);

			$info = $user->Info();
			$smarty->assign("info", $info);
			echo 'ok[#]';
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			$smarty->display(DOC_ROOT.'/templates/lists/payments.tpl');
			echo "[#]";
			echo number_format($compInfo["saldo"],2);
			echo "[#]";
/*				foreach($_POST as $key => $val){
					$values[$key] = $val;
				}


				$comprobantes = array();
				$comprobantes = $cxc->SearchCuentasPorCobrar($values);
				$smarty->assign('comprobantes',$comprobantes);

				$total = 0;
				if($comprobantes["items"])
				{
					foreach($comprobantes["items"] as $res){
						if($res["tipoDeComprobante"] == "ingreso")
						{
							$total += $res['total'];
							$payments += $res['payment'];
							$saldo += $res['saldo'];
						}
					}
				}

				$smarty->assign('totalFacturas',$totalFacturas);
				$smarty->assign('total',$total);
				$smarty->assign('payments',$payments);
				$smarty->assign('saldo',$saldo);

				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/cxc.tpl');*/
		break;

	case "saveEditCxC":
			if(!$cxc->Edit($_POST["comprobanteId"], $_POST["cxcDiscount"]))
			{
				echo "fail[#]";
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			}
			else
			{
				foreach($_POST as $key => $val){
					$values[$key] = $val;
				}

				$comprobantes = array();
				$comprobantes = $cxc->SearchCuentasPorCobrar($values);
				$smarty->assign('comprobantes',$comprobantes);

				$total = 0;
				echo 'ok[#]';
				$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
				echo "[#]";
				if($comprobantes["items"])
				{
					foreach($comprobantes["items"] as $res){
						if($res["tipoDeComprobante"] == "ingreso")
						{
							$total += $res['total'];
							$payments += $res['payment'];
							$saldo += $res['saldo'];
						}
					}
				}

				$smarty->assign('totalFacturas',$totalFacturas);
				$smarty->assign('total',$total);
				$smarty->assign('payments',$payments);
				$smarty->assign('saldo',$saldo);

				$smarty->assign('DOC_ROOT', DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/cxc.tpl');
			}

		break;

		case 'search':

			echo 'ok[#]';
			$year = $_POST['year'];
			
			$formValues['subordinados'] = $_POST['deep'];			
			$formValues['respCuenta'] = $_POST['responsableCuenta'];
			$formValues['departamentoId'] = $_POST["departamentoId"];
			$formValues['cliente'] = $_POST["rfc"];
            $formValues['sinServicios'] = true;
            $formValues['activos'] = true;
			
			//Actualizamos la clase del workflow, porque al generar los workflows la clase esta vacia (campo Class)
			$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' WHERE class = ''";
			$db->setQuery($sql);
			$db->UpdateData();
						
			$contracts = array();
			include(DOC_ROOT."/ajax/filterOnlyContract.php");
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
			
			$values['departamentoId'] = $_POST["departamentoId"];
			$values['anio'] = $_POST["year"];
			$values['nombre'] = $_POST['rfc'];
			$values['facturador'] = $_POST['facturador'];
			$values['respCuenta'] = $_POST['responsableCuenta'];
			$values['subordinados'] = $_POST['deep'];
			$values['cliente'] = $_POST['cliente'];
            $values['month'] = $_POST['month'];
			$_SESSION['cxc'] = $values;
			$comprobantes = array();
			$comprobantes = $cxc->SearchCuentasPorCobrarNoReporte($contratosClte, $values);
			$total = 0;
			$items = array();
			if($comprobantes["items"])
			{
				foreach($comprobantes["items"] as $res){
					if($res['facturador'] != 'Efectivo'){
						if($res["tipoDeComprobante"] == "ingreso" && $res["status"] == 1)
						{
							$total += $res['total'];
							$payments += $res['payment'];
							$saldo += $res['saldo'];
						}
					}else{
						$total += $res['total'];
						$payments += $res['payment'];
						$saldo += $res['saldo'];
					}
					$items[] = $res;
				}
			}

			$comprobantes['items'] = $items;
			$smarty->assign('comprobantes',$comprobantes);
			$smarty->assign('totalFacturas',$totalFacturas);
			$smarty->assign('total',$total);
			$smarty->assign('payments',$payments);
			$smarty->assign('saldo',$saldo);
			echo '[#]';
			$smarty->assign('DOC_ROOT', DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/cxc.tpl');
		break;

		case 'editCxC':

			$id_comprobante = $_POST['id'];

			$compInfo = $comprobante->GetInfoComprobante($id_comprobante);
			$user->setUserId($compInfo['userId'],1);
			$usr = $user->GetUserInfo();
			$nomRfc = $usr['rfc'];

			$serie = $compInfo['serie'];
			$folio = $compInfo['folio'];

			$smarty->assign('id_comprobante', $id_comprobante);
			$smarty->assign('post', $compInfo);
			$smarty->assign('rfc', $nomRfc);
			$smarty->assign('serie', $serie);
			$smarty->assign('folio', $folio);
			$smarty->assign('DOC_ROOT', DOC_ROOT);

			$info = $user->Info();
			$smarty->assign("info", $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/edit-cxc-popup.tpl');

			break;

		case 'paymentDetails':

			$id_comprobante = $_POST['id'];

			if($_POST['efectivo']=="ok")
			$efectivo=$_POST['efectivo'];

			$compInfo = $comprobante->GetInfoComprobante($id_comprobante,$efectivo);
			if($efectivo)
			{$user->setUserId($compInfo['contractId'],1);}
			else
			{$user->setUserId($compInfo['userId'],1);}

			$usr = $user->GetUserInfo();
			$nomRfc = $usr['rfc'];

			if($efectivo)
			{
			$serie = "E";
			$folio = $compInfo['instanciaServicioId'];
			}
			else
			{
			$serie = $compInfo['serie'];
			$folio = $compInfo['folio'];
			}

//			print_r($compInfo);
			$smarty->assign('id_comprobante', $id_comprobante);
			$smarty->assign('post', $compInfo);
			$smarty->assign('rfc', $nomRfc);
			$smarty->assign('serie', $serie);
			$smarty->assign('folio', $folio);
			$smarty->assign('DOC_ROOT', DOC_ROOT);

			$info = $user->Info();
			$smarty->assign("info", $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/details-cxc-popup.tpl');

			break;
		case 'addPayment':

			$id_comprobante = $_POST['id'];

			$compInfo = $comprobante->GetInfoComprobante($id_comprobante);
			$user->setUserId($compInfo['userId'],1);
			$usr = $user->GetUserInfo();
			$nomRfc = $usr['rfc'];

			$serie = $compInfo['serie'];
			$folio = $compInfo['folio'];

//			print_r($compInfo);
			$smarty->assign('id_comprobante', $id_comprobante);
			$smarty->assign('post', $compInfo);
			$smarty->assign('usr', $usr);
			$smarty->assign('rfc', $nomRfc);
			$smarty->assign('serie', $serie);
			$smarty->assign('folio', $folio);
			$smarty->assign('DOC_ROOT', DOC_ROOT);

			$info = $user->Info();
			$smarty->assign("info", $info);
			$smarty->display(DOC_ROOT.'/templates/boxes/payment-cxc-popup.tpl');

			break;

	}//switch

?>
