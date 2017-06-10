<?php

class Month13 extends Comprobante
{
	function CreateMonth13()
	{
		global $months;
		$month = 11;
		$year = date("Y");
		echo "<pre>";
		$init = microtime();
		
		$this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '15' ORDER BY rfcId ASC LIMIT 1");
		$emisorHuerin = $this->Util()->DB()->GetRow();

		$this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '20' ORDER BY rfcId ASC LIMIT 1");
		$emisorBraun = $this->Util()->DB()->GetRow();

		$this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '21' ORDER BY rfcId ASC LIMIT 1");
		$emisorBHSC = $this->Util()->DB()->GetRow();

		$this->Util()->DB()->setQuery("SELECT * FROM customer");
		$clientes = $this->Util()->DB()->GetResult();
		
		$data = array();
		foreach($clientes as $key => $cliente)
		{
			if($cliente["active"] == "1")
			{
				$data["clientes"]["activo"][] = $cliente;
			}
			else
			{
				$data["clientes"]["inactivo"][] = $cliente;
			}
		}
?>
  <table border="1" width="600">
  <tr>
  <td>Concepto</td>
  <td>Braun Huerin SC</td>
  <td>Jacobo Braun</td>
	  <td>BHSC</td>
  <td>Total</td>
  </tr>
  <tr>
  <td>Clientes Totales</td>
  <td>N/A</td>
  <td>N/A</td>
	  <td>N/A</td>
  <td><?php echo count($clientes) ?></td>
  </tr>

  <tr>
  <td>Clientes Activos</td>
  <td>N/A</td>
  <td>N/A</td>
  <td><b><?php echo count($data["clientes"]["activo"]) ?></b></td>
  </tr>

  <tr>
  <td>Clientes Inactivos</td>
  <td>N/A</td>
  <td>N/A</td>
	  <td>N/A</td>
  <td><?php echo count($data["clientes"]["inactivo"]) ?></td>
  </tr>
<?php 
	//ya no necesitamos los clientes inactivos
	unset($data["clientes"]["inactivo"]);

	//clientes marcados que no se genere
	foreach($data["clientes"]["activo"] as $key => $cliente)
	{
		$this->Util()->DB()->setQuery("SELECT noFactura13 FROM customer WHERE customerId = '".$cliente["customerId"]."'");
		$noFactura = $this->Util()->DB()->GetSingle();
		
		if($noFactura == "Si")
		{
			$data["clientesSinFactura13"]++;
			unset($data["clientes"]["activo"][$key]);
			continue;
		}
	}
	
?>		
  <tr>
  <td>Clientes sin Factura 13</td>
  <td>N/A</td>
  <td>N/A</td>
	  <td>N/A</td>
  <td><?php echo $data["clientesSinFactura13"]?></td>
  </tr>
<?php 		
	foreach($data["clientes"]["activo"] as $key => $cliente)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM contract WHERE customerId = '".$cliente["customerId"]."'");
		$contratos = $this->Util()->DB()->GetResult();
		
		if(count($contratos) == 0)
		{
			$data["clientesSinContrato"]++;
			unset($data["clientes"]["activo"][$key]);
			continue;
		}
		
		$data["totalContratos"] += count($contratos); 
		
		foreach($contratos as $keyContrato => $contrato)
		{
			if($contrato["activo"] == "Si")
			{
				$data["totalContratosActivos"]++;

				if($contrato["facturador"] == "Braun")
				{
					$data["contratosBraun"][] = $contrato; 	
				}
				elseif($contrato["facturador"] == "BHSC")
				{
					$data["contratosBhsc"][] = $contrato;
				}
				elseif($contrato["facturador"] == "Huerin")
				{
					$data["contratosHuerin"][] = $contrato; 	
				}
			}
			else
			{
				$data["totalContratosInactivos"]++; 
			}
		}
	}
	
?>		
  <tr>
  <td>Clientes sin Razones Sociales</td>
  <td></td>
  <td></td>
	  <td></td>
	  <td><?php echo $data["clientesSinContrato"]?></td>
  </tr>

