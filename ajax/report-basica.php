<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{
	case "search":
	case "sendEmail":
	case "graph":
			if($_POST["rfc"] == "")
			{
				$_POST["cliente"] = 0;
			}
			$clientes = $customer->Enumerate("propio", $_POST["cliente"], 1);
			//print_r($clientes);
			$clientes = $workflow->EnumerateWorkflows($clientes, $_POST["month"], $_POST["year"]);

			$smarty->assign("clientes", $clientes);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			
			if($_POST["type"] == "graph")
			{
				//echo "create graph";
				include_once(DOC_ROOT."/libs/graph/graph.php");
				?>
        <div style="text-align:center; float:left"">
        <img src="<?php echo WEB_ROOT ?>/imagefile.png?<?php echo time()?>" />
        </div>
        <?php

				include_once(DOC_ROOT."/libs/graph/graphContract.php");
				?>
        <div style="text-align:left; float:left">
        <img src="<?php echo WEB_ROOT ?>/imagefile_contract.png?<?php echo time()?>" />
        </div>
        <?php

				include_once(DOC_ROOT."/libs/graph/graphServices.php");
				?>
        <div style="text-align:left; float:left">
        <img src="<?php echo WEB_ROOT ?>/imagefile_services.png?<?php echo time()?>" />
        </div>
        <?php

				include_once(DOC_ROOT."/libs/graph/graphSteps.php");
				?>
        <div style="text-align:left; float:left">
        <img src="<?php echo WEB_ROOT ?>/imagefile_steps.png?<?php echo time()?>" />
        </div>
        <?php

				$smarty->display(DOC_ROOT.'/templates/lists/report-obligaciones.tpl');
			}
			else
			{
				$smarty->display(DOC_ROOT.'/templates/lists/report-obligaciones.tpl');
			}
								
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
