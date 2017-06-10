<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT servicio.*, customer.active AS customerActive, contract.type AS contractType FROM servicio 
	LEFT JOIN contract ON contract.contractId = servicio.contractId
	LEFT JOIN customer ON customer.customerId = contract.customerId
	WHERE (tipoServicioId = '".ANUAL."') AND customer.active = '1' ORDER BY customer.customerId");
	$servicios = $util->DB()->GetResult();
//	echo count($servicios);
	foreach($servicios as $servicioKey => $servicio)
	{
		if($servicio["contractType"] == "Persona Moral")
		{
			$monthComparison = 3;
		}
		else
		{
			$monthComparison = 4;
		}
		
		$mes = explode("-", $servicio["inicioOperaciones"]);
		$util->DB()->setQuery("SELECT instanciaServicioId, date, class FROM instanciaServicio WHERE servicioId = '".$servicio["servicioId"]."' AND status != 'baja' ORDER BY date ASC");
		$instancias = $util->DB()->GetResult();
		
		$count = count($instancias);
		
		$divided = DivideInYears($instancias);
		
		foreach($divided as $keyDivided => $result)
		{
			//si en el anio solo hubo un servicio no hay que hacer nada
			if(count($result) < 2)
			{
				//print_r($result);	
				foreach($result as $key => $value)
				{
					$date = explode("-", $value["date"]);
					$month = $date[1];
					
					if($month == $monthComparison)
					{
						unset($divided[$keyDivided]);
						continue;
					}
					//echo $servicio["contractType"];
					
					//update to month
					//print_r($value);
					//print_r($date);
					$formDate = $date[0]."-".str_pad($monthComparison, 2, "0", STR_PAD_LEFT)."-".$date[2];
					
					$util->DB()->setQuery("UPDATE instanciaServicio SET date = '".$formDate."' WHERE instanciaServicioId = '".$value["instanciaServicioId"]."' LIMIT 1");
					echo $util->DB()->query;
					$util->DB()->UpdateData();
					
				}
			}
			else
			{
				$notInApril = 0;
				foreach($result as $key => $value)
				{
					$date = explode("-", $value["date"]);
					$month = $date[1];
					
					if($month != $monthComparison)
					{
						$notInApril++;
						$servicios[$servicioKey][$keyDivided]["notInApril"][] = $value;
					}
				}
				if($notInApril == 0)
				{
					unset($servicios[$servicioKey][$keyDivided]);
				}
			}
		}
		//not in april
		//print_r($result);
	}
	
//	print_r($servicios);
	
	foreach($servicios as $key => $servicio)
	{
		foreach($servicio as $keyServicio => $instancia)
		{
			//echo $keyServicio;
			if(is_numeric($keyServicio))
			{
				//print_r($servicio[$keyServicio]);
				foreach($servicio[$keyServicio]["notInApril"] as $id)
				{
					//print_r($id);
					$util->DB()->setQuery("UPDATE instanciaServicio SET lastStatus = status, status = 'baja' WHERE instanciaServicioId = '".$id["instanciaServicioId"]."' LIMIT 1");
					echo $util->DB()->query;
					$util->DB()->UpdateData();
				}
			}
		}
	}
	//echo count($servicios);
	//print_r($servicios);
	function DivideInYears($result)
	{
		$servicios = array();
		foreach($result as $key => $value)
		{
			$date = explode("-", $value["date"]);
			//print_r($date);
			$servicios[$date[0]][] = $value;
		}
		return $servicios;
	}
?>

