<?php

class XmlTransform extends Comprobante
{
	function Execute($ruta, $archivo, $saveTo, $empresaId)
	{

		$pathXml = $ruta.$archivo;
		//Generamos el PDF.
		$xml = simplexml_load_file($pathXml);
		
		$ns = $xml->getNamespaces(true);
		$xml->registerXPathNamespace('c',$ns['cfdi']);
		$xml->registerXPathNamespace('t',$ns['tfd']);

		//Emisor
        $card = array();
        foreach($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $emisor){
            $card['rfc'] = $emisor['rfc'];
            $card['razonSocial'] = $emisor['nombre'];
        }//foreach
        //Emisor > Domicilio Fiscal
        foreach($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal') as $domFiscal){
            $card['calle'] = $domFiscal['calle'];
            $card['noExt'] = $domFiscal['noExterior'];
            $card['noInt'] = $domFiscal['noInterior'];
            $card['colonia'] = $domFiscal['colonia'];
            $card['municipio'] = $domFiscal['municipio'];
            $card['estado'] = $domFiscal['estado'];
            $card['pais'] = $domFiscal['pais'];
            $card['cp'] = $domFiscal['codigoPostal'];
        }//foreach

        //Emisor > Regimen Fiscal
        foreach($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal') as $regimen){
            $card['regimenFiscal'] = $regimen['Regimen'];
        }//foreach

        $infoRfc = $this->InfoRfcByRfc2($card['rfc']);
        $card["rfcId"] = $infoRfc['rfcId'];
        $data['nodoEmisor']['rfc'] = $card;

		//Comprobante
		
		foreach($xml->xpath('//cfdi:Comprobante') as $comp){
			//get serie
			$this->Util()->DB()->setQuery("SELECT * FROM serie WHERE serie = '".$comp["serie"]."' and rfcId ='".$infoRfc['rfcId']."' ");
			$row = $this->Util()->DB()->GetRow();

			$serie['serie'] = $row['serie'];
			$serie['serieId'] = $row['serieId'];
			$serie['noCertificado'] = $comp['noCertificado'];

			
			$data['folio'] = $comp['folio'];
            $data['version'] = $comp['version'];
			$fecha = explode('T',$comp['fecha']);
			$data['fecha'] = $fecha[0].' '.$fecha[1];
			$data['tipoDeComprobante'] = $comp['tipoDeComprobante'];
			
			$data['formaDePago'] = $comp['formaDePago'];
			$data['metodoDePago'] = $comp['metodoDePago'];		
			$data['condicionesDePago'] = $comp['condicionesDePago'];		
			$data['NumCtaPago'] = $comp['NumCtaPago'];		
			$data['LugarExpedicion'] = $comp['LugarExpedicion'];
			$data['tiposDeMoneda'] = $comp['Moneda'];
			$data['tipoDeCambio'] = $comp['TipoCambio'];
			
			$totales['moneda'] = $comp['Moneda'];
			$totales['subtotal'] = $comp['subTotal'];
			$totales['descuento'] = $comp['descuento'];
			$totales['total'] = $comp['total'];		
					
		}//foreach


		//Obtenemos la informacion del Comprobante
		if($empresaId == 185)
		{
			$anio = explode("-", $data["fecha"]);
			$anio = $anio[0];
			$sql = "SELECT * FROM comprobante 
				WHERE serie = '".$serie['serie']."' AND folio = '".$data['folio']."' AND YEAR(fecha) = '".$anio."' ";
		}
		else
		{
			$sql = "SELECT * FROM comprobante 
				WHERE serie = '".$serie['serie']."' AND folio = '".$data['folio']."' and rfcId = '".$infoRfc['rfcId']."' ";
		}
		
		$this->Util()->DBSelect($empresaId)->setQuery($sql);
		$fact = $this->Util()->DBSelect($empresaId)->GetRow();
        $cancelado = 0;
		$this->Util()->DBSelect($empresaId)->setQuery("SELECT * FROM pending_cfdi_cancel WHERE cfdi_id ='".$fact['comprobanteId']."' ");
		$pending_cancel= $this->Util()->DB()->GetResult();
		dd($pending_cancel);
		if(!is_array($pending_cancel))
		    $pending_cancel = [];
            echo "antes".$cancelado;
		$cancelado = ($fact['status'] == 0 || count($pending_cancel)>0) ? 1 : 0;
        echo "despues".$cancelado;

		$data['sucursalId'] = $fact['sucursalId'];
		$data['tiposComprobanteId'] = $fact['tiposComprobanteId'];
		$data['observaciones'] = $fact['observaciones'];
		$comprobante = new Comprobante;
		$data["comprobante"] = $comprobante->InfoComprobante($data["tiposComprobanteId"]);

		$empresa = new Empresa;
		$empresa->setEmpresaId($empresaId);
		$emp = $empresa->Info();
				
		//Retenciones
		
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion') as $ret){ 	   
				if($ret['impuesto'] == 'IVA')
				$totales['retIva'] = floatval($ret['importe']);
			elseif($ret['impuesto'] == 'ISR')
				$totales['retIsr'] = floatval($ret['importe']); 
		} 
		