	<?php $clientesActivosMenosSinContrato = count($data["clientes"]["activo"]) - $data["clientesSinContrato"];?>
  <tr>
  <td>Clientes Activos MENOS Clientes ACTIVOS sin Razones Sociales</td>
  <td></td>
  <td></td>
	  <td></td>
  <td><b><?php echo count($data["clientes"]["activo"]); ?></b></td>
  </tr>

  <tr>
  <td>Razones Sociales Totales</td>
  <td></td>
  <td></td>
	  <td></td>
  <td><b><?php echo $data["totalContratos"]; ?></b></td>
  </tr>

  <tr>
  <td>Razones Sociales Activas</td>
  <td><?php echo count($data["contratosHuerin"])?></td>
  <td><?php echo count($data["contratosBraun"])?></td>
	  <td><?php echo count($data["contratosBhsc"])?></td>
  <td><b><?php echo $data["totalContratosActivos"]; ?></b></td>
  </tr>

  <tr>
  <td>Razones Sociales Inactivas</td>
  <td></td>
  <td></td>
	  <td></td>
  <td><?php echo $data["totalContratosInactivos"]; ?></td>
  </tr>

<?php
	//obtener servicios
	foreach($data["contratosHuerin"] as $key => $contratoHuerin)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId = '".$contratoHuerin["contractId"]."'");
		$servicios = $this->Util()->DB()->GetResult();

		if(count($servicios) == 0)
		{
			$data["clientesSinServicios"]++;
			unset($data["contratosHuerin"][$key]);
			continue;
		}
		
		$data["totalServicios"] += count($servicios); 
		$data["totalServiciosHuerin"] += count($servicios); 
		
		foreach($servicios as $keyServicio => $servicio)
		{
			if($servicio["status"] == "activo")
			{
				$data["totalServiciosActivos"]++; 
				
				$data["servicios"][] = $servicio; 	
				$data["serviciosHuerin"][] = $servicio; 	
			}
			else
			{
				$data["totalServiciosInactivos"]++; 
			}
		}
	}


	foreach($data["contratosBraun"] as $key => $contratoHuerin)
	{
		$this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId = '".$contratoHuerin["contractId"]."'");
		$servicios = $this->Util()->DB()->GetResult();

		if(count($servicios) == 0)
		{
			$data["clientesSinServicios"]++;
			unset($data["contratosBraun"][$key]);
			continue;
		}
		
		$data["totalServicios"] += count($servicios); 
		$data["totalServiciosBraun"] += count($servicios); 
		
		foreach($servicios as $keyServicio => $servicio)
		{
			if($servicio["status"] == "activo")
			{
				$data["totalServiciosActivos"]++; 
				
				$data["servicios"][] = $servicio; 	
				$data["serviciosBraun"][] = $servicio; 	
			}
			else
			{
				$data["totalServiciosInactivos"]++; 
			}
		}
	}

foreach($data["contratosBhsc"] as $key => $contratoBhsc)
{
	$this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId = '".$contratoBhsc["contractId"]."'");
	$servicios = $this->Util()->DB()->GetResult();

	if(count($servicios) == 0)
	{
		$data["clientesSinServicios"]++;
		unset($data["contratosBhsc"][$key]);
		continue;
	}

	$data["totalServicios"] += count($servicios);
	$data["totalServiciosBhsc"] += count($servicios);

	foreach($servicios as $keyServicio => $servicio)
	{
		if($servicio["status"] == "activo")
		{
			$data["totalServiciosActivos"]++;

			$data["servicios"][] = $servicio;
			$data["serviciosBhsc"][] = $servicio;
		}
		else
		{
			$data["totalServiciosInactivos"]++;
		}
	}
}
?>
  <tr>
  <td>Servicios Totales de Razones Sociales Activos</td>
  <td><b><?php echo $data["totalServiciosHuerin"]; ?></b></td>
  <td><b><?php echo $data["totalServiciosBraun"]; ?></b></td>
	  <td><b><?php echo $data["totalServiciosBhsc"]; ?></b></td>
  <td><b><?php echo $data["totalServicios"]; ?></b></td>
  </tr>

  <tr>
  <td>Servicios Activos</td>
  <td><?php echo count($data["serviciosHuerin"])?></td>
  <td><?php echo count($data["serviciosBraun"])?></td>
	  <td><?php echo count($data["serviciosBhsc"])?></td>
  <td><b><?php echo $data["totalServiciosActivos"]; ?></b></td>
  </tr>

  <tr>
  <td>Servicios Inactivos</td>
  <td></td>
  <td></td>
	  <td></td>
  <td><?php echo $data["totalServiciosInactivos"]; ?></td>
  </tr>

