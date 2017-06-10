<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	exit();
	$db->setQuery("SELECT timbreFiscal, rfc, comprobante.empresaId, comprobante.noCertificado, folio, serie, comprobanteId FROM comprobante 
	LEFT JOIN contract ON contract.contractId = comprobante.userId
	WHERE MONTH(fecha) = 10 AND empresaId = '20'");
	$result = $db->GetResult();
	echo count($result);
	foreach($result as $res)
	{

		$db->setQuery("UPDATE instanciaServicio SET comprobanteId = 0 WHERE comprobanteId =  '".$res["comprobanteId"]."'");
//		echo $db->query;
		$db->UpdateData();
		
	}
	
?>