<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

		$util->DB()->setQuery("SELECT name FROM personal
		WHERE personalId = '".$_GET["usuario"]."'");
		$miUsuario = $util->DB()->GetSingle();
	
   	$personal->setPersonalId($_GET["usuario"]);
   	$subordinados = $personal->Subordinados();
		echo count($subordinados);
		print_r($subordinados);

		foreach($subordinados as $subordinado) {
			$util->DB()->setQuery("SELECT personalId, name FROM personal
		WHERE personalId = '".$subordinado["personalId"]."'");
			$persona = $util->DB()->GetRow();
			print_r($persona);
		}
		
		$jefes = $personal->jefes($_GET["usuario"]);
		print_r($jefes);
		
		

