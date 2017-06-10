<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT servicio.*, customer.active AS customerActive, contract.type AS contractType FROM servicio 
	LEFT JOIN contract ON contract.contractId = servicio.contractId
	LEFT JOIN customer ON customer.customerId = contract.customerId
	WHERE (tipoServicioId = '".RIF."') AND customer.active = '1' ORDER BY customer.customerId");
	$servicios = $util->DB()->GetResult();

	echo count($servicios);
	foreach($servicios as $servicioKey => $servicio)
	{
		$mes = explode("-", $servicio["inicioOperaciones"]);
		$util->DB()->setQuery("SELECT instanciaServicioId, date, class FROM instanciaServicio WHERE servicioId = '".$servicio["servicioId"]."' AND status != 'baja' ORDER BY date ASC");
		$instancias = $util->DB()->GetResult();
		
		$count = count($instancias);
		
		$divided = DivideInYears($instancias);
		//print_r($divided);
		
		foreach($divided as $keyDivided => $result)
		{
			//si en el anio solo hubo un servicio no hay que hacer nada
				$notInApril = 0;
				foreach($result as $key => $value)
				{
					$date = explode("-", $value["date"]);
					$month = $date[1];
					
					if($month % 2 == 1)
					{
						$notInApril++;
						$servicios[$servicioKey][$keyDivided]["notInApril"][] = $value;
					}
				}
				if($notInApril == 0)
				{
					unset($servicios[$servicioKey]);
				}
		}
		//not in april
		//print_r($result);
	}
	echo count($servicios);
	//print_r($servicios);
	
	
	foreach($servicios as $key => $servicio)
	{
		foreach($servicio as $keyServicio => $instancia)
		{
			//echo $keyServicio;
			if(is_numeric($keyServicio))
			{
				print_r($servicio[$keyServicio]);
				foreach($servicio[$keyServicio]["notInApril"] as $id)
				{
					$add = "+1 month";
					$addedDate = strtotime ( $add , strtotime ( $id["date"] ) ) ;
					$addedDate = date ( 'Y-m-d' , $addedDate );
					
					//print_r($id);
					$util->DB()->setQuery("UPDATE instanciaServicio SET date = '".$addedDate."' WHERE instanciaServicioId = '".$id["instanciaServicioId"]."' LIMIT 1");
					//echo $util->DB()->query;
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

