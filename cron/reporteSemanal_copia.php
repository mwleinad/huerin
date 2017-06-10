<?php

ini_set('memory_limit','3G');

if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
	$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
}
else
{
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
}

	define('DOC_ROOT', $docRoot);

	//session_save_path("/tmp");

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}


		$db->setQuery("SELECT distinct responsableCuenta, personal.name, personal.email
	                  FROM contract
	                  left join personal on responsableCuenta = personalId
	                  where responsableCuenta != 0 and personal.email != '' and contract.activo = 'Si'
	                  order by name");
	  	$responsables = $db->GetResult();

	  	foreach ($responsables as $key => $res) {

		    $db->setQuery("SELECT t.tipoServicioId,t.nombreServicio,i.instanciaServicioId
		                    FROM tipoServicio as t
		                    left join servicio as s on s.tipoServicioId = t.tipoServicioId
		                    left join instanciaServicio as i on i.servicioId = s.servicioId
		                    left join contract as cc on cc.contractId = s.contractId
		                    where cc.activo = 'Si' and s.status = 'activo' and i.status!='completa' and cc.responsableCuenta = '".$res['responsableCuenta']."'
		                    group by t.tipoServicioId
		                    order by t.tipoServicioId");
		    $servicios = $db->GetResult();

		    foreach ($servicios as $key => $serv) {

		    	$asunto = "Reporte Semanal ".$res['name']." - ".$serv['nombreServicio']." - ".date('d/m/Y');

		      	$db->setQuery("SELECT i.instanciaServicioId,i.date
		                      	FROM `instanciaServicio` as i
		                      	left join servicio as s on s.servicioId = i.servicioId
		                      	left join tipoServicio as t on t.tipoServicioId = s.tipoServicioId
		                      	left join contract as cc on cc.contractId = s.contractId
		                      	where cc.activo = 'Si' and s.status = 'activo' and i.status!='completa' and cc.responsableCuenta = '".$res['responsableCuenta']."' and s.tipoServicioId = '".$serv['tipoServicioId']."' and i.date>='2013-01-01'
		                      	group By i.date");
		      	$fechas = $db->GetResult();

		      	$db->setQuery("SELECT * FROM `step` where servicioId = '".$serv['tipoServicioId']."' order by stepId");
			    $pasos = $db->GetResult();

				$npasos = count($pasos);

				if($npasos>5)
					$pasosPDF = array_chunk($pasos,5);
				else
					$pasosPDF[0] = $pasos;

			    if(count($fechas)<=0){
			    	break;
			    }

			    foreach ($pasosPDF as $key => $p) {

			    	$npasosPDF[$key] = count($p);

			    	$html[$key] = '<html>
						                <head>
						                <style type="text/css">
						                body{
						                  font-family:Verdana, Arial, Helvetica, sans-serif;
						                  font-size:10px;
						                }
						                .titulo {
						                  color: #FFFFFF;
						                  font-family: Verdana, Arial, Helvetica, sans-serif;
						                  font-weight: bold;
						                  font-size: 12px;
						                }
						                </style>
						                </head>
						                <body>
						                <table border="0" cellspacing="0" cellpadding="0">
						                <tr>
						                <td width="20%" align="left" bgcolor="#CCCCCC"><IMG SRC="../logo.png"></td>
						                <td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						                <td width="60%" align="left">'.$res['name'].'<br>'.$serv['nombreServicio'].'<br>'.date('d/m/Y').'<br>'.$asunto.'</td>
						                </tr>
						                </table>
						                <br><br><br>
						                <table border="1" cellspacing="1" cellpadding="1">
						                <tr>
						                <td align="left">Mes</td>';
					foreach ($p as $k => $step) {
					    $html[$key] .= '<td align="left">'.$step['nombreStep'].'</td>';
					}

					$html[$key] .= '</tr>';

					foreach ($fechas as $keyF => $fecha) {

				    	$html[$key] .= '<tr><td align="left">'.$fecha['date'].'</td>';

				    	foreach ($p as $k => $step) {
	
							$db->setQuery("SELECT i.instanciaServicioId,i.date,s.tipoServicioId,cc.name,t.nombreServicio
					                    	FROM `instanciaServicio` as i
					                        left join servicio as s on s.servicioId = i.servicioId
					                        left join tipoServicio as t on t.tipoServicioId = s.tipoServicioId 
					                        left join contract as cc on cc.contractId = s.contractId
					                        where cc.activo = 'Si' and s.status = 'activo' and i.status!='completa' and cc.responsableCuenta = '".$res['responsableCuenta']."' and s.tipoServicioId = '".$serv['tipoServicioId']."' and date='".$fecha['date']."'
					                        order By i.date");
					        $razones = $db->GetResult();

					        $html[$key] .= '<td align="left">';

					        foreach ($razones as $keyR => $raz) {

					            $db->setQuery("SELECT count(*)
					        	                FROM `task`
					                            where stepId = '".$step['stepId']."'");
					            $ntareas = $db->GetSingle();
					            
					            $db->setQuery("SELECT count(*)
					                            FROM `taskFile`
					                            where servicioId = '".$raz['instanciaServicioId']."' and stepId = '".$step['stepId']."' and version=1");
					            $ntareasCompletas = $db->GetSingle();

					            $db->setQuery("SELECT diaVencimiento
					                            FROM `task`
					                            where stepId = '".$step['stepId']."'");
					            $diaV= $db->GetSingle();
					            
					            
					            if(($fecha['date']<date('Y-m-d')) and ($ntareasCompletas<$ntareas)){
					            	$day = date("d");
					            	$month = date("m");
					            	$year = date("Y");
					            	$fArray = explode("-",$fecha['date']);
					            	$auxD = $fArray[2];
					            	$auxM = $fArray[1];
					            	$auxY = $fArray[0];
					            	if($month == $auxM and $year == $auxY){
					            		if($diaV<$day){
					            			$html[$key] .= $raz['name'].'<br>';
					            		}
					            	}
					            	else{
					            		$html[$key] .= $raz['name'].'<br>';
					            	}
					            }
	   			            }

	   			             $html[$key] .= '</td>';
	        			}
	        			$html[$key] .= '</tr>';
	      			}
	      			$html[$key] .= '</table></body></html>';
			    }
			    				    
			    $mail = new PHPMailer(true);

			    $body = "Adjunto reporte semanal de ".$res['name']." del servicio ".$serv['nombreServicio']." hasta el ".date('d/m/Y');
				$to = $res['email'];
				//$to = 'kayhep92@gmail.com';
				$toName = $res['name'];
				$from = "sistema@braunhuerin.com.mx";
				$fromName = "Administrador del Sistema";

				foreach ($html as $key => $value) {

	      			$dompdf = new DOMPDF();
					$dompdf->load_html($value);
					if($npasosPDF[$key]<=2)
						$dompdf->set_paper('legal', 'portrait');
					else
						$dompdf->set_paper('legal', 'landscape');
					$dompdf->render();
					file_put_contents("reporte_semanal_".($key+1).".pdf", $dompdf->output());
					$attachment = DOC_ROOT."/cron/reporte_semanal_".($key+1).".pdf";
					$fileName = "reporte_semanal_".($key+1).".pdf";
					$mail->AddAttachment($attachment, $fileName);
				}
	      		
				$mail->AddReplyTo($from, $fromName);
				$mail->SetFrom($from, $fromName);
				$mail->AddAddress($to, $toName);
				$mail->Subject    = $asunto;
				$mail->MsgHTML($body);
				

				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.avantika.com.mx";
				$mail->Port       = 587;
				$mail->Username   = "smtp@avantika.com.mx";
				$mail->Password   = "smtp1234";

				$mail->SMTPDebug = 0;

				$mail->Send();

				echo "Email Enviado al Responsable de Cuenta ".$toName;
				echo "<br>";
			}
		}

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/reportSemanal.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}
	

?>