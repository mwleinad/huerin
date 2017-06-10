<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT * FROM servicio WHERE (tipoServicioId = '".RIF."')");
	$servicios = $util->DB()->GetResult();

	foreach($servicios as $servicio)
	{
		$util->DB()->setQuery("SELECT * FROM instanciaServicio WHERE servicioId = '".$servicio["servicioId"]."' ORDER BY date ASC");
		$result = $util->DB()->GetResult();
		
		$count = count($result);
		foreach($result as $key => $value)
		{
			echo "<br>";

			//ultima instancia
			if($key >= $count - 1)
			{
				if($mes[1] % 2 == 1)
				{
					$add = "+1 month";
					$addedDate = strtotime ( $add , strtotime ( $value["date"] ) ) ;
					$addedDate = date ( 'Y-m-d' , $addedDate );

					$util->DB()->setQuery("UPDATE instanciaServicio SET date = '".$addedDate."' WHERE instanciaServicioId = '".$value["instanciaServicioId"]."' LIMIT 1");
					//echo $util->DB()->query;
					$util->DB()->UpdateData();
				}
			}
			echo "date:".$value["date"];
			echo " ";
			echo "compare:".$result[$key + 1]["date"];
			echo " ";
			$diff = strtotime($value["date"]) - strtotime($result[$key + 1]["date"]);
			echo $diff = abs($diff / (3600 * 24));
			echo "<br>";
			
			if($diff > 50)
			{
				//checar si mes es impar
				$mes = explode("-", $value["date"]);
				
				if($mes[1] % 2 == 1)
				{
					$add = "+1 month";
					$addedDate = strtotime ( $add , strtotime ( $value["date"] ) ) ;
					$addedDate = date ( 'Y-m-d' , $addedDate );
					
					$util->DB()->setQuery("UPDATE instanciaServicio SET date = '".$addedDate."' WHERE instanciaServicioId = '".$value["instanciaServicioId"]."' LIMIT 1");
					//echo $util->DB()->query;
					$util->DB()->UpdateData();
					
				}
			}
			
			
			
		}
		//print_r($result);
	}
?>

