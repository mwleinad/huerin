<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT * FROM contract
		JOIN customer ON customer.customerId = contract.customerId
		WHERE contract.name = ''");
	$servicios = $util->DB()->GetResult();
	
	foreach($servicios as $contrato)
	{
		echo $contrato["nameContact"];
		echo "<br>";
	}
?>

