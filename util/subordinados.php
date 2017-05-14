<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

		$util->DB()->setQuery("SELECT name FROM personal
		WHERE personalId = '".$_GET["usuario"]."'");
		$miUsuario = $util->DB()->GetSingle();
	
		$util->DB()->setQuery("SELECT *, contract.name AS name, customer.nameContact AS customerName FROM contract
		JOIN customer ON customer.customerId = contract.customerId
		WHERE contract.customerId = '".$_GET["cliente"]."'");
		$contracts = $util->DB()->GetResult();

   	$personal->setPersonalId($_GET["usuario"]);
   	$subordinados = $personal->Subordinados();
		print_r($subordinados);