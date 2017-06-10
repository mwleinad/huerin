<?php

if(!$_SERVER["DOCUMENT_ROOT"])
{
	
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/opt/lampp/htdocs")
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
	
	$nowDate = date("Y-m-d");
	$result = $archivo->GetArchivoTwo();
	
	foreach($result  as $key => $value)
	{
	   $email = $value["email"];
	   $uname = $value["uname"];
	   $contract = $value["contractId"];
	   $ncomercial = $value["nombreComercial"];
	   $nameContact = $value["nameContact"];
	   $nuevaFecha = date("Y-m-d", strtotime($value["date"]."-2 month"));	   
	   $fecha = $util->ChangeDateFormat($value["date"]);
	   if($nowDate >= $nuevaFecha)
	   {
	     if($archivo->SendAlerta($email,$uname,$contract,$ncomercial,$nameContact,$fecha))
		 {
			 echo "Enviado ".$contract." - ".$uname." - ".$ncomercial." - ".$nameContact."<br>";
			 $email ="";
			 $uname = "";
			 $contract = "";
			 $ncomercial = "";
			 $nameContact = "";
		 }
		 else
		 {
			 echo "Hubo un problema al enviar la alerta con el contrato No ".$contract." (".$ncomercial.")";
			 echo "<br> del cliente ".$nameContact;
		     exit();
		 }
	     
	   }
	
	}
	
	
?>