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
            SELECT p.personalId,p.name,p.email,p.departamentoId
            FROM personal AS p
            WHERE p.personalId IN (
            SELECT personal.jefeGerente
            FROM contract
            LEFT JOIN personal ON responsableCuenta = personal.personalId
            ORDER BY responsableCuenta) AND p.email LIKE "%@%"
        ');
        $jefesGerente = $db->GetResult();

        foreach ($jefesGerente as $keyJG => $rowJG) {

            $db->setQuery('
                SELECT
                    p.personalId,
                    p.name,
                    p.email
                FROM
                    personal as p
                where
                    p.jefeGerente = "'.$rowJG['personalId'].'" and
                    p.email like "%@%" and
                    p.tipoPersonal = "Supervisor" and
                    p.departamentoId = "'.$rowJG['departamentoId'].'"
            ');
            $jefesSup = $db->GetResult();

            foreach ($jefesSup as $keyJS => $sup) {

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
                                    where jefeSupervisor='".$sup[personalId]."'
                                    GROUP BY personalId
                                )
                                AND t.departamentoId = '".$rowJG['departamentoId']."'
                                group by t.tipoServicioId
                                order by t.tipoServicioId
                ");
                $servicios = $db->GetResult();


                foreach ($servicios as $keyS => $serv) {

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
                                    AND departamentoId = '".$rowJG['departamentoId']."'
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
                                AND departamentoId = '".$rowJG['departamentoId']."'
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
                    $html  = '';
                    foreach ($responsablePDF as $keyR => $res) {

                        $nresponsablesPDF[$keyR] = count($res);
                        // $html .= "PDF: 1 ";

                        $html .= '<html>
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
                            // $html .= ' 2 ';
                            $html .= '<td align="left">'.$r['name'].'</td>';
                        }
                        // $html .= ' 3 ';
                        $html .= '</tr>';

                        foreach ($fechas as $keyF => $fecha) {
                            // $html .= ' 4 ';
                            $html .= '<tr><td align="left">'.$util->GetMesGuion($fecha['date']).'</td>';


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
                                                        /* (i.status!='completa' or i.status!='baja')  and */
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
                                     // $html .= ' 5 ';
                $html  .= '<td align="left" '.$color.'>';

                                    foreach ($razones as $keyRZ => $raz) {
                                        // $html .= ' 6 ';
                                        $html  .= $raz['name'].'<br>';
                                    }
                                    // $html .= ' 7 ';
                                    $html .= '</td>';
                            }
                            // $html .= ' 8 ';
                            $html .= '</tr>';
                        }
                        // $html .= ' 9 END ';
                        $html .= '</table></div></body></html>';

                        $DATO[$serv['tipoServicioId']]['name'] = $rowJG['name'];
                        $DATO[$serv['tipoServicioId']]['email'] = $rowJG['email'];
                        $DATO[$serv['tipoServicioId']]['personalId'] = $rowJG['personalId'];
                        $DATO[$serv['tipoServicioId']]['subject'] = $subject;
                        $DATO[$serv['tipoServicioId']]['nombreServicio'] = $serv['nombreServicio'];

                        $DATO[$serv['tipoServicioId']]["FILES"]["personalId_".$sup['personalId']]['name'] = $sup['name'];
                        $DATO[$serv['tipoServicioId']]["FILES"]["personalId_".$sup['personalId']]['email'] = $sup['email'];
                        $DATO[$serv['tipoServicioId']]["FILES"]["personalId_".$sup['personalId']]['personalId'] = $sup['personalId'];
                        $DATO[$serv['tipoServicioId']]["FILES"]["personalId_".$sup['personalId']]['PDF'] = $html;

                        $html2['GRENTE']["ID_GERENTE_".$rowJG['personalId']]['PDFS'] = $DATO;
                    } //  FIN foreach ($responsablePDF as $keyR => $res)

                } //  FIN foreach ($servicios as $keyS => $serv)

            } //  FIN foreach ($jefesSup as $keyJS => $sup)

        } //  FIN foreach ($jefesGerente as $keyJG => $rowJG)

        foreach ($html2['GRENTE'] as $keyGER => $rowGER) {
            foreach ($rowGER['PDFS'] as $key => $row) {
                        $mail = new PHPMailer(true);

                        $body = "Adjunto reporte semanal del Equipo ".$row['nombreServicio']." hasta el ".date('d/m/Y');
                        $to = $row['email'];
                        // $to = 'oswarl8S@gmail.com';
                        // $to = 'jony@avantika.com.mx';
                        // $to = 'ayuda@avantika.com.mx';
                        $toName = $row['name'];
                        $from = "sistema@braunhuerin.com.mx";
                        $fromName = "Administrador del Sistema";
                        foreach ($row['FILES'] as $keyFILE => $rowFILE) {
                            // print_r($rowFILE['PDF']);
                            $dompdf = new DOMPDF();
                            $dompdf->load_html($rowFILE['PDF']);

                            $dompdf->set_paper('legal', 'landscape');

                            $dompdf->render();
                            file_put_contents("FILE_PDF/reporte_semanal_gerente Supervisor_".($rowFILE['name']).".pdf", $dompdf->output());
                            $attachment = DOC_ROOT."/cron/FILE_PDF/reporte_semanal_gerente Supervisor_".($rowFILE['name']).".pdf";
                            $fileName = "reporte_semanal_gerente Supervisor_".($rowFILE['name']).".pdf";
                            $mail->AddAttachment($attachment, $fileName);
                        }

                        $mail->AddReplyTo($from, $fromName);
                        $mail->SetFrom($from, $fromName);
                        $mail->AddAddress($to, $toName);
                        $mail->Subject    = $row['subject'];
                        $mail->MsgHTML($body);

                        $mail->IsSMTP();
                        $mail->SMTPAuth   = true;
                        $mail->Host       = "mail.avantika.com.mx";
                        $mail->Port       = 587;
                        $mail->Username   = "smtp@avantika.com.mx";
                        $mail->Password   = "smtp1234";

                        $mail->SMTPDebug = 0;

                        $mail->Send();

                        echo "Email Enviado al Gerente ".$toName;
                        echo "<br>";
                        // exit();
            }
        }

        echo "<br><br><pre>";
        print_r($html2);
        echo "<br><br><pre>";

        $time = date("d-m-Y").' a las '.date('H:i:s');
        $entry = "Cron ejecutado el $time Hrs.";
        $file = DOC_ROOT."/cron/reportSemanalGerente.txt";
        $open = fopen($file,"w");

        if ( $open ) {
            fwrite($open,$entry);
            fclose($open);
        }

?>
