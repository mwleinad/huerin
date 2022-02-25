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
			$values['nombre'] = $_POST['rfc'];
			$values['facturador'] = $_POST['facturador'];
			$values['respCuenta'] = $_POST['responsableCuenta'];
			$values['subordinados'] = $_POST['deep'];
			$values['cliente'] = $_POST['cliente'];
			$values['year'] = $_POST['year'];
			//si subordinados esta activo se busca todos los subordinados
			$encargados = array();
			$empleados = array();
			if($values['respCuenta'] == 0) {
                $personal->setActive(1);
                $empleados = $personal->ListAll();
                $empleados = $util->ConvertToLineal($empleados, 'personalId');
            }else{
                array_push($encargados,$values['respCuenta']);
                if($values['subordinados']){
                    $personal->setPersonalId($values['respCuenta']);
                    $empleados = $personal->Subordinados();
                    if(!empty($empleados))
                        $empleados = $util->ConvertToLineal($empleados,'personalId');

                }
			}
			$encargados = array_merge($encargados,$empleados);
			$values['respCuenta'] =  array_unique($encargados);

			$listCxc = $cxc->searchCxC($values);
			$totales =  array();
			$contratos= [];
			foreach($listCxc['items'] as $key => $value)
			{

				$totales[$value['nombre']]['total']=$totales[$value['nombre']]['total']+$value['total'];
				$totales[$value['nombre']]['payment']=$totales[$value['nombre']]['payment']+$value['payment'];
				$totales[$value['nombre']]['saldo']=$totales[$value['nombre']]['saldo']+$value['saldo'];
				$totales[$value['nombre']]['nameContact']=$value['nameContact'];
				$totales[$value['nombre']]['facturador']=$value['facturador'];
				$totales[$value['nombre']]['rfc']=$value['rfc'];
				$totales[$value['nombre']]['facturas'][]=$value;
                if(!in_array($value['contractId'],$contratos)){
					array_push($contratos,$value['contractId']);
                    $totales[$value['nombre']]['saldoAnterior']=$cxc->getSaldo((int)$values['year']-1,$value['contractId']);
                    $totales[$value['nombre']]['saldo']=$totales[$value['nombre']]['saldo']+$totales[$value['nombre']]['saldoAnterior'];
            	}
			}
			$smarty->assign("totales", $totales);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-cxc.tpl');
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
