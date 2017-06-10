<?php
	
	include_once('../init.php');
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	$_POST["year"] = 2013;
	$_POST['departamentoId'] = '';
	
	$value = $workflow->StatusByMonth(527, 12, $_POST["year"]);
	print_r($value);
	
	$sql = "SELECT class, instanciaServicioId, instanciaServicio.status FROM instanciaServicio 
			LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
			WHERE 
			MONTH(instanciaServicio.date) = '12' 
			AND YEAR(instanciaServicio.date) = '2013'
			AND (servicio.status != 'baja'
      		OR servicio.status != 'inactiva')
			AND servicio.servicioId = '527'";
	$util->DB()->setQuery($sql);
	$data = $util->DB()->GetRow();
	echo '<br>';
	print_r($data);
	echo '<br>';
	
	$value = $workflow->StatusById(16603);
	
	print_r($value);
	
	exit;
	$clientes = $customer->Enumerate($deep, 371);

	foreach($clientes as $key => $cliente)
	{
		foreach($cliente["contracts"] as $keyContract => $contract)
		{
			if($clientes[$key]["contracts"][$keyContract]["instanciasServicio"])
			{
				foreach($clientes[$key]["contracts"][$keyContract]["instanciasServicio"] as $keyInstancia => $instancia)
				{
					if($_POST['departamentoId'] && $_POST['departamentoId'] != $instancia["departamentoId"])
					{
						unset($clientes[$key]["contracts"][$keyContract]["instanciasServicio"][$keyInstancia]);
						continue;
					}
					
					for($ii = 1; $ii <= 12; $ii++)
					{
						$clientes[$key]["contracts"][$keyContract]["instanciasServicio"][$keyInstancia]["instancias"][$ii] = $workflow->StatusByMonth($instancia["servicioId"], $ii , $_POST["year"]);
					}
				}
			}//if
		}
	}
	
	echo '<pre>';
	print_r($clientes);
	
	exit;

?>	