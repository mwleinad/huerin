<?php
//	$strAddenda = "<cfdi:Addenda>";
//	$strAddenda .= "</cfdi:Addenda>";
				
//	$fh = fopen($realSignedXml, 'r');
//	$theData = fread($fh, filesize($realSignedXml));
//	fclose($fh);
//	$theData = str_replace("</cfdi:Complemento>", "</cfdi:Complemento>".$strAddenda, $theData);
		$xml = new DOMdocument();
		$root = $xml->createElement("cfdi:Addenda");
		$root = $xml->appendChild($root);
		
		$request = $xml->createElement("RequestCFD");
		$request = $root->appendChild($request);
		
		$xmlGen = new XmlGen;
		$xmlGen->CargaAtt($request, 
			array("tipo"=>"AddendaPCO",
					 "version"=>"2.0",
					 "idPedido"=>$this->Util()->CadenaOriginalVariableFormat($data["idPedido"],false,false),
					 "idSolicitudPago"=>$this->Util()->CadenaOriginalVariableFormat($data["idSolicitudPago"],false,false),
				 )
		);
		
		$documento = $xml->createElement("Documento");
		$documento = $request->appendChild($documento);

		$xmlGen->CargaAtt($documento, 
			array("folioUUID"=>$timbreXml["UUID"],
					 "tipoDoc"=>$data["tiposComprobanteId"],
				 )
		);

		$proveedor = $xml->createElement("Proveedor");
		$proveedor = $request->appendChild($proveedor);

		$xmlGen->CargaAtt($proveedor, 
			array("idProveedor"=>$data["idProveedor"],
				 )
		);

		$recepciones = $xml->createElement("Recepciones");
		$recepciones = $request->appendChild($recepciones);
		//order by idRecepcion
		$misRecepciones = array();
		foreach($_SESSION["conceptos"] as $miConcepto)
		{
			$misRecepciones[$miConcepto["idRecepcion"]][] = $miConcepto;
		}
		
		foreach($misRecepciones as $key => $laRecepcion)
		{
			$recepcion = $xml->createElement("Recepcion");
			$recepcion = $recepciones->appendChild($recepcion);

			$xmlGen->CargaAtt($recepcion, 
				array("idRecepcion"=>$key,
					 )
			);
			
			foreach($laRecepcion as $miConcepto)
			{

				$concepto = $xml->createElement("Concepto");
				$concepto = $recepcion->appendChild($concepto);

				$xmlGen->CargaAtt($concepto, 
					array("importe"=>$miConcepto["importe"],
					"valorUnitario"=>$miConcepto["valorUnitario"],
					"cantidad"=>$miConcepto["cantidad"],
					"descripcion"=>$miConcepto["descripcion"],
					"unidad"=>$miConcepto["unidad"],
					)
				);
			}//for each concpeto
		}//for each recepcion
			
	//	}
		$strAddenda = $xml->saveXML();
		$strAddenda = urlencode($strAddenda);
		$strAddenda = str_replace("%3C%3Fxml+version%3D%221.0%22%3F%3E","",$strAddenda);
		$strAddenda = urldecode($strAddenda);
		//$srtAddenda = substr($srtAddenda, 21);

?>
