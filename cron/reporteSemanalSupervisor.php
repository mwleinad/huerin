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
                            p.personalId,p.name,p.email,p.departamentoId
                        from
                            personal as p
                        where
                            p.personalId in (
                                            select personal.jefeSupervisor
                                            FROM contract
                                            left join personal on responsableCuenta = personal.personalId
                                            order by responsableCuenta
                            )
                            and p.email like "%@%"');
        $jefesSup = $db->GetResult();

        foreach ($jefesSup as $key => $sup) {

            $db->setQuery("SELECT t.tipoServicioId,t.nombreServicio,i.instanciaServicioId
                            FROM tipoServicio as t
                            left join servicio as s on s.tipoServicioId = t.tipoServicioId
                            left join instanciaServicio as i on i.servicioId = s.servicioId
                            left join contract as cc on cc.contractId = s.contractId
                            where
                                cc.activo = 'Si' and
                                s.status = 'activo' and

                                i.status <> 'completa' and
                                i.status <> 'inactiva' and
                                i.status <> 'baja' and
                                i.class <> 'CompletoTardio' and

                                /* i.status!='completa' and  */
                                cc.responsableCuenta in (
                                SELECT personalId
                                FROM personal
                                where jefeSupervisor = '".$sup[personalId]."'
                                GROUP BY personalId
                            )
                            AND t.departamentoId =  '".$sup[departamentoId]."'
                            group by t.tipoServicioId
                            order by t.tipoServicioId");
            $servicios = $db->GetResult();

            foreach ($servicios as $key => $serv) {

                $subject = "Reporte Semanal del Equipo ".$serv['nombreServicio']." - ".date('d/m/Y');
                $jefeSupervisor = 0;
                $CONTADORESAUXILIARES = '';
                $db->setQuery("
                        SELECT
                            personalId,
                            CONCAT('<b style=\"color:green\">',name,'</b>') as name,
                            CONCAT('SUPERVISOR') as tipoPersona
                        FROM
                            personal
                        WHERE
                            personalId='".$sup['personalId']."'
                    ");
                // echo $db->getQuery();
                $jefeSupervisor = $db->GetRow();
                $CONTADORESAUXILIARES[] = $jefeSupervisor;

                $db->setQuery("SELECT
                                personalId,
                                CONCAT('<b style=\"color:blue\">',name,'</b>') as name,
                                CONCAT('CONTADOR') as tipoPersona
                                FROM personal
                                where jefeSupervisor='".$sup[personalId]."' and personalId in(
                                        select personalId
                                        FROM contract
                                        left join personal on responsableCuenta = personal.personalId
                                        where activo = 'Si'
                                        GROUP BY personalId
                                    )
                                    AND tipoPersonal = 'Contador'
                                    AND departamentoId = '".$sup[departamentoId]."'
                                order by name");
                $responsable = $db->GetResult();
                $jefeContador = '';
                foreach ($responsable as $key10 => $row10) {
                    $CONTADORESAUXILIARES[] = $row10;
                    $jefeContador .= $row10['personalId'].',';
                    $db->setQuery("
                        SELECT
                            personalId,
                            CONCAT('<b style=\"color:red\">',name,'</b>') as name,
                            CONCAT('AUXILIAR') as tipoPersona
                        FROM
                            personal
                        WHERE
                            jefeContador='".$row10['personalId']."' AND
                            personalId IN (
                                SELECT
                                    personalId
                                FROM
                                    contract
                                LEFT JOIN personal ON responsableCuenta = personal.personalId
                                ORDER BY responsableCuenta
                            )
                            AND departamentoId = '".$sup[departamentoId]."'
                    ");

                    $AUXILIARES = $db->GetResult();
                    foreach ($AUXILIARES as $key11 => $row11) {
                        $CONTADORESAUXILIARES[] = $row11;
                    }
                }
                $responsable = $CONTADORESAUXILIARES;

                $nresponsables = count($responsable);

                // if ($nresponsables>5)
                    // $responsablePDF = array_chunk($responsable,5);
                // else
                    $responsablePDF[0] = $responsable;

                $db->setQuery("SELECT i.instanciaServicioId,i.date,cc.name,t.nombreServicio
                                FROM `instanciaServicio` as i
                                left join servicio as s on s.servicioId = i.servicioId
                                left join tipoServicio as t on t.tipoServicioId = s.tipoServicioId
                                left join contract as cc on cc.contractId = s.contractId
                                where cc.activo = 'Si' and s.status = 'activo' and i.status!='completa' and cc.responsableCuenta in (
                                        SELECT personalId
                                        FROM personal
                                        where
                                        jefeSupervisor = '".$sup['personalId']."' OR
                                        jefeContador IN (".$jefeContador."-1)
                                    ) and s.tipoServicioId = '".$serv['tipoServicioId']."' and (i.date>='2015-01-01' AND i.date<='".date("Y-m-d")."')
                                group By i.date");
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
                  <div style="/*border-style: solid;border-color: #ff0000 #0000ff;*/">
		<table border="0" cellspacing="0" cellpadding="0" position="fixed">
                        <tr>
                            <td width="20%" align="left" bgcolor="#CCCCCC"><IMG SRC="../logo.png"></td>
                            <td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="60%" align="left">'.$sup['name'].'<br>'.$serv['nombreServicio'].'<br>'.date('d/m/Y').'<br>'.$subject.'</td>
                        </tr>
                    </table>
                    <br><br><br>
                    <table border="1" cellspacing="1" cellpadding="1" style="width:100%">
                        <tr>
                            <td align="left">Mes</td>';

                    foreach ($res as $k => $r) {
                        $html[$key] .= '<td align="left">'.$r['name'].'</td>';
                    }
                    $html[$key] .= '</tr>';

                    foreach ($fechas as $keyF => $fecha) {

                         $html[$key] .= '<tr><td align="left">'.$util->GetMesGuion($fecha['date']).'</td>';

                        foreach ($res as $k => $r) {

                                $db->setQuery("SELECT i.instanciaServicioId,i.date,s.tipoServicioId,cc.name,t.nombreServicio
                                                FROM `instanciaServicio` as i
                                                left join servicio as s on s.servicioId = i.servicioId
                                                left join tipoServicio as t on t.tipoServicioId = s.tipoServicioId
                                                left join contract as cc on cc.contractId = s.contractId
                                                where
                                                    cc.activo = 'Si' and
                                                    s.status = 'activo' and

                                                    i.status <> 'completa' and
                                                    i.status <> 'inactiva' and
                                                    i.status <> 'baja' and
                                                    i.class <> 'CompletoTardio' and

                                                    /* i.status!='completa' */
                                                    /* (i.status!='completa' or i.status!='baja') and */
                                                    cc.responsableCuenta = '".$r['personalId']."' and
                                                    s.tipoServicioId = '".$serv['tipoServicioId']."' and
                                                    date='".$fecha['date']."'
                                                order By i.date
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
                                    $html[$key]  .= $raz['name'].'<br>';
                                }
                                $html[$key] .= '</td>';
                        }
                        $html[$key] .= '</tr>';
                    }
                    $html[$key] .= '</table></div></body></html>';
                }


                echo "<br><br><pre>";
                print_r($html);
                echo "<br><br><pre>";

                $mail = new PHPMailer(true);

                $body = "Adjunto reporte semanal del Equipo ".$serv['nombreServicio']." hasta el ".date('d/m/Y');
                $to = $sup['email'];
                // $to = 'oswarl8S@gmail.com';
                // $to = 'jony@avantika.com.mx';
                // $to = 'ayuda@avantika.com.mx';
                //$to = 'kayhep92@gmail.com';
                $toName = $sup['name'];
                $from = "sistema@braunhuerin.com.mx";
                $fromName = "Administrador del Sistema";

                foreach ($html as $key => $value) {
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($value);
                    // if($nresponsablesPDF[$key]<=2)
                    //     $dompdf->set_paper('legal', 'portrait');
                    // else

                    $dompdf->set_paper('legal', 'landscape');

                    $dompdf->render();
                    file_put_contents("reporte_semanal_supervisor_".($key+1).".pdf", $dompdf->output());
                    $attachment = DOC_ROOT."/cron/reporte_semanal_supervisor_".($key+1).".pdf";
                    $fileName = "reporte_semanal_supervisor_".($key+1).".pdf";
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

                echo "Email Enviado al Supervisor ".$toName;
                echo "<br>";
            }
        }

    $time = date("d-m-Y").' a las '.date('H:i:s');
    $entry = "Cron ejecutado el $time Hrs.";
    $file = DOC_ROOT."/cron/reportSemanalSupervisor.txt";
    $open = fopen($file,"w");

    if ( $open ) {
        fwrite($open,$entry);
        fclose($open);
    }

?>