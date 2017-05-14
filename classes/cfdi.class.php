<?php

class Cfdi extends Producto
{
	function Search($values){

			global $user;
			
			if($values["rfc"])
			{
				$sqlSearch .= " AND rfc LIKE '%".$values["rfc"]."%'";
			}

			if($values["razonSocial"])
			{
				$sqlSearch .= " AND razonSocial LIKE '%".$values["razonSocial"]."%'";
			}

			 $sql = "SELECT * FROM empresa WHERE rfc != ''".$sqlSearch;
			$id_empresa = $_SESSION['empresaId'];
			$this->Util()->DBRemote()->setQuery($sql);
			$comprobantes = $this->Util()->DBRemote()->GetResult();

			$data["items"] = $comprobantes;
			$data["pages"] = $pages;
			$data["total"] = count($comprobantes);
			
			return $data;
	}//SearchComprobantesByRfc

	function SearchPagos($values){

			global $user;
			
			if($values["rfc"])
			{
				$sqlSearch .= " AND rfc LIKE '%".$values["rfc"]."%'";
			}

			if($values["razonSocial"])
			{
				$sqlSearch .= " AND razonSocial LIKE '%".$values["razonSocial"]."%'";
			}

			echo $sql = "SELECT * FROM ventas WHERE rfc != ''".$sqlSearch." ORDER BY idVenta DESC LIMIT 50";
			$id_empresa = $_SESSION['empresaId'];
			$this->Util()->DBRemote("pascacio_general")->setQuery($sql);
			$comprobantes = $this->Util()->DBRemote("pascacio_general")->GetResult();

		foreach($comprobantes as $key => $comprobante)
		{
			$sql = "SELECT estado FROM rfc LIMIT 1";
			$id_empresa = $comprobante['idEmpresa'];
			$this->Util()->DBRemote("pascacio_".$id_empresa)->setQuery($sql);
			$comprobantes[$key]["estado"] = $this->Util()->DBRemote("pascacio_".$id_empresa)->GetSingle();

		}


			$data["items"] = $comprobantes;
			$data["pages"] = $pages;
			$data["total"] = count($comprobantes);
			
			return $data;
	}//SearchComprobantesByRfc
	
	function AutorizarPago($id){

			global $user;
			
      $sql = "SELECT * FROM ventas WHERE idVenta = '".$id."'";
			$id_empresa = $_SESSION['empresaId'];
			$this->Util()->DBRemote()->setQuery($sql);
			$venta = $this->Util()->DBRemote()->GetRow();
			
			if($venta["status"] == "pagado")
			{
				return;
			}
			$date = date("Y-m-d");

      $sql = "UPDATE ventas SET status = 'Pagado', fechaPagado = '".$date."' WHERE idVenta = '".$id."'";
			$id_empresa = $_SESSION['empresaId'];
			$this->Util()->DBRemote()->setQuery($sql);
			$this->Util()->DBRemote()->UpdateData();
			
			//activamos empresa y updateamos fecha activado y fecha de 1er vencimiento
			$datePost = date("Y-m-d", strtotime($date. " + 1 YEAR"));

      $sql = "UPDATE empresa SET vencimiento = '".$datePost."', limite = limite + '".$venta["cantidad"]."' WHERE empresaId = '".$venta["idEmpresa"]."'";
			$id_empresa = $_SESSION['empresaId'];
			$this->Util()->DBRemote()->setQuery($sql);
			$this->Util()->DBRemote()->UpdateData();
			
/*				$empresaIdFacturador = 15;
				$_SESSION['empresaId'] = 15;
				$emisor = $emisorHuerin;
				$nombreFactura = "Factura";
			
				$subtotal = 0;
				$_SESSION["conceptos"] = array();
				
				$concepto = $venta["cantidad"]." TIMBRES FISCALES";
				
				$valorUnitario = $venta["monto"] / 1.16;
				$valorUnitario += $res["costoServicio"];
				
				$_SESSION["conceptos"][] = array(
					"noIdentificacion" => "",
					"cantidad" => 1,
					"unidad" => "No Aplica",
					"valorUnitario" => $valorUnitario,
					"importe" => $valorUnitario,
					"excentoIva" => "no",
					"descripcion" => $concepto,
					"tasaIva" => 16
				);
				
				$iva = $subtotal * (16 / 100);
				$total = $subtotal + $iva;
				$tasaIva = 16;			
						
				$data["idFactura"] = $res["instanciaServicioId"]; //Duda
		
				$metodoDePago = "No Aplica";
	
				$data["formaDePago"] = "PAGO EN UNA SOLA EXHIBICION";
				$data["condicionesDePago"] = "";
				$data["metodoDePago"] = $venta["metodoPago"];
				$data["NumCtaPago"] = "";
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
				
				$this->Util()->DB()->setQuery("SELECT * FROM rfc
					WHERE empresaId = '15' ORDER BY rfcId ASC LIMIT 1");
				$emisor = $this->Util()->DB()->GetRow();
				
			
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
				
				
			
				$data["nodoEmisor"]["sucursal"]["identificador"] = "Matriz";	

				$this->Util()->DB()->setQuery("SELECT * FROM contract
					WHERE rfc = '".$venta["rfc"]."' LIMIT 1");
				$res = $this->Util()->DB()->GetRow();
					
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
//			print_r($data);
			$comprobante = new Comprobante;
			$comprobante->GenerarComprobanteAutomatico($data, false, false, $empresaIdFacturador);
*/						
			//return $data;
	}//SearchComprobantesByRfc	


}
?>