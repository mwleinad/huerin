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

	session_save_path("/tmp");

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}

	$db->setQuery("SELECT * FROM personal WHERE tipoPersonal = 'Gerente' OR tipoPersonal = 'Supervisor'");
	$personal = $db->GetResult();

	foreach($personal as $value)
	{
		$User["userId"] = $value["personalId"];
		
		switch($value["tipoPersonal"])
		{
			case "Socio": $User['roleId'] = 1; break;
			case "Gerente": $User['roleId'] = 2; break;
			case "Supervisor": $User['roleId'] = 3; break;
			case "Contador": $User['roleId'] = 3; break;
			case "Auxiliar": $User['roleId'] = 3; break;
			case "Asistente": $User['roleId'] = 1; break;
			case "Recepcion": $User['roleId'] = 1; break;
			case "Cliente": $User['roleId'] = 4; break;
			case "Nomina": 
				$User['roleId'] = 1; 
				$User['subRoleId'] = "Nomina"; 
		}
		$User["username"] = $value["name"];
		$clientes = $customer->EnumerateNameOnly("subordinado", 0);
//		$clientes = $workflow->EnumerateWorkflows($clientes, date("m"), date("Y"));
			foreach($clientes as $key => $cliente)
			{
				foreach($cliente["contracts"] as $keyContract => $contract)
				{
					$db->setQuery("SELECT servicioId, nombreServicio FROM servicio 
					LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
					WHERE contractId = '".$contract["contractId"]."' AND servicio.status = 'activo'					
					ORDER BY nombreServicio ASC");
		//$this->Util()->DB()->query;
					$clientes[$key]["contracts"][$keyContract]["instanciasServicio"] = $db->GetResult();
				
					foreach($clientes[$key]["contracts"][$keyContract]["instanciasServicio"] as $keyInstancia => $instancia)
					{
						for($ii = 1; $ii <= 12; $ii++)
						{
							$clientes[$key]["contracts"][$keyContract]["instanciasServicio"][$keyInstancia]["instancias"][$ii] = $workflow->StatusByMonth($instancia["servicioId"], $ii , date("Y"));
						}
					}
				}
			}

		$smarty->assign("clientes", $clientes);
		$smarty->assign("clientesMeses", $clientesMeses);
		$smarty->assign("DOC_ROOT", DOC_ROOT);
		$smarty->assign("WEB_ROOT", WEB_ROOT);

		$html = '<link rel="stylesheet" type="text/css" href="'.WEB_ROOT.'/css/960.css" />
		<link rel="stylesheet" type="text/css" href="'.WEB_ROOT.'/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="'.WEB_ROOT.'/css/text.css" />
		<link rel="stylesheet" type="text/css" href="'.WEB_ROOT.'/css/blue.css" />
		<link type="text/css" href="'.WEB_ROOT.'/css/smoothness/ui.css" rel="stylesheet" />
		<link rel="icon" href="'.WEB_ROOT.'/css/animated_favicon.gif" type="image/gif" />';

		$html .= $smarty->fetch(DOC_ROOT.'/templates/lists/report-servicio-pdf.tpl');

		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('legal', 'landscape');
		$dompdf->render();
	
		file_put_contents("reporte.pdf", $dompdf->output()); 
		
		$body = "Adjunto encontrara su reporte semanal de servicio";
		$to = "dlopez@trazzos.com";
		$attachment = DOC_ROOT."/cron/reporte.pdf";
		$fileName = "reporte_de_servicio.pdf";
		
		$sendmail->Prepare("Reporte Semanal de Servicio", $body, $to, $toName, $attachment, $fileName);
		echo "Email Enviado";
		echo "<br>";
		
	}

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/report-servicio.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}
	

?>