<?php

	//remover costo 0
	foreach($data["serviciosHuerin"] as $key => $servicio)
	{
		if($servicio["costo"] <= 0)
		{
			$data["costo0Huerin"]++;
			$data["totalCosto0"]++;
			unset($data["serviciosHuerin"][$key]);
			continue;
		}
	}

	foreach($data["serviciosBraun"] as $key => $servicio)
	{
		if($servicio["costo"] <= 0)
		{
			$data["costo0Braun"]++;
			$data["totalCosto0"]++;
			unset($data["serviciosBraun"][$key]);
			continue;
		}
	}

foreach($data["serviciosBhsc"] as $key => $servicio)
{
	if($servicio["costo"] <= 0)
	{
		$data["costo0Bhsc"]++;
		$data["totalCosto0"]++;
		unset($data["serviciosBhsc"][$key]);
		continue;
	}
}

	$data["totalCostoMayor0"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]);

?>
  <tr>
  <td>Servicios con Costo == 0</td>
  <td><?php //echo $data["costo0Huerin"]?></td>
  <td><?php //echo $data["costo0Braun"]?></td>
  <td><?php echo $data["totalCosto0"]; ?></td>
  </tr>
  <tr>
  <td>Servicios con Costo > 0</td>
  <td><?php echo count($data["serviciosHuerin"])?></td>
  <td><?php echo count($data["serviciosBraun"])?></td>
	  <td><?php echo count($data["serviciosBhsc"])?></td>
  <td><b><?php echo $data["totalCostoMayor0"]; ?></b></td>
  </tr>
<?php 	

	///quitar los que la fecha de facturacion no es ha iniciado
	foreach($data["serviciosHuerin"] as $key => $servicio)
	{
		if($servicio["inicioFactura"] == "0000-00-00")
		{
			unset($data["serviciosHuerin"][$key]);
			continue;
		}
		
		$fecha = explode("-", $servicio["inicioFactura"]);
		if($fecha[0] > $year)
		{
			$data["fechaPosteriorHuerin"]++;
			$data["fechaPosterior"]++;
			unset($data["serviciosHuerin"][$key]);
			continue;
		}

		if($fecha[1] > $month && $fecha[0] == $year)
		{
			$data["fechaPosteriorHuerin"]++;
			$data["fechaPosterior"]++;
			unset($data["serviciosHuerin"][$key]);
			continue;
		}
	}

	foreach($data["serviciosBraun"] as $key => $servicio)
	{
		if($servicio["inicioFactura"] == "0000-00-00")
		{
			unset($data["serviciosBraun"][$key]);
			continue;
		}
		
		$fecha = explode("-", $servicio["inicioFactura"]);
		if($fecha[0] > $year)
		{
			$data["fechaPosteriorBraun"]++;
			$data["fechaPosterior"]++;
			unset($data["serviciosBraun"][$key]);
			continue;
		}

		if($fecha[1] > $month && $fecha[0] == $year)
		{
			$data["fechaPosteriorBraun"]++;
			$data["fechaPosterior"]++;
			unset($data["serviciosBraun"][$key]);
			continue;
		}
	}

