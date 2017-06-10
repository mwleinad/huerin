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

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}

	$diaSemana = date("l");

	if($diaSemana == 'Tuesday'){

		$db->setQuery("SELECT distinct responsableCuenta, personal.name, personal.email
	                  FROM contract
	                  left join personal on responsableCuenta = personalId
	                  where responsableCuenta = 106 and personal.email != ''
	                  order by name");
	  	$responsables = $db->GetResult();

	  	foreach ($responsables as $key => $res) {

		    $db->setQuery("SELECT t.tipoServicioId,t.nombreServicio
		                    FROM tipoServicio as t");
		    $servicios = $db->GetResult();

		    foreach ($servicios as $key => $serv) {

		      	$db->setQuery("SELECT i.date
		                      	FROM `instanciaservicio` as i
                                where i.date >= '2013-01-01'
		                      	group By i.date");
		      	$fechas = $db->GetResult();

		      	print_r($fechas);

		      	$db->setQuery("SELECT * FROM `step` where servicioId = '".$serv['tipoServicioId']."' order by stepId");
			    $pasos = $db->GetResult();

			    $enviar=false;

			    $subject = "Reporte Semanal ".$res['name']." - ".$serv['nombreServicio']." - ".date('d/m/Y');

			    $html = '<html>
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
		                <td width="60%" align="left">'.$res['name'].'<br>'.$serv['nombreServicio'].'<br>'.date('d/m/Y').'<br>'.$subject.'</td>
		                </tr>
		                </table>
		                <br><br><br>
		                <table border="1" cellspacing="1" cellpadding="1">
		                <tr>
		                <td align="left">Mes</td>';

		        foreach ($pasos as $key => $step) {
			        $html .= '<td align="left">'.$step['nombreStep'].'</td>';
			    }

			    $html .= '</tr>';

			    foreach ($fechas as $key => $fecha) {

			    	$html .= '<tr><td align="left">'.$fecha['date'].'</td>';

			    	foreach ($pasos as $key => $step) {

				        $db->setQuery("SELECT i.instanciaServicioId,i.date,s.servicioId,c.contractId,t.nombreServicio,c.name
				        				FROM `instanciaservicio` as i
				        				left join servicio as s on i.servicioId = s.servicioId
                                        left join tipoServicio as t on s.tipoServicioId = t.tipoServicioID
				        				left join contract as c on s.contractId = c.contractId
				        				where s.tipoServicioId = '".$serv['tipoServicioId']."' and c.responsableCuenta = '".$res['responsableCuenta']."' and date='".$fecha['date']."'
				                        order By i.date");
				        $razones = $db->GetResult();

				        $html .= '<td align="left">';

				        foreach ($razones as $key => $raz) {

				            $db->setQuery("SELECT count(*)
				        	                FROM `task`
				                            where stepId = '".$step['stepId']."'");
				            $ntareas = $db->GetSingle();
				            
				            $db->setQuery("SELECT count(*)
				                            FROM `taskFile`
				                            where servicioId = '".$raz['instanciaServicioId']."' and stepId = '".$step['stepId']."' and version=1");
				            $ntareasCompletas = $db->GetSingle();
				            
				            if(($fecha['date']<date('Y-m-d')) and ($ntareasCompletas<$ntareas)){
				                $html .= $raz['name'].'<br>';
				                $enviar=true;
				            }
   			            }
   			            $html .= '</td>';
        			}
        			$html .= '</tr>';
      			}
      			$html .= '</table></body></html>';

      			if($enviar==true){
      				$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					$dompdf->set_paper('legal', 'landscape');
					$dompdf->render();
		
					file_put_contents("reporte_semanal.pdf", $dompdf->output());
					
					$body = "Adjunto reporte semanal de ".$res['name']." del servicio ".$serv['nombreServicio']." hasta el ".date('d/m/Y');
					$to = "kayhep92@gmail.com";
					$toName = $res['name'];
					$attachment = DOC_ROOT."/cron/reporte_semanal.pdf";
					$fileName = "reporte_semanal.pdf";
					
					$sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName);
					echo "Email Enviado";
					echo "<br>";
      			}
      			
			}
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