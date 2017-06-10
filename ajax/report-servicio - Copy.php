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
			//print_r($_POST);
			if($_POST["rfc"] == "")
			{
				$_POST["cliente"] = 0;
			}
			echo "<pre>";
			$clientes = $customer->Enumerate("subordinado", $_POST["cliente"]);

			foreach($clientes as $key => $cliente)
			{
				foreach($cliente["contracts"] as $keyContract => $contract)
				{
					print_r($contract);
/*					$clientes[$key]["contracts"][$keyContract]["instanciasServicio"] = $servicio->EnumerateActiveOnlyNames($contract["contractId"]);
					
					foreach($clientes[$key]["contracts"][$keyContract]["instanciasServicio"] as $keyInstancia => $instancia)
					{
						for($ii = 1; $ii <= 12; $ii++)
						{
							$clientes[$key]["contracts"][$keyContract]["instanciasServicio"][$keyInstancia]["instancias"][$ii] = $workflow->StatusByMonth($instancia["servicioId"], $ii , $_POST["year"]);
						}
					}
*/				}
			}

			$clientesMeses = array();
			$smarty->assign("clientes", $clientes);
			$smarty->assign("clientesMeses", $clientesMeses);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			
			$smarty->display(DOC_ROOT.'/templates/lists/report-servicio.tpl');
								
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