foreach($data["serviciosBhsc"] as $key => $servicio)
{
	if($servicio["inicioFactura"] == "0000-00-00")
	{
		unset($data["serviciosBhsc"][$key]);
		continue;
	}

	$fecha = explode("-", $servicio["inicioFactura"]);
	if($fecha[0] > $year)
	{
		$data["fechaPosteriorBhsc"]++;
		$data["fechaPosterior"]++;
		unset($data["serviciosBhsc"][$key]);
		continue;
	}

	if($fecha[1] > $month && $fecha[0] == $year)
	{
		$data["fechaPosteriorBhsc"]++;
		$data["fechaPosterior"]++;
		unset($data["serviciosBhsc"][$key]);
		continue;
	}
}
	
		$data["totalFechaPosterior"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]) + count($data["serviciosBhsc"]);

	
	?>

  <tr>
  <td>Inicio Factura Posterior a Fecha</td>
  <td><?php //echo $data["costo0Huerin"]?></td>
  <td><?php //echo $data["costo0Braun"]?></td>
  <td><?php echo $data["fechaPosterior"]; ?></td>
  </tr>
  <tr>
  <td>Inicio de Factura Correcto</td>
  <td><?php echo count($data["serviciosHuerin"])?></td>
  <td><?php echo count($data["serviciosBraun"])?></td>
	  <td><?php echo count($data["serviciosBhsc"])?></td>
  <td><b><?php echo $data["totalFechaPosterior"]; ?></b></td>
  </tr>
<?php  
//quitamos braun por lo pronto
//unset($data["serviciosBraun"]);
//$data["serviciosBraun"] = array();
//quitar instancia de servicio
	///quitar los que la fecha de facturacion no es ha iniciado
	
	foreach($data["serviciosHuerin"] as $key => $servicio)
	{
		$sql = "SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		LEFT JOIN customer ON customer.customerId = contract.customerId 
		WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'";
		$this->Util()->DB()->setQuery($sql);
		$row = $this->Util()->DB()->GetRow();

		if(!$row)
		{
			$data["noInstanciaHuerin"]++;
			$data["noInstancia"]++;
			unset($data["serviciosHuerin"][$key]);
			continue;
		}

		$data["serviciosHuerin"][$key] = $row;
	}

	foreach($data["serviciosBraun"] as $key => $servicio)
	{
		$sql = "SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		LEFT JOIN customer ON customer.customerId = contract.customerId 
		WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'";
		$this->Util()->DB()->setQuery($sql);
		$row = $this->Util()->DB()->GetRow();
		
		if(!$row)
		{
			$data["noInstanciaBraun"]++;
			$data["noInstancia"]++;
			unset($data["serviciosBraun"][$key]);
			continue;
		}

		$data["serviciosBraun"][$key] = $row;
	}

foreach($data["serviciosBhsc"] as $key => $servicio)
{
	$sql = "SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		LEFT JOIN customer ON customer.customerId = contract.customerId
		WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'";
	$this->Util()->DB()->setQuery($sql);
	$row = $this->Util()->DB()->GetRow();

	if(!$row)
	{
		$data["noInstanciaBhsc"]++;
		$data["noInstancia"]++;
		unset($data["serviciosBhsc"][$key]);
		continue;
	}

	$data["serviciosBhsc"][$key] = $row;
}
		$data["totalInstancias"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]) + count($data["serviciosBhsc"]);
	
?>
  <tr>
  <td>Sin Instancia de Servicio</td>
  <td><?php echo $data["noInstanciaHuerin"]?></td>
  <td><?php echo $data["noInstanciaBraun"]?></td>
	  <td><?php echo $data["noInstanciaBhsc"]?></td>
  <td><?php echo $data["noInstancia"]; ?></td>
  </tr>
  <tr>
  <td>Con Instancia de Servicio (Facturas teoricas)</td>
  <td><b><?php echo count($data["serviciosHuerin"])?></b></td>
  <td><b><?php echo count($data["serviciosBraun"])?></b></td>
	  <td><b><?php echo count($data["serviciosBhsc"])?></b></td>
  <td><b><?php echo $data["totalInstancias"]; ?></b></td>
  </tr>
