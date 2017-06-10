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
		echo "ok";
		break;
	case "search":
	case "sendEmail":
	case "graph":
			
			global $infoUser;
						
			if(!$_POST["responsableCuenta"]){
				$page = "report-servicio";
			}
			
			$deep = ($_POST['deep']) ? "subordinado" : "propio";
					
			if($infoUser['departamentoId'] == "1"){
				if($_POST['departamentoId'])
					$filtroDepto = "tipoServicio.departamentoId='".$_POST['departamentoId']."' AND";
			}else{
				$filtroDepto = "tipoServicio.departamentoId='".$infoUser['departamentoId']."' AND";
			}

			$personal->setPersonalId($_POST["responsableCuenta"]);
			$myUser = $personal->Info();
			$roleId = $personal->GetRoleId($myUser["tipoPersonal"]);

			if($_POST["responsableCuenta"]){
				$User["roleId"] = $roleId;
				$User["departamentoId"] = $myUser["departamentoId"];
				$User["userId"] = $_POST["responsableCuenta"];
			}else{
				$User['userId'] = $_SESSION['User']['userId'];
			}

			if($_POST["rfc"] == ""){
				$_POST["cliente"] = 0;
			}

			$clientes = $customer->Enumerate($deep, $_POST["cliente"]);
					
      		$workflow->setTipoOperacion('reporteMensual');
			
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
						$nomPers = $personal->GetDataReport();
						
						$permisos[$idDepto] = $nomPers;
						$permisos2[$idDepto] = $idPersonal;
					}	
					
					$instServ = array();
					foreach($con['instanciasServicio'] as $serv){
												
						$deptoId = $serv['departamentoId'];
						$serv['responsable'] = $permisos[$deptoId];
						
						if($_POST['departamentoId'] && $_POST['departamentoId'] != $serv["departamentoId"])
							continue;
						
						$ii = $_POST["month"];
							
						$workfl = $workflow->StatusByMonth($serv["servicioId"], $ii , $_POST["year"]);
						$workflow->setInstanciaServicioId($workfl['instanciaServicioId']);
						$serv['instancias'][$ii] = $workflow->Info();
						
						$tempNumSteps = count($serv["instancias"][$ii]['steps']);
						
						if($tempNumSteps>$maxSteps){
							$maxSteps = $tempNumSteps;
						}
						
						$instServ[] = $serv;
						
					}//foreach
					$con['instanciasServicio'] = $instServ;
					
					
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
							$card["nameContact"] = $cliente["nameContact"];
							$card["tipoPersonal"] = $servicio["responsable"]["tipoPersonal"];
							$card["responsable"] = $servicio["responsable"]["name"];
							$card["name"] = $contract["name"];
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
			
			$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre",
			"Octubre","Noviembre","Diciembre");
			$smarty->assign("mes", $meses[$_POST['month']]);
			$clientesMeses = array();
			
			$smarty->assign("cleanedArray", $sortedArray);
			$smarty->assign("maxSteps", $maxSteps);
			$smarty->assign("clientes", $resClientes);
			$smarty->assign("clientesMeses", $clientesMeses);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-servicio-auditoria.tpl');
			
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
				
}

?>