		//Traslados
		
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $tras){ 	   
				if($tras['impuesto'] == 'IVA'){
				$totales['iva'] = floatval($tras['importe']);
				$totales['tasaIva'] = $tras['tasa'];
			}elseif($tras['impuesto'] == 'IEPS'){
				$totales['ieps'] = floatval($tras['importe']);
				$totales['porcentajeIEPS'] = $tras['tasa'];
			}	   
		}

		//Emisor > Expedido En
		$card = array();
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:ExpedidoEn') as $exp){
			$card['identificador'] = $data['LugarExpedicion'];
			$card['calle'] = $exp['calle'];
			$card['noExt'] = $exp['noExterior'];
			$card['noInt'] = $exp['noInterior'];
			$card['colonia'] = $exp['colonia'];
			$card['municipio'] = $exp['municipio'];
			$card['estado'] = $exp['estado'];
			$card['pais'] = $exp['pais'];
			$card['cp'] = $exp['codigoPostal'];
		}//foreach

		$data['nodoEmisor']['sucursal'] = $card;
		$data['nodoEmisor']['sucursal']['nombre'] = $card['identificador'];
		$data['nodoEmisor']['sucursal']['sucursalActiva'] = 'no';
		
		//Receptor
		$card = array();
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $receptor){		
			$card['rfc'] = $receptor['rfc'];
			$card['nombre'] = $receptor['nombre'];						
		}//foreach
		
		//Receptor > Domicilio
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Receptor//cfdi:Domicilio') as $dom){		
			$card['calle'] = $dom['calle'];
			$card['noExt'] = $dom['noExterior'];
			$card['noInt'] = $dom['noInterior'];
			$card['colonia'] = $dom['colonia'];
			$card['municipio'] = $dom['municipio'];
			$card['estado'] = $dom['estado'];
			$card['pais'] = $dom['pais'];
			$card['cp'] = $dom['codigoPostal'];				
		}//foreach
			
		$data['nodoReceptor'] = $card;
		$nodoReceptor = $card;
		
		//Conceptos 
		$conceptos = array();
		foreach($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $con){ 	   
			 $conceptos[] = $con;	   
		}
		
		//TimbreFiscalDigital
		foreach($xml->xpath('//t:TimbreFiscalDigital') as $tfd){		
			$data['UUID'] = $tfd['UUID'];
			$data['FechaTimbrado'] = $tfd['FechaTimbrado'];
			$data['sello'] = $tfd['selloCFD'];
			$data['selloSAT'] = $tfd['selloSAT'];		
		}//foreach

		$infEmp['empresaId'] = $empresaId;
		
		//Cadena Original
		switch($data['version'])
		{
			case 'auto':
            case '3.2':
            case '3.3':
			case 'v3':
			case 'construc':
				include_once(DOC_ROOT.'/classes/cadena_original_v3.class.php');break;
			case '2': 	
				include_once(DOC_ROOT.'/classes/cadena_original_v2.class.php');break;
		}
		
		$cadena = new Cadena;
		$cadenaOriginal = $cadena->BuildCadenaOriginal($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $conceptos);
		$data['cadenaOriginal'] = $cadenaOriginal;
		
		//Timbre
		
		$nufa = $infEmp["empresaId"]."_".$serie["serie"]."_".$data["folio"];

		$rfcActivo =$infoRfc['rfcId'];
		$root = DOC_ROOT."/empresas/".$empresaId."/certificados/".$rfcActivo."/facturas/xml/";
		$root_dos = DOC_ROOT."/empresas/".$empresaId."/certificados/".$rfcActivo."/facturas/xml/timbres/";
	
		$nufa_dos = "SIGN_".$empresaId."_".$serie["serie"]."_".$data["folio"];
		$timbradoFile = $root.$nufa_dos.".xml";	
		
		$timbreFiscal = unserialize(urldecode($fact["timbreFiscal"]));
		$cadenaOriginalTimbre = $timbreFiscal;
		$data['timbreFiscal'] = $cadenaOriginalTimbre;

		include_once(DOC_ROOT."/designs/override_default_generator_pdf.class.php");
		$override = new OverrideGenerator;
		$override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $conceptos, $infEmp, $cancelado);

	}//VistaPreviaComprobante
	
}



?>