<?php
	//facturadas
	foreach($data["serviciosBraun"] as $key => $servicio)
	{
		$sql = "SELECT COUNT(*) FROM facturaEspecial WHERE contractId = '".$servicio["contractId"]."' AND year = '".$year."'";
		$this->Util()->DB()->setQuery($sql);
		$count = $this->Util()->DB()->GetSingle();
		
		if($count!= 0)
		{
			$data["facturadaBraun"]++;
			$data["facturada"]++;
			//echo "jere";
			unset($data["serviciosBraun"][$key]);
			continue;
		}
		
		if(!$data["facturadaBraun"])
		{
			$data["facturadaBraun"] = 0;
		}
	}

	foreach($data["serviciosHuerin"] as $key => $servicio)
	{
		$sql = "SELECT COUNT(*) FROM facturaEspecial WHERE contractId = '".$servicio["contractId"]."' AND year = '".$year."'";
		$this->Util()->DB()->setQuery($sql);
		$count = $this->Util()->DB()->GetSingle();
		
		if($count!= 0)
		{
			$data["facturadaHuerin"]++;
			$data["facturada"]++;
			unset($data["serviciosHuerin"][$key]);
			//continue;
		}
		
		if(!$data["facturadaHuerin"])
		{
			$data["facturadaHuerin"] = 0;
		}
		
	}

foreach($data["serviciosBhsc"] as $key => $servicio)
{
	$sql = "SELECT COUNT(*) FROM facturaEspecial WHERE contractId = '".$servicio["contractId"]."' AND year = '".$year."'";
	$this->Util()->DB()->setQuery($sql);
	$count = $this->Util()->DB()->GetSingle();

	if($count!= 0)
	{
		$data["facturadaBhsc"]++;
		$data["facturada"]++;
		unset($data["serviciosBhsc"][$key]);
		//continue;
	}

	if(!$data["facturadaBhsc"])
	{
		$data["facturadaBhsc"] = 0;
	}

}

	if(!$data["facturada"])
	{
		$data["facturada"] = 0;
	}
	
	$data["totalFacturadas"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]) + count($data["serviciosBhsc"]);
	
?>	
  <tr>
  <td>Facturadas</td>
  <td><?php echo $data["facturadaHuerin"]?></td>
  <td><?php echo $data["facturadaBraun"]?></td>
	  <td><?php echo $data["facturadaBhsc"]?></td>
  <td><?php echo $data["facturada"]; ?></td>
  </tr>
  <tr>
  <td>Por Facturar</td>
  <td><b><?php echo count($data["serviciosHuerin"])?></b></td>
  <td><b><?php echo count($data["serviciosBraun"])?></b></td>
	  <td><b><?php echo count($data["serviciosBhsc"])?></b></td>
  <td><b><?php echo $data["totalFacturadas"]; ?></b></td>
  </tr>
  </table>
