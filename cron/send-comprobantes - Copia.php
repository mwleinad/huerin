<?php
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
//echo DOC_ROOT;

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');

	session_save_path("/tmp");
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$db->setQuery("SELECT * FROM comprobante WHERE sent = 'no'");
	$comprobantes = $db->GetResult();
	foreach($comprobantes as $Key => $factura)
	{
		$comprobante->SendComprobante($factura["comprobanteId"]);

		$db->setQuery("UPDATE comprobante SET sent = 'si' WHERE comprobanteId = '".$factura["comprobanteId"]."'");
		$db->UpdateData();
		echo "Enviado Comprobante numero:".$factura["comprobanteId"];
		echo "<br>";
//		exit();
	}
	echo "Cron Completado Satisfactoriamente";

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/send-comprobantes.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}
?>