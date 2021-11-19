<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail extends Main
{
	public function Prepare($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema")
	{
			$mail = new PHPMailer(true); // defaults to using php "mail()"

			$subject= utf8_decode($subject);
		 	$fromName = utf8_decode($fromName);
		 	try{
                $mail->addReplyTo($from, $fromName);
                $mail->setFrom($from, $fromName);
                $mail->addAddress($to, $toName);
                $mail->Subject    = $subject;
                $mail->msgHTML($body);
                $mail->isSMTP();
                $mail->SMTPAuth   = true;
                $mail->Host       = SMTP_HOST2;
                $mail->SMTPAutoTLS  = false;
                $mail->Port       = SMTP_PORT2;
                $mail->Username   = SMTP_USER2;
                $mail->Password   = SMTP_PASS2;
                $mail->Timeout=300;
                $mail->SMTPDebug = 0;
                if($attachment != "")
                {
                    $mail->addAttachment($attachment, $fileName);
                }

                if($attachment2 != "")
                {
                    $mail->addAttachment($attachment2, $fileName2);
                }

                $mail->send();

            } catch (Exception $e){
                return false;
            }
        return true;
	}

	public function PrepareMultiple($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$cc=array())
	{
			$mail = new PHPMailer(true); // defaults to using php "mail()"
            try{
                $mail->addReplyTo($from, $fromName);
                $mail->setFrom($from, $fromName);
                foreach($to as $correo => $name)
                {
                    switch($name){
                        case 'Desarrollador':
                            if(count($to)>1)
                                $mail->addBCC($correo, $name);
                            else
                                $mail->addAddress($correo, $name);
                            break;
                        default:
                            $mail->addAddress($correo, $name);
                            break;
                    }
                }
                if(count($cc)>0)
                {
                    foreach($cc as $ccEmail => $ccName)
                    {
                        $mail->addBCC($ccEmail, $ccName);
                    }
                    $mail->addBCC(EMAIL_DEV,'COPIA CARBON');
                }else{
                    $mail->addAddress(EMAIL_DEV, 'DEVELOPER');
                }
                $mail->Subject    = $subject;
                $mail->msgHTML($body);
                $mail->isSMTP();
                $mail->SMTPAuth   = true;
                $mail->Host       = SMTP_HOST2;
                $mail->SMTPAutoTLS  = false;
                $mail->Port       = SMTP_PORT2;
                $mail->Username   = SMTP_USER2;
                $mail->Password   = SMTP_PASS2;
                $mail->SMTPDebug=0;
                $mail->Timeout=3600;
                if($attachment != "")
                {
                    $mail->addAttachment($attachment, $fileName);
                }

                if($attachment2 != "")
                {
                    $mail->addAttachment($attachment2, $fileName2);
                }
                $mail->send();
            } catch (Exception $e){
                return false;
            }

        return true;
	}
    public function PrepareMultipleHidden($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        $mail = new PHPMailer();

        $mail->addReplyTo($from, $fromName);
        $mail->setFrom($from, $fromName);
        foreach($to as $correo => $name)
        {
           $mail->addBCC($correo, $name);
        }
        if($sendDesarrollador)
            $mail->addBCC(EMAIL_DEV,'Desarrollador');

        $mail->Subject    = $subject;
        $mail->msgHTML($body);
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = SMTP_HOST2;
        $mail->SMTPAutoTLS  = false;
        $mail->Port       = SMTP_PORT2;
        $mail->Username   = SMTP_USER2;
        $mail->Password   = SMTP_PASS2;
        $mail->Timeout=300;
        $mail->SMTPDebug=0;

        if($attachment != "")
        {
            $mail->addAttachment($attachment, $fileName);
        }

        if($attachment2 != "")
        {
            $mail->addAttachment($attachment2, $fileName2);
        }
        if($mail->send())
            return true;
        else
            return false;

    }
    public function PrepareMultipleNotice($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        //crear un objeto mail por cada correo
        $mail = new PHPMailer(); // defaults to using php "mail()"
        $mail->addReplyTo($from, $fromName);
        $mail->setFrom($from, $fromName);
        $mail->Subject      = $subject;
        $mail->msgHTML($body);
        $mail->isSMTP();
        $mail->SMTPAuth     = true;
        $mail->Host         = SMTP_HOST2;
        $mail->SMTPAutoTLS  = false;
        $mail->Port         = SMTP_PORT2;
        $mail->Username     = SMTP_USER2;
        $mail->Password     = SMTP_PASS2;
        $mail->SMTPDebug    = 0;
        $mail->SMTPKeepAlive=true;

        if($attachment != "")
        {
            $mail->addAttachment($attachment, $fileName);
        }

        if($attachment2 != "")
        {
            $mail->addAttachment($attachment2, $fileName2);
        }

        $cont=1;
        $lote =1;
        $totalCont =1;
        $totalCorreo = count($to);
        $logSend ="";
        foreach($to as $correo => $name)
        {
            if($correo==EMAILCOORDINADOR){
                $totalCont++;
                continue;
            }

            if($totalCorreo==1){
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->addAddress($correo, $name);
                $cont++;
                $totalCont++;
            }
            if($cont>=50||$totalCont>=$totalCorreo){
                //en la ultima iteracion o por cada 50 no se incluye el ultimo se debe incluir
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->addBCC($correo, $name);

                //resetear contador
                $cont=1;
                $mail->send();
                $logSend.="Lote ".$lote." enviado".chr(13).chr(10);
                $lote++;
                $mail->clearAllRecipients();

            }else {
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->addBCC($correo, $name);
                $cont++;
            }
            $totalCont++;
        }
        $mail->clearAllRecipients();
        if($sendDesarrollador){
            if($totalCorreo>0){
                $file = trim('logcorreos_'.strtotime(date('Y-m-d H:i:s')).".txt");
                $filePath = DOC_ROOT."/sendFiles/".$file;
                $open = fopen($filePath,"w");
                if ( $open ) {
                    fwrite($open, $logSend);
                    fclose($open);
                    $mail->addAttachment($filePath, $file);
                }
            }

            if(PROJECT_STATUS=='test'){
                $mail->addAddress(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
                $mail->addCC(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
            }else{
                $mail->addAddress(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
                $mail->addBCC(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
            }

            $mail->send();
            $mail->clearAllRecipients();
            if($totalCorreo>0)
             @unlink($filePath);
        }
        $mail->SmtpClose();
        unset($mail);

    }

    public function SendMultipleNotice($subject, $body, $to, $archivos = [], $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false) {

        $mail = new PHPMailer();
        $mail->addReplyTo($from, $fromName);
        $mail->setFrom($from, $fromName);
        $mail->Subject    = $subject;
        $mail->msgHTML($body);
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->SMTPAutoTLS  = false;
        $mail->SMTPKeepAlive=true;
        $mail->Host       = SMTP_HOST2;
        $mail->Port       = SMTP_PORT2;
        $mail->Username   = SMTP_USER2;
        $mail->Password   = SMTP_PASS2;
        $mail->SMTPDebug=0;

        $logSend = "Lista de correos enviados: ".chr(13).chr(10);;
        foreach($to as $email => $name) {

            if($email == EMAILCOORDINADOR) continue;

            try {
                $mail->addAddress($email, $name);
            } catch (Exception $e) {
                continue;
            }

            foreach ($archivos as $archivo) {
                if (is_file($archivo['url']))
                    $mail->addAttachment($archivo['url'], $archivo['name']);
            }

            try {
               if ($mail->send())
                    $logSend .="Se envia a ".$name."(".$email.")".chr(13).chr(10);
               else
                   $logSend .="Hubo un error en el envio a ".$name."(".$email.")".chr(13).chr(10);
            } catch (Exception $e) {

               $mail->getSMTPInstance()->reset();
            }

            $mail->clearAddresses();
            $mail->clearAttachments();
        }

        if($sendDesarrollador) {
            $file = trim('logcorreos_'.strtotime(date('Y-m-d H:i:s')).".txt");
            $filePath = DOC_ROOT."/sendFiles/".$file;
            $open = fopen($filePath,"w");
            if ( $open ) {
                fwrite($open, $logSend);
                fclose($open);
                if (is_file($filePath))
                    $mail->addAttachment($filePath, $file);
            }

            foreach ($archivos as $archivo) {
                if (is_file($archivo['url']))
                    $mail->addAttachment($archivo['url'], $archivo['name']);
            }

            if(PROJECT_STATUS=='test'){
                $mail->addAddress(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
                $mail->addCC(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
            }else{
                $mail->addAddress(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
                $mail->addBCC(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
            }

            $mail->send();
            $mail->clearAllRecipients();
            if(is_file($filePath))
                @unlink($filePath);
        }
        $mail->SmtpClose();
        unset($mail);
    }
}


?>