<?php 	
		$countFactAll = 0;
		if(!$data["serviciosBraun"])
		{
			$data["serviciosBraun"] = array();
		}

		if(!$data["serviciosHuerin"])
		{
			$data["serviciosHuerin"] = array();
		}

		if(!$data["serviciosBhsc"])
		{
			$data["serviciosBhsc"] = array();
		}

		//$servicio = array_merge($data["serviciosHuerin"], $data["serviciosBraun"], $data["serviciosBhsc"]);
		$servicio = array_merge($data["serviciosHuerin"], $data["serviciosBhsc"]);

		$idContracts = array();
		$contratos = array();
		foreach($servicio as $res){

			$contractId = $res['contractId'];
			$contratos[$contractId][] = $res;

		}//foreach

		unset($data);
		foreach($contratos as $contractId => $servicios){
			//	echo "jere";
			$this->Util()->DB()->setQuery("SELECT facturador FROM contract WHERE contractId = '".$contractId."'");
			$value['facturador'] = $this->Util()->DB()->GetSingle();

			if($value["facturador"] == "BHSC")
			{
				$empresaIdFacturador = 21;
				$_SESSION['empresaId'] = 21;
				$emisor = $emisorBHSC;
				$nombreFactura = "Factura";
			}
			if($value["facturador"] == "Huerin")
			{
				$empresaIdFacturador = 15;
				$_SESSION['empresaId'] = 15;
				$emisor = $emisorHuerin;
				$nombreFactura = "Factura";
			}
			elseif($value["facturador"] == "Braun")
			{
				$empresaIdFacturador = 20;
				$_SESSION['empresaId'] = 20;
				$emisor = $emisorBraun;
				$nombreFactura = "Recibo Honorarios";
			}

			echo '*****************';
			echo '<br>';
			echo $contractId.' :: '.$value["facturador"];
			echo "<br>";

			$subtotal = 0;
			$idInstServ = array();
			$_SESSION["conceptos"] = array();
			foreach($servicios as $res){

				$subtotal += $res["costoServicio"];

				$fecha = explode("-", $res["date"]);
				$fechaText = $months[$fecha[1]]." del ".$fecha["0"];
				$concepto = $res["nombreServicio"]." MES 13";

				$_SESSION["conceptos"][] = array(
						"noIdentificacion" => "",
						"cantidad" => 1,
						"unidad" => "No Aplica",
						"valorUnitario" => $res["costoServicio"],
						"importe" => $res["costoServicio"],
						"excentoIva" => "no",
						"descripcion" => $concepto,
						"tasaIva" => $tasaIva
				);

				echo  $res["nombreServicio"]." ".$res["instanciaServicioId"]." ".$res["name"]." ".$res["rfc"]." ".$res["costoServicio"];
				echo "<br>";

				$idInstServ[] = $res['instanciaServicioId'];

			}//foreach



			$iva = $subtotal * ($emisor["iva"] / 100);
			$total = $subtotal + $iva;
			$tasaIva = $emisor["iva"];

			$data["idFactura"] = $res["instanciaServicioId"]; //Duda


			$data["formaDePago"] = "PAGO EN UNA SOLA EXHIBICION";
			$data["condicionesDePago"] = "";
			$data["tasaIva"] = $tasaIva;
			$data["tiposDeMoneda"] = "MXN";
			$data["porcentajeRetIva"] = 0;
			$data["porcentajeDescuento"] = 0;
			$data["tipoDeCambio"] = 0;
			$data["porcentajeRetIsr"] = 0;
			$data["tiposComprobanteId"] = 1;
			$data["porcentajeIEPS"] = 0 ;

			//get serie
			$this->Util()->DB()->setQuery("SELECT * FROM serie WHERE empresaId = '".$empresaIdFacturador."'
				ORDER BY serieId ASC LIMIT 1");
			$serie = $this->Util()->DB()->GetRow();
			//agregar serie
			$data["serie"] = array
			(
					"serieId" => $serie["serieId"],
					"serie" => $serie["serie"],
					"empresaId" => $serie["empresaId"],
					"tiposComprobanteId" => $serie["tiposComprobanteId"],
					"lugarDeExpedicion" => $serie["lugarDeExpedicion"],
					"noCertificado" => $serie["noCertificado"],
					"email" => $serie["email"],
					"consecutivo" => $serie["consecutivo"],
					"rfcId" => $serie["rfcId"]
			);

			$data["comprobante"] = array
			(
					"tiposComprobanteId" => 1,
					"nombre" => $nombreFactura,
					"tipoDeComprobante" => "ingreso"
			);

			//nodo emisor
			$emisor["rfc"] = trim(str_replace("-", "", $emisor["rfc"]));
			$emisor["rfc"] = str_replace(" ", "", $emisor["rfc"]);

			$data["nodoEmisor"]["rfc"] = array
			(
					"rfcId" => $emisor["rfcId"],
					"empresaId" => $empresaIdFacturador,
					"regimenFiscal" => $emisor["regimenFiscal"],
					"rfc" => $emisor["rfc"],
					"razonSocial" => $emisor["razonSocial"],
					"pais" => $emisor["pais"],
					"calle" => $emisor["calle"],
					"noExt" => $emisor["noExt"],
					"noInt" => $emisor["noInt"],
					"colonia" => $emisor["colonia"],
					"localidad" => $emisor["localidad"],
					"municipio" => $emisor["municipio"],
					"ciudad" => $emisor["ciudad"],
					"referencia" => $emisor["referencia"],
					"estado" => $emisor["estado"],
					"cp" => $emisor["cp"],
					"activo" => $emisor["activo"],
					"main" => $emisor["main"]
			);

			if($value["facturador"] == "BHSC")
			{
				$data["nodoEmisor"]["sucursal"] = array
				(
						"identificador" => "Matriz",
						"rfcId" => $emisor["rfcId"],
						"empresaId" => $empresaIdFacturador,
						"regimenFiscal" => $emisor["regimenFiscal"],
						"rfc" => $emisor["rfc"],
						"razonSocial" => $emisor["razonSocial"],
						"pais" => $emisor["pais"],
						"calle" => "NAVARRA",
						"noExt" => "210",
						"noInt" => "PB",
						"colonia" => "Alamos",
						"localidad" => "BENITO JUAREZ",
						"municipio" => "BENITO JUAREZ",
						"ciudad" => "BENITO JUAREZ",
						"referencia" => "",
						"estado" => "DF",
						"cp" => "03400",
						"activo" => $emisor["activo"],
						"main" => $emisor["main"]
				);
			}
			else
			{
				$data["nodoEmisor"]["sucursal"] = array(
						"identificador" => "Matriz",
						"rfcId" => $emisor["rfcId"],
						"empresaId" => $empresaIdFacturador,
						"regimenFiscal" => $emisor["regimenFiscal"],
						"rfc" => $emisor["rfc"],
						"razonSocial" => $emisor["razonSocial"],
						"pais" => $emisor["pais"],
						"calle" => $emisor["calle"],
						"noExt" => $emisor["noExt"],
						"noInt" => $emisor["noInt"],
						"colonia" => $emisor["colonia"],
						"localidad" => $emisor["localidad"],
						"municipio" => $emisor["municipio"],
						"ciudad" => $emisor["ciudad"],
						"referencia" => $emisor["referencia"],
						"estado" => $emisor["estado"],
						"cp" => $emisor["cp"],
						"activo" => $emisor["activo"],
						"main" => $emisor["main"]
				);
			}

			//$data["nodoEmisor"]["sucursal"]["identificador"] = "Matriz";
			$res["rfc"] = trim(str_replace("-", "", $res["rfc"]));
			$res["rfc"] = str_replace(" ", "", $res["rfc"]);

			if($res["rfc"] == "123123123123")
			{
				continue;
			}
			if(!$res["rfc"])
			{
				continue;
				$res["rfc"] = "XAXX010101000";
			}

			if(strlen($res["rfc"]) < 12)
			{
				continue;
				$res["rfc"] = "XAXX010101000";
			}

			$data["nodoReceptor"] = array
			(
					"userId" => $res["contractId"],
					"empresaId" => $empresaIdFacturador,
					"rfcId" => $emisor["rfcId"],
					"rfc" => $res["rfc"],
					"nombre" => $res["name"],
					"calle" => $res["address"],
					"noExt" => $res["noExtAddress"],
					"noInt" => $res["noIntAddress"],
					"colonia" => $res["coloniaAddress"],
					"municipio" => $res["municipioAddress"],
					"cp" => $res["cpAddress"],
					"estado" => $res["estadoAddress"],
					"localidad" => $res["municipioAddress"],
					"referencia" => "",
					"pais" => $res["paisAddress"],
					"email" => $res["emailContactoAdministrativo"],
					"telefono" => $res["telefonoContactoAdministrativo"],
					"password" => ""
			);

			$metodoDePago = $res["metodoDePago"];
			$data["metodoDePago"] = $metodoDePago;
			$data["NumCtaPago"] = $res["noCuenta"];

			//print_r($_SESSION["conceptos"]);

			echo "\n\nFactura para ".$res["rfc"]." Lista\n";

			if(!$result = $this->GenerarComprobanteAutomatico($data, false, false, $empresaIdFacturador))
			{
				echo "\nError al generar la factura para ".$res["rfc"]."\n\n";
			}
			else
			{
				$last = $this->GetLastComprobante();
				//$comprobante = $result;
				$this->Util()->DB()->setQuery("
				INSERT INTO  `facturaEspecial` (
					`contractId` ,
					`year`
					)
					VALUES (
					'".$res["contractId"]."',  '".$year."'
					)");
				$this->Util()->DB()->InsertData();
				echo "\n\nFactura para ".$value["rfc"]." Lista\n";
			}
			//break;
			//exit;
		}//foreach

		//FIN AGRUPADO POR CONTRATOS
		
		$end = microtime();
		$tiempo = $end-$init;
		echo "<br>Script ejecutado en ".$tiempo." Milisegundos";

	}
} 


?>