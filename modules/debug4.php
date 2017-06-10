<?php

	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	session_start();
	/*
	if($_GET['tipo'] = 'instancias'){
		$servicioDebug->CreateServiceInstances();
		echo 'Done';
		exit;
	}
	*/
		
	$sql = "SELECT servicioId FROM instanciaServicio
			GROUP BY servicioId";
	$util->DB()->setQuery($sql);
	$servicios = $util->DB()->GetResult();
	
	$total = 0;
	foreach($servicios as $serv){
				
		$sql = "SELECT date FROM instanciaServicio 
				WHERE servicioId = ".$serv['servicioId'].'
				ORDER BY date DESC
				LIMIT 1';
		$util->DB()->setQuery($sql);
		$fecha = $util->DB()->GetSingle();
		
		if($fecha != '2014-10-01')
			continue;
		
		echo 'servicioId = '.$serv['servicioId'];
		echo '<br>';
		echo $fecha;
		echo '<br><br>';		
		
		$total++;
		
	}//foreach
	
	echo '<br><br>';
	echo 'TOTAL = '.$total;
	
	exit;

?>
