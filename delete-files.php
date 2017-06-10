<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	$db->setQuery("SELECT timbreFiscal, rfc, comprobante.empresaId, comprobante.noCertificado, folio, serie FROM comprobante 
	LEFT JOIN contract ON contract.contractId = comprobante.userId
	WHERE MONTH(fecha) = 10 AND empresaId = '20'");
	$result = $db->GetResult();
	exit();
	foreach($result as $res)
	{
		$_SESSION["empresaId"] = $res["empresaId"];
		$rfcActivo = $rfc->getRfcActive();

		$db->setQuery("SELECT rfc FROM rfc WHERE rfcId = '".$rfcActivo."'");
		$card["rfc"] = $db->GetSingle();

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/SIGN_".$res["empresaId"]."_".$res["serie"]."_".$res["folio"].".xml";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/pdf/".$res["empresaId"]."_".$res["serie"]."_".$res["folio"].".pdf";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/qr/".$res["empresaId"]."_".$res["serie"]."_".$res["folio"].".png";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/".$res["empresaId"]."_".$res["serie"]."_".$res["folio"].".xml";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/".$res["empresaId"]."_".$res["serie"]."_".$res["folio"].".zip";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";

		$path = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/".$res["empresaId"]."_".$res["serie"]."_".$res["folio"]."_signed.zip";
		if(file_exists($path))
		{
			unlink($path);
			echo "si";
		}
		echo "<br>";
		
	}
	
?>