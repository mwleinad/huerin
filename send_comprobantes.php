<?php
	define('DOC_ROOT', '/var/www/html/huerin_test');

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$db->setQuery("SELECT * FROM comprobante WHERE sent = 'no'");
	$comprobantes = $db->GetResult();
	
	foreach($comprobantes as $Key => $factura)
	{
		$comprobante->SendComprobante($factura["comprobanteId"]);

		$db->setQuery("UPDATE comprobante SET sent = 'si' WHERe comprobanteId = '".$factura["comprobanteId"]."'");
		$db->UpdateData();
		echo "Enviado Comprobante numero:".$factura["comprobanteId"];
		echo "<br>";
	}
?>