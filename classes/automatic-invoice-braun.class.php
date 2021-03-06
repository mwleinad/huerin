<?php

class AutomaticInvoiceBraun extends Comprobante
{
	function CreateServiceInvoices()
	{
		global $months;
		$init = microtime();
		$this->Util()->DB()->setQuery("SELECT * FROM rfc
		WHERE rfcId = '29'");
		$emisor = $this->Util()->DB()->GetRow();
		$this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		WHERE instanciaServicio.comprobanteId = '0' AND contract.facturador = 'Braun'");
		//echo $this->Util()->DB()->query;
		$result = $this->Util()->DB()->GetResult();
//echo count($result);
		$countFact = 0;
		$countFactAll = 0;
		foreach($result as $key => $value)
		{		
			$subtotal = $value["costoServicio"];
			if($subtotal < 1)
			{
				continue;
			}

			
			$iva = $value["costoServicio"] * ($emisor["iva"] / 100);
			$total = $value["costoServicio"] + $iva;
			$tasaIva = $emisor["iva"];
			$fecha = explode("-", $value["date"]);
			if($fecha[0] < 2013)
			{
				continue;
			}
			
			if($fecha[0] == 2013 && $fecha[1] < 5)
			{
				continue;
			}
			
		$this->Util()->DB()->setQuery("SELECT * FROM customer WHERE customerId = '".$value["customerId"]."'");
		//echo $this->Util()->DB()->query;
		$customer = $this->Util()->DB()->GetRow();
		
		if(!$customer || $customer["active"] == '0')
		{
			continue;
		}
		
		if($value["facturador"] != "Braun")
		{
			continue;
		}
			$fechaText = $months[$fecha[1]]." del ".$fecha["0"];

			//agregar conceptos
			$_SESSION["conceptos"][1] = array(
				"noIdentificacion" => "",
				"cantidad" => 1,
				"unidad" => "No Aplica",
				"valorUnitario" => $value["costoServicio"],
				"importe" => $value["costoServicio"],
				"excentoIva" => "no",
				"descripcion" => $value["nombreServicio"]." Correspondiente al mes de ".$fechaText,
				"tasaIva" => $tasaIva					
															);
		
			$data["idFactura"] = $value["instanciaServicioId"];
		
			$metodoDePago = "No Aplica";

			$data["formaDePago"] = "PAGO EN UNA SOLA EXHIBICION";
			$data["condicionesDePago"] = "";
			$data["metodoDePago"] = $metodoDePago;
			$data["NumCtaPago"] = $cuenta;
			$data["tasaIva"] = $tasaIva;
			$data["tiposDeMoneda"] = "MXN";
			$data["porcentajeRetIva"] = 0;
			$data["porcentajeDescuento"] = 0;
			$data["tipoDeCambio"] = 0;
			$data["porcentajeRetIsr"] = 0;
			$data["tiposComprobanteId"] = 1;
			$data["porcentajeIEPS"] = 0 ;
			
			//get serie
			$this->Util()->DB()->setQuery("SELECT * FROM serie WHERE empresaId = 20
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
				"nombre" => "Factura",
				"tipoDeComprobante" => "ingreso"
			);
			
			//nodo emisor
			$emisor["rfc"] = trim(str_replace("-", "", $emisor["rfc"]));
			$emisor["rfc"] = str_replace(" ", "", $emisor["rfc"]);
		
			$data["nodoEmisor"]["rfc"] = array
			(
				"rfcId" => 1,
				"empresaId" => $emisor["empresaId"],
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
			
			$data["nodoEmisor"]["sucursal"]["identificador"] = "Matriz";	
				
			$value["rfc"] = trim(str_replace("-", "", $value["rfc"]));
			$value["rfc"] = str_replace(" ", "", $value["rfc"]);

			if($value["rfc"] == "123123123123")
			{
				continue;
			}			
			if(!$value["rfc"])
			{
				continue;
				$value["rfc"] = "XAXX010101000";
			}
			
			if(strlen($value["rfc"]) < 12)
			{
				continue;
				$value["rfc"] = "XAXX010101000";
			}
			
			//print_r($cliente);
			$data["nodoReceptor"] = array
			(
				"userId" => $value["contractId"],
				"empresaId" => 20,
				"rfcId" => 1,
				"rfc" => $value["rfc"],
				"nombre" => $value["name"],
				"calle" => $value["address"],
				"noExt" => $value["noExtAddress"],
				"noInt" => $value["noIntAddress"],
				"colonia" => $value["coloniaAddress"],
				"municipio" => $value["municipioAddress"],
				"cp" => $value["cpAddress"],
				"estado" => $value["estadoAddress"],
				"localidad" => $value["municipioAddress"],
				"referencia" => "",
				"pais" => "Mexico",
				"email" => $value["emailContactoAdministrativo"],
				"telefono" => $value["telefonoContactoAdministrativo"],
				"password" => ""
			);
			
			//print_r($data["nodoReceptor"]);
			if(!$result = $this->GenerarComprobanteAutomatico($data, false, false, 20))
			{
				echo "\nError al generar la factura para ".$value["rfc"]."\n\n";
//echo "jere2";
				
			}
			else
			{
				$last = $this->GetLastComprobante();
				//$comprobante = $result;
				$this->Util()->DB()->setQuery("UPDATE instanciaServicio SET comprobanteId = '".$last["comprobanteId"]."' WHERE instanciaServicioId = '".$value["instanciaServicioId"]."'");
				$this->Util()->DB()->UpdateData();
			echo "\n\nFactura para ".$value["rfc"]." Lista\n";
				
				$countFact++;
				if($countFact == 2)
				{
				//	exit();
				}
				
			}
			exit();

		}//foreach
		$end = microtime();
		$tiempo = $end-$init;
		echo "<br>Script ejecutado en ".$tiempo." Milisegundos";

	}
} 


?>