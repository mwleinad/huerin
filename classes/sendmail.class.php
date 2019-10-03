<?php

//require_once(DOC_ROOT."/phpmailer/class.phpmailer.php");
class SendMail extends Main
{
		
	public function Prepare($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema") 
	{
			$mail = new PHPMailer(true); // defaults to using php "mail()"
			
			$subject= utf8_decode($subject);
		 	$fromName = utf8_decode($fromName);
		 	try{
                $mail->AddReplyTo($from, $fromName);
                $mail->SetFrom($from, $fromName);
                $mail->AddAddress($to, $toName);
                $mail->Subject    = $subject;
                $mail->MsgHTML($body);
                $mail->IsSMTP();
                $mail->SMTPAuth   = true;
                $mail->Host       = SMTP_HOST2;
                $mail->Port       = SMTP_PORT2;
                $mail->Username   = SMTP_USER2;
                $mail->Password   = SMTP_PASS2;
                $mail->Timeout=300;
                $mail->SMTPDebug = 0;
                if($attachment != "")
                {
                    $mail->AddAttachment($attachment, $fileName);
                }

                if($attachment2 != "")
                {
                    $mail->AddAttachment($attachment2, $fileName2);
                }

                $mail->Send();

            }catch(phpmailerException $e){
                return false;
            }catch(Exception $e){
                return false;
            }
        return true;
	}

	public function PrepareMultiple($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$cc=array())
	{
			$mail = new PHPMailer(true); // defaults to using php "mail()"
            try{
                $mail->AddReplyTo($from, $fromName);
                $mail->SetFrom($from, $fromName);
                foreach($to as $correo => $name)
                {
                    switch($name){
                        case 'Desarrollador':
                            if(count($to)>1)
                                $mail->AddBCC($correo, $name);
                            else
                                $mail->AddAddress($correo, $name);
                            break;
                        default:
                            $mail->AddAddress($correo, $name);
                            break;
                    }
                }
                if(count($cc)>0)
                {
                    foreach($cc as $ccEmail => $ccName)
                    {
                        $mail->AddBCC($ccEmail, $ccName);
                    }
                    $mail->AddBCC(EMAIL_DEV,'COPIA CARBON');
                }else{
                    $mail->AddAddress(EMAIL_DEV, 'DEVELOPER');
                }
                $mail->Subject    = $subject;
                $mail->MsgHTML($body);
                $mail->IsSMTP();
                $mail->SMTPAuth   = true;
                $mail->Host       = SMTP_HOST2;
                $mail->Port       = SMTP_PORT2;
                $mail->Username   = SMTP_USER2;
                $mail->Password   = SMTP_PASS2;
                $mail->SMTPDebug=0;
                $mail->Timeout=300;
                if($attachment != "")
                {
                    $mail->AddAttachment($attachment, $fileName);
                }

                if($attachment2 != "")
                {
                    $mail->AddAttachment($attachment2, $fileName2);
                }
                $mail->Send();
            }catch(phpmailerException $e){
                return false;
            }catch(Exception $e){
                return false;
            }

        return true;
	}
    public function PrepareMultipleHidden($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        $mail = new PHPMailer();

        $mail->AddReplyTo($from, $fromName);
        $mail->SetFrom($from, $fromName);
        foreach($to as $correo => $name)
        {
           $mail->AddBCC($correo, $name);
        }
        if($sendDesarrollador)
            $mail->AddBCC(EMAIL_DEV,'Desarrollador');

        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = SMTP_HOST2;
        $mail->Port       = SMTP_PORT2;
        $mail->Username   = SMTP_USER2;
        $mail->Password   = SMTP_PASS2;
        $mail->Timeout=300;
        $mail->SMTPDebug=0;

        if($attachment != "")
        {
            $mail->AddAttachment($attachment, $fileName);
        }

        if($attachment2 != "")
        {
            $mail->AddAttachment($attachment2, $fileName2);
        }
        if($mail->Send())
            return true;
        else
            return false;

    }
    public function PrepareMultipleNotice($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        //crear un objeto mail por cada correo
        $mail = new PHPMailer(); // defaults to using php "mail()"
        $mail->AddReplyTo($from, $fromName);
        $mail->SetFrom($from, $fromName);
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = SMTP_HOST2;
        $mail->Port       = SMTP_PORT2;
        $mail->Username   = SMTP_USER2;
        $mail->Password   = SMTP_PASS2;
        $mail->SMTPDebug=0;
        $mail->SMTPKeepAlive=true;
        if($attachment != "")
        {
            $mail->AddAttachment($attachment, $fileName);
        }

        if($attachment2 != "")
        {
            $mail->AddAttachment($attachment2, $fileName2);
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
                $mail->AddAddress($correo, $name);
                $cont++;
                $totalCont++;
            }
            if($cont>=50||$totalCont>=$totalCorreo){
                //en la ultima iteracion o por cada 50 no se incluye el ultimo se debe incluir
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->AddBCC($correo, $name);

                //resetear contador
                $cont=1;
                $mail->Send();
                $logSend.="Lote ".$lote." enviado".chr(13).chr(10);
                $lote++;
                $mail->clearAllRecipients();

            }else {
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->AddBCC($correo, $name);
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
                    $mail->AddAttachment($filePath, $file);
                }
            }

            if(PROJECT_STATUS=='test'){
                $mail->AddAddress(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
                $mail->AddCC(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
            }else{
                $mail->AddAddress(EMAILCOORDINADOR,'Rogelio Isaac Zetina Olazagasti');
                $mail->AddBCC(EMAIL_DEV,'DESARROLLADOR '.date('Y-m-d H:i:s',time()));
            }
            $mail->Send();
            $mail->clearAllRecipients();
            if($totalCorreo>0)
             @unlink($filePath);
        }
        $mail->SmtpClose();
        unset($mail);

    }

}


?>