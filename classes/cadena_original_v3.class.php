<?php

class Cadena extends Comprobante
{
	public function BuildCadenaOriginal($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $nodosConceptos)
	{
		//informacion nodo comprobante
		$cadenaOriginal = "||3.2|";
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($serie["serie"]);
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["folio"]);
		$data["fecha"] = explode(" ", $data["fecha"]);
		$data["fecha"] = $data["fecha"][0]."T".$data["fecha"][1];
		//$data["fecha"] = "2010-09-22T07:45:09";
		
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["fecha"]);
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($serie["noAprobacion"]);
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($serie["anoAprobacion"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["tipoDeComprobante"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["formaDePago"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["condicionesDePago"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["subtotal"], true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["descuento"], true);
		//tipo de cambio
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["tipoDeCambio"], true, true, 4);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["tiposDeMoneda"], false);
		
		//tipo de cambio
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["total"], true);

		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["metodoDePago"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["identificador"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["NumCtaPago"]);

		//informacion nodo emisor
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["rfc"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["razonSocial"]);

		//informacion nodo domiciliofiscal
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["calle"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["noExt"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["noInt"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["colonia"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["localidad"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["referencia"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["municipio"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["estado"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["pais"]);
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["cp"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($this->Util()->PadStringLeft($data["nodoEmisor"]["rfc"]["cp"], 5, "0"));
		
		if($data["nodoEmisor"]["sucursal"]["sucursalActiva"] == 'no'){
		
			//informacion nodo expedidoen
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["calle"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["noExt"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["noInt"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["colonia"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["localidad"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["referencia"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["municipio"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["estado"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["pais"]);
	//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["sucursal"]["cp"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($this->Util()->PadStringLeft($data["nodoEmisor"]["sucursal"]["cp"], 5, "0"));
		
		}

		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["nodoEmisor"]["rfc"]["regimenFiscal"]);

		//informacion nodo receptor
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["rfc"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["nombre"]);

		//informacion nodo domicilio
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["calle"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["noExt"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["noInt"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["colonia"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["localidad"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["referencia"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["municipio"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["estado"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["pais"]);
//		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($nodoReceptor["cp"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($this->Util()->PadStringLeft($nodoReceptor["cp"], 5, "0"));

		//informacion nodos conceptos
		foreach($nodosConceptos as $concepto)
		{
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["cantidad"],true);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["unidad"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["noIdentificacion"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["descripcion"]);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["valorUnitario"],true);
			$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($concepto["importe"],true);
			//aca falta la informacion aduanera
			//aca falta cuenta predial
		}
		
		//todo complementoconcepto

		//nodoretenciones
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat("IVA");
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["retIva"],true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat("ISR");
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["retIsr"],true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["retIsr"]+$totales["retIva"],true);

		//nodotraslados
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat("IVA");
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["tasaIva"], true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["iva"], true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat("IEPS");
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["porcentajeIEPS"], true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["ieps"], true);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($totales["iva"] + $totales["ieps"], true);

		//falta nodo complemento
		$cadenaOriginal .= "|";

		$cadenaOriginal = utf8_encode($cadenaOriginal);
		return $cadenaOriginal;
	}
	
}



?>