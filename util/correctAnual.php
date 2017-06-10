<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT * FROM servicio WHERE (tipoServicioId = '".ANUAL."' OR tipoServicioId = '".DIM."')");
	$servicios = $util->DB()->GetResult();

	foreach($servicios as $servicio)
	{
		//print_r($servicio);
		$mes = explode("-", $servicio["inicioOperaciones"]);
		echo $mes[1];
		$util->DB()->setQuery("SELECT * FROM instanciaServicio WHERE servicioId = '".$servicio["servicioId"]."' ORDER BY date ASC");
		$result = $util->DB()->GetResult();
		
		$count = count($result);
		foreach($result as $key => $value)
		{
			echo "<br>";
			if($key >= $count - 1)
			{
				//continue;
			}
			echo $value["date"];
			echo $result[$key + 1]["date"];
			$diff = strtotime($value["date"]) - strtotime($result[$key + 1]["date"]);
			echo $diff = abs($diff / (3600 * 24));
			
			if($diff > 300)
			{
				$newDate = explode("-",$value["date"]);
				$addedDate = $newDate[0]."-".$mes[1]."-".$newDate[2];
				
				$util->DB()->setQuery("UPDATE instanciaServicio SET date = '".$addedDate."' WHERE instanciaServicioId = '".$value["instanciaServicioId"]."' LIMIT 1");
				echo $util->DB()->query;
				$util->DB()->UpdateData();

//				continue;
			}
			
			
			
		}
		//print_r($result);
	}
?>

