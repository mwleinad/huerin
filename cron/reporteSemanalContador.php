<?php
exit;
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


		$db->setQuery('
			SELECT
				p.personalId,
				p.name,
				p.email
			FROM
				personal as p
			WHERE
				p.personalId in (
					SELECT personal.jefeContador
					FROM contract
					LEFT JOIN personal on responsableCuenta = personal.personalId
					GROUP BY jefeContador
					ORDER BY responsableCuenta
				) and p.email like "%@%"
		');
		$jefesCont = $db->GetResult();

		foreach ($jefesCont as $key => $cont) {

			$db->setQuery("
				SELECT
					t.tipoServicioId,
					t.nombreServicio,
					i.instanciaServicioId
				FROM
					tipoServicio as t
				LEFT JOIN servicio as s ON s.tipoServicioId = t.tipoServicioId
				LEFT JOIN instanciaServicio as i ON i.servicioId = s.servicioId
				LEFT JOIN contract as cc ON cc.contractId = s.contractId
				WHERE
					cc.activo = 'Si' AND
					s.status = 'activo' AND

					i.status <> 'completa' and
					i.status <> 'inactiva' and
					i.status <> 'baja' and
					i.class <> 'CompletoTardio' and

					/* i.status!='completa' AND */
					cc.responsableCuenta in (
						SELECT
							personalId
						FROM
							personal
						WHERE
							jefeContador='".$cont[personalId]."'
					)
				GROUP BY t.tipoServicioId
				ORDER BY t.tipoServicioId
			");
			$servicios = $db->GetResult();

			foreach ($servicios as $key => $serv) {

				$subject = "Reporte Semanal del Equipo ".$serv['nombreServicio']." - ".date('d/m/Y');
				$jefeContador = 0;
				$CONTADORESAUXILIARES = '';
				$db->setQuery("
					SELECT
						personalId,
						CONCAT('<b style=\"color:blue\">',name,'</b>') as name,
						CONCAT('CONTADOR') as tipoPersona
					FROM personal
					where
					personalId='".$cont[personalId]."'
				");
				$jefeContador = $db->GetRow();
				$CONTADORESAUXILIARES[] = $jefeContador;
				$db->setQuery("
					SELECT
						personalId,
						CONCAT('<b style=\"color:red\">',name,'</b>') as name,
						CONCAT('AUXILIAR') as tipoPersona
					FROM
						personal
					WHERE
						jefeContador='".$cont[personalId]."' AND
						personalId IN (
							SELECT
								personalId
							FROM
								contract
							LEFT JOIN personal ON responsableCuenta = personal.personalId
							ORDER BY responsableCuenta
						)
				");

				$responsable = $db->GetResult();
				foreach ($responsable as $key10 => $row10) {
					$CONTADORESAUXILIARES[] = $row10;
				}
				$responsable = $CONTADORESAUXILIARES;


				$nresponsables = count($responsable);

				if ($nresponsables>5)
					$responsablePDF = array_chunk($responsable,5);
				else
					$responsablePDF[0] = $responsable;

				$db->setQuery("
					SELECT
						i.instanciaServicioId,
						i.date,
						cc.name,
						t.nombreServicio
					FROM
						instanciaServicio as i
					LEFT JOIN servicio as s ON s.servicioId = i.servicioId
					LEFT JOIN tipoServicio as t ON t.tipoServicioId = s.tipoServicioId
					LEFT JOIN contract as cc ON cc.contractId = s.contractId
					WHERE
						cc.activo = 'Si' AND
						s.status = 'activo' AND
						i.status!='completa' AND
						cc.responsableCuenta IN (
							SELECT
								personalId
							FROM
								personal
							WHERE
								jefeContador='".$cont[personalId]."'
						) AND
						s.tipoServicioId = '".$serv['tipoServicioId']."' AND
						(i.date>='2015-01-01' AND i.date<='".date("Y-m-d")."')
					group By i.date
				");
				$fechas = $db->GetResult();

				if(count($fechas)<=0){
					break;
				}

				foreach ($responsablePDF as $key => $res) {

					$nresponsablesPDF[$key] = count($res);

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
											<td width="60%" align="left">'.$cont['name'].'<br>'.$serv['nombreServicio'].'<br>'.date('d/m/Y').'<br>'.$subject.'</td>
										</tr>
									</table>
									<br><br><br>
									<table border="1" cellspacing="1" cellpadding="1">
										<tr>
											<td align="left">Mes</td>
					';

					foreach ($res as $k => $r) {
						$html[$key] .= '<td align="left">'.$r['name'].'</td>';
					}

					$html[$key] .= '</tr>';

					foreach ($fechas as $keyF => $fecha) {

						$html[$key] .= '<tr><td align="left">'.$util->GetMesGuion($fecha['date']).'</td>';

						foreach ($res as $k => $r) {

							$db->setQuery("
								SELECT
									i.instanciaServicioId,
									i.date,
									s.tipoServicioId,
									cc.name,
									t.nombreServicio
								FROM `instanciaServicio` as i
								LEFT JOIN servicio as s ON s.servicioId = i.servicioId
								LEFT JOIN tipoServicio as t ON t.tipoServicioId = s.tipoServicioId
								LEFT JOIN contract as cc ON cc.contractId = s.contractId
								WHERE
									cc.activo = 'Si' AND
									s.status = 'activo' AND

									i.status <> 'completa' and
									i.status <> 'inactiva' and
									i.status <> 'baja' and
									i.class <> 'CompletoTardio' and

									/* i.status!='completa' AND */
									cc.responsableCuenta = '".$r['personalId']."' AND
									s.tipoServicioId = '".$serv['tipoServicioId']."' AND
									date='".$fecha['date']."'
								ORDER BY i.date
							");
							$razones = $db->GetResult();
							if ($r['tipoPersona'] == "CONTADOR") {
								$color = ' style="background-color: #F5F9BF;" ';
							}else if ($r['tipoPersona'] == "AUXILIAR"){
								$color = ' style="background-color: #D1D1D1;" ';

							}else if ($r['tipoPersona'] == "SUPERVISOR"){
								$color = ' style="background-color: #C7FEC0;" ';

							}
							$html[$key]  .= '<td align="left" '.$color.'>';

							foreach ($razones as $keyR => $raz) {
								$html[$key] .= $raz['name'].'<br>';
							}
							$html[$key] .= '</td>';
						}
						$html[$key] .= '</tr>';
					}
					$html[$key] .= '</table></body></html>';
				}
				echo "<pre>";
				print_r($html);
				echo "</pre>";


				$mail = new PHPMailer();

				$body = "Adjunto reporte semanal del Equipo ".$serv['nombreServicio']." hasta el ".date('d/m/Y');
				$to = $cont['email'];
				//$to = 'kayhep92@gmail.com';
				// $to = 'oswarl8S@gmail.com';
				// $to = 'jony@avantika.com.mx';
				// $to = 'ayuda@avantika.com.mx';
				$toName = $cont['name'];
				$from = "sistema@braunhuerin.com.mx";
				$fromName = "Administrador del Sistema";

				foreach ($html as $key => $value) {
					$dompdf = new DOMPDF();
					$dompdf->load_html($value);
					// if($nresponsablesPDF<=2)
					// 	$dompdf->set_paper('legal', 'portrait');
					// else

					$dompdf->set_paper('legal', 'landscape');
					$dompdf->render();
					file_put_contents("reporte_semanal_contador_".($key+1).".pdf", $dompdf->output());
					$attachment = DOC_ROOT."/cron/reporte_semanal_contador_".($key+1).".pdf";
					$fileName = "reporte_semanal_contador_".($key+1).".pdf";
					$mail->AddAttachment($attachment, $fileName);
				}

				$mail->AddReplyTo($from, $fromName);
				$mail->SetFrom($from, $fromName);
				$mail->AddAddress($to, $toName);
				$mail->Subject    = $subject;
				$mail->MsgHTML($body);


				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.avantika.com.mx";
				$mail->Port       = 587;
				$mail->Username   = "smtp@avantika.com.mx";
				$mail->Password   = "smtp1234";

				$mail->SMTPDebug = 0;

				$mail->Send();

				echo "Email Enviado al Contador ".$toName;
				echo "<br>";
			}
		}

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/reportSemanalContador.txt";
	$open = fopen($file,"w");

	if ( $open ) {
		fwrite($open,$entry);
		fclose($open);
	}

?>