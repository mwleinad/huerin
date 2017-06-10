<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	exit();
	$db->setQuery("SELECT comprobanteId, timbreFiscal, rfc, comprobante.empresaId, comprobante.noCertificado, folio FROM comprobante 
	LEFT JOIN contract ON contract.contractId = comprobante.userId
	WHERE MONTH(fecha) = 10 AND status = '1' AND empresaId = '20' ORDER BY comprobanteId");
	$result = $db->GetResult();
	$uuids = array();
	foreach($result as $res)
	{
		$_SESSION["empresaId"] = $res["empresaId"];
		$rfcActivo = $rfc->getRfcActive();

		$db->setQuery("SELECT rfc FROM rfc WHERE rfcId = '".$rfcActivo."'");
		$card["rfc"] = $db->GetSingle();
		$card["comprobanteId"] = $res["comprobanteId"];
		
		$timbre = unserialize($res["timbreFiscal"]);
//		print_r($timbre);

		$root = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/password.txt";		
		$fh = fopen($root, 'r');
		$card["password"] = fread($fh, filesize($root));

		$card["path"] = DOC_ROOT."/empresas/".$res["empresaId"]."/certificados/".$rfcActivo."/".$res["noCertificado"].".cer.pfx";

		$card["timbre"] = $timbre;
		//$card["rfc"] = $res["rfc"];
		$card["folio"] = $res["folio"];
		array_push($uuids, $card);
	}
	
	foreach($uuids as $uuid)
	{
		print_r($uuid);
		$cancel = $pac->CancelaCfdi(USER_PAC, PW_PAC, $uuid["rfc"], $uuid["timbre"]["UUID"], $uuid["path"], $uuid["password"]);
		print_r($cancel);
		$db->setQuery("UPDATE comprobante SET status = '0' WHERE comprobanteId = '".$uuid["comprobanteId"]."'");
		$db->UpdateData();
		
	}
?>