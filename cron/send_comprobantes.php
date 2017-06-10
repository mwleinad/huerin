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
exit;
	define('DOC_ROOT', $docRoot);

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

		$db->setQuery("UPDATE comprobante SET sent = 'si' WHERE comprobanteId = '".$factura["comprobanteId"]."'");
		$db->UpdateData();
		echo "Enviado Comprobante numero:".$factura["comprobanteId"];
		echo "<br>";
		exit();
	}
	echo "Cron Completado Satisfactoriamente";
?>