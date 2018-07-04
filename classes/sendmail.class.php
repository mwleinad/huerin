<?php

//require_once(DOC_ROOT."/phpmailer/class.phpmailer.php");
class SendMail extends Main
{
		
	public function Prepare($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema") 
	{
			$mail = new PHPMailer(); // defaults to using php "mail()"
			
			$subject= utf8_decode($subject);
		 	$fromName = utf8_decode($fromName);

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
	//		$mail->SMTPSecure="ssl";
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
	}

	public function PrepareMultiple($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$cc=array())
	{
			$mail = new PHPMailer(); // defaults to using php "mail()"
			
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
            }
			$mail->Subject    = $subject;
			$mail->MsgHTML($body);
            $mail->IsSMTP();
			$mail->SMTPAuth   = true;
            $mail->Host       = SMTP_HOST2;
            $mail->Port       = SMTP_PORT2;
            $mail->Username   = SMTP_USER2;
            $mail->Password   = SMTP_PASS2;
	//		$mail->SMTPSecure="ssl";
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
    public function PrepareMultipleHidden($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        $mail = new PHPMailer(); // defaults to using php "mail()"

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
        //		$mail->SMTPSecure="ssl";
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
        //		$mail->SMTPSecure="ssl";
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
            if($totalCorreo==1){
                $logSend .="Se envia a ".$name."(".$correo.")".chr(13).chr(10);
                $mail->AddAddress($correo, $name);
                $cont++;
            }
            if($cont>=50||$totalCont==$totalCorreo){
                //resetear contador
                $cont=1;
                $add= "notice".$lote."@braunhuerin.com.mx";
                //$add="isc061990@gmail.com";
                //$mail->AddAddress($add,'Notice'.$lote);
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
            $file = trim('f'.strtotime(date('Y-m-d H:i:s')).".txt");
            $filePath = DOC_ROOT."/sendFiles/".$file;
            $open = fopen($filePath,"w");
            if ( $open ) {
                fwrite($open, $logSend);
                fclose($open);
                $mail->AddAttachment($filePath, $file);
            }

            $mail->AddAddress(EMAIL_DEV,'DESARROLLADOR'.date('Y-m-d H:i:s',time()));
            $mail->Send();
            $mail->clearAllRecipients();
            @unlink($filePath);
        }
        $mail->SmtpClose();
        unset($mail);

    }

}


?>