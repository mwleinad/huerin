<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "goToWorkflow":
			$_SESSION["search"]["rfc"] = $_POST["rfc"];
			$_SESSION["search"]["responsableCuenta"] = $_POST["responsableCuenta"];
			$_SESSION["search"]["status"] = $_POST["status"];
			$_SESSION["search"]["month"] = $_POST["month"];
			$_SESSION["search"]["year"] = $_POST["year"];
			$_SESSION["search"]["from"] = $_POST["from"];
			$_SESSION["search"]["atrasados"] = $_POST["atrasados"];
			echo "ok";
		break;

	case "search":
	case "sendEmail":
	case "graph":
			$year = $_POST['year'];
			$sql = "UPDATE instanciaServicio SET class = 'PorIniciar' 
							WHERE class = ''";
			$db->setQuery($sql);
			$db->UpdateData();

			$contracts = array();
			$subordinados = $personal->GetIdResponsablesSubordinados($_POST);
			$filter = $_POST;
			$filter['subordinados'] =  $subordinados;
			$filter['tipos'] = 'activos';
			$filter['like'] = $_POST['rfc'];
			$contracts = $contract->Suggest($filter, false, true);

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
			}

			$withIva =  false;
			if(isset($_POST['withIva']))
				$withIva = true;

			if($_POST['month'])
				$meses = [(int)$_POST['month']];
			else
				$meses = [1,2,3,4,5,6,7,8,9,10,11,12];


			$resClientes = array();
			foreach($clientes as $clte){
				$contratos = array();
				foreach($clte['contracts'] as $con) {
					$con['responsable'] = $con['responsables'][array_search(1, array_column($con['responsables'], 'departamentoId'))]['name'];
					$serv = array();
                    $statusColor =  $workflow->getRowCobranzaBono($con['contractId'], $year,'I',$meses,$withIva);
					$con['instanciasServicio'] =$statusColor['serv'];

					if($_POST['atrasados'] && $statusColor['noComplete'] > 0)
					{
						$contratos[] = $con;
					}
					elseif(!$_POST['atrasados'])
					{
						$contratos[] = $con;
					}
				}//foreach
				$clte['contracts'] = $contratos;
				$resClientes[] = $clte;
			}//foreach

			$cleanedArray = array();
			foreach($resClientes as $key => $cliente)
			{
				foreach($cliente["contracts"] as $keyContract => $contract)
				{
					$card["comentario"] = $contract["comentario"];
					$card["contractId"] = $contract["contractId"];
					$card["nameContact"] = $cliente["nameContact"];
					$card["responsable"] = $contract["responsable"];
					$card["name"] = $contract["name"];
					$card["instanciasServicio"] = $contract["instanciasServicio"];;
					//$card["nombreServicio"] = $servicio["nombreServicio"];;
					$cleanedArray[] = $card;
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
			//array de meses
            foreach($meses as $mes)
            	$mesesComplete[] = $monthsInt[$mes];

			$clientesMeses = array();
			$smarty->assign("cleanedArray", $cleanedArray);
			$smarty->assign("clientes", $resClientes);
			$smarty->assign("clientesMeses", $clientesMeses);
            $smarty->assign("mesesComplete", $mesesComplete);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-cobranza-new.tpl');

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
/*		echo "[#]";
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
		$smarty->display(DOC_ROOT.'/templates/lists/cxc.tpl');*/
		break;
	case 'doSendEmail':

			$email = $_POST['email'];
			$message = utf8_decode($_POST['msg']);
			$mensaje = utf8_decode($_POST['msj']);

			//Iniciando la clase mail
			$mail = new PHPMailer(true);

			$html = nl2br($mensaje);

			$msgHtml = '<h2 align="center">REPORTE DE OBLIGACIONES</h2>';
			$msgHtml .= '<p>&nbsp;</p>';
			$msgHtml .=  $message;

			//Adjuntamos el archivo PDF
			$dompdf = new DOMPDF();
			$dompdf->set_paper('letter');
			$dompdf->load_html($msgHtml);
			$dompdf->render();

			//Guardamos el archivo temporalmente
			$pdfoutput = $dompdf->output();
			$filename = DOC_ROOT.'/temp/reporte_obligaciones.pdf';
			$fp = fopen($filename, "a");
			fwrite($fp, $pdfoutput);
			fclose($fp);

			try {

				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->Host = SMTP_HOST;
				$mail->Username = SMTP_USER;
				$mail->Password = SMTP_PASS;
				$mail->Port = SMTP_PORT;

			 	$mail->AddAddress($email, '');
		 	  	$mail->SetFrom('no-reply@gmail.com', 'ROQUENI');
				$mail->Subject = 'Reporte de Obligaciones';
			  	$mail->MsgHTML($html);
			  	$mail->AddAttachment(DOC_ROOT.'/temp/reporte_obligaciones.pdf');      // attachment
			  	$mail->Send();

				$util->setError(10040, "complete");

			} catch (phpmailerException $e) {
				$util->setError(10018, "error");
			} catch (Exception $e) {
				$util->setError(10018, "error");
			}

			echo 'ok[#]';


			$util->PrintErrors();

			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');

		break;

	case 'getEmail':

			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/boxes/add-email-popup.tpl');

		break;
	case "editComentario":
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$contract->setContractId($_POST['contractId']);
		$myContract = $contract->Info();

		$smarty->assign("post", $myContract);
		$smarty->display(DOC_ROOT.'/templates/boxes/edit-comentario-contract-popup.tpl');
		break;
	case "saveEditComentario":
		print_r($_POST);
		$contract->setContractId($_POST['contractId']);
		$contract->UpdateComentario($_POST['comentario']);

			echo "ok[#]";
			$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
			echo "[#]";
			echo $_POST['comentario'];
		break;
}

?>
