<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT * FROM servicio
		WHERE tipoServicioId = '".SERVICIO_CONTABILIDAD."' AND contractId = 25");
		echo $util->DB()->query;
	$servicios = $util->DB()->GetResult();

	$contratos = array();	
	
	foreach($servicios as $servicio)
	{
		$contratos[$servicio["contractId"]]["contract"] = $servicio["contractId"];
		$contratos[$servicio["contractId"]]["count"]++;
	}
	
	foreach($contratos as $key => $contrato)
	{
		if($contrato["count"] < 2)
		{
			unset($contratos[$key]);
		}
	}
	print_r($contratos);
?>