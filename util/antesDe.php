<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT inicioOperaciones, servicioId, nombreServicio, periodicidad , contract.name 
	FROM servicio 
	JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
	JOIN contract ON contract.contractId = servicio.contractId WHERE 1");
	$servicios = $util->DB()->GetResult();
	
	foreach($servicios as $key => $servicio)
	{
		$util->DB()->setQuery("SELECT instanciaServicioId, date  FROM instanciaServicio WHERE date < '".$servicio["inicioOperaciones"]."' AND servicioId = '".$servicio["servicioId"]."' AND (status = 'activa' OR status = 'completa')");
		$servicios[$key]["antesDe"] = $util->DB()->GetResult();
		
		if(count($servicios[$key]["antesDe"]) <= 0)
		{
			unset($servicios[$key]);
		}
	}
	?>
  <table border="1">
    <tr>
      <td>Id</td>
      <td>Nombre</td>
      <td>Servicio</td>
      <td>Inicio Operaciones</td>
      <td># de Instancias antes de Fecha Inicio</td>
    </tr>  
  <?php 
	foreach($servicios as $key => $servicio)
	{
	?>
    <tr>
      <td><?php echo $servicio["servicioId"];?></td>
      <td><?php echo $servicio["name"];?>  </td>
      <td><?php echo $servicio["nombreServicio"];?>  </td>
      <td><?php echo $servicio["inicioOperaciones"];?>  </td>
      <td align="center"><?php echo count($servicio["antesDe"]);?>  </td>
    </tr>
  <?php 	
		//corregir instancias
		foreach($servicio["antesDe"] as $antes)
		{
			$db->setQuery("UPDATE instanciaServicio SET lastStatus = status, status = 'baja' WHERE instanciaServicioId = '".$antes["instanciaServicioId"]."'")	;
			//echo $db->query;
			$db->UpdateData();
		}
	}
	//print_r($servicios);
?>
</table>
Las instancias con fecha menor a la fecha de inicio de operaciones fueron dadas de baja.

