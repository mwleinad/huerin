<?php

	if(!$_SERVER["DOCUMENT_ROOT"])
		$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
	
	if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
		$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
	else
		$docRoot = $_SERVER['DOCUMENT_ROOT'];

	define('DOC_ROOT', $docRoot);

	session_save_path("/tmp");

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');

	if (!isset($_SESSION)) {
	  session_start();
	}
	
	$sql = 'SELECT COUNT(*) FROM instanciaServicio';
	$db->setQuery($sql);
	$totalRegs = $db->GetSingle();
		
	$sql = 'SELECT * FROM cron_class WHERE idCron = 1';
	$db->setQuery($sql);
	$infC = $db->GetRow();
	
	$bloque = $infC['bloque'];
	$ultimoBloque = $infC['ultimoBloque'];
		
	$sqlLim = 'LIMIT '.$ultimoBloque.','.$bloque;
	
	$sql = 'SELECT * FROM instanciaServicio 
			ORDER BY instanciaServicioId
			'.$sqlLim;
	$db->setQuery($sql);
	$workflows = $db->GetResult();
	
	foreach($workflows as $res){
		
		$instServId = $res['instanciaServicioId'];
		$infS = $workflow->StatusById($instServId);
		
		echo $instServId.' :: '.$res['servicioId'].' :: '.$res['status'].' :: '.$res['class'].' :: '.$infS['class'];
		echo '<br>';
		if($res['class'] != $infS['class']){
			$sql = 'UPDATE instanciaServicio SET class = "'.$infS['class'].'" 
					WHERE instanciaServicioId = "'.$instServId.'"';			
			$db->setQuery($sql);	
			$db->UpdateData();
		}	
		
	}//foreach
	
	$ultimoBloque += $bloque;
	
	if($ultimoBloque >= $totalRegs)
		$ultimoBloque = 0;
		
	echo $sql = 'UPDATE cron_class SET fecha = "'.date('Y-m-d H:i:s').'", ultimoBloque = "'.$ultimoBloque.'"
			WHERE idCron = 1';
	$db->setQuery($sql);	
	$db->UpdateData();
		
	echo "Completo";

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/update-class-workflow.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}

?>