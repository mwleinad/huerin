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
			$mail->Host       = "mail.avantika.com.mx";
			$mail->Port       = 587;
			$mail->Username   = "smtp@avantika.com.mx";
			$mail->Password   = "smtp1234";
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
			$mail->Host       = "mail.avantika.com.mx";
			$mail->Port       = 587;
			$mail->Username   = "smtp@avantika.com.mx";
			$mail->Password   = "smtp1234";
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
        $mail->Host       = "mail.avantika.com.mx";
        $mail->Port       = 587;
        $mail->Username   = "smtp@avantika.com.mx";
        $mail->Password   = "smtp1234";
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
    public function PrepareMultipleVisible($subject, $body, $to, $toName, $attachment = "", $fileName = "", $attachment2 = "", $fileName2 = "", $from = "sistema@braunhuerin.com.mx", $fromName = "Administrador del Sistema",$sendDesarrollador=false)
    {
        foreach($to as $correo => $name)
        {
            //crear un objeto mail por cada correo
            $mail = new PHPMailer(); // defaults to using php "mail()"

            $mail->AddReplyTo($from, $fromName);
            $mail->SetFrom($from, $fromName);
            $mail->Subject    = $subject;
            $mail->MsgHTML($body);
            $mail->IsSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = "mail.avantika.com.mx";
            $mail->Port       = 587;
            $mail->Username   = "smtp@avantika.com.mx";
            $mail->Password   = "smtp1234";
            //		$mail->SMTPSecure="ssl";
            $mail->SMTPDebug=1;
            if($attachment != "")
            {
                $mail->AddAttachment($attachment, $fileName);
            }

            if($attachment2 != "")
            {
                $mail->AddAttachment($attachment2, $fileName2);
            }
            $mail->AddAddress($correo, $name);
            $mail->Send();
            unset($mail);
        }
        if($sendDesarrollador){
            //crear un objeto mail por cada correo
            $mail = new PHPMailer(); // defaults to using php "mail()"

            $mail->AddReplyTo($from, $fromName);
            $mail->SetFrom($from, $fromName);
            $mail->Subject    = $subject;
            $mail->MsgHTML($body);
            $mail->IsSMTP();
            $mail->SMTPAuth   = true;
            $mail->Host       = "mail.avantika.com.mx";
            $mail->Port       = 587;
            $mail->Username   = "smtp@avantika.com.mx";
            $mail->Password   = "smtp1234";
            //		$mail->SMTPSecure="ssl";
            $mail->SMTPDebug=1;
            if($attachment != "")
            {
                $mail->AddAttachment($attachment, $fileName);
            }

            if($attachment2 != "")
            {
                $mail->AddAttachment($attachment2, $fileName2);
            }
            $mail->AddAddress(EMAIL_DEV, 'DESARROLLADOR');
            $mail->Send();
            unset($mail);
        }

    }

}


?>