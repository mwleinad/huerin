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
		
		$xmlGen = new XmlGen;
		
		$misRecepciones = array();
		foreach($_SESSION["conceptos"] as $miConcepto)
		{
			$item = $xml->createElement("cfdi:ItemsFacturados");
			$item = $root->appendChild($item);
			
			$xmlGen->CargaAtt($item, 
				array(
					"FactRef"=>urldecode($data["referencia"]),
					"Item"=>$miConcepto["item"],
					"OrdenCompra"=>$miConcepto["ordenCompra"]
				)
			);			
		}
		
	//	}
		$strAddenda = $xml->saveXML();
		$strAddenda = urlencode($strAddenda);
		$strAddenda = str_replace("%3C%3Fxml+version%3D%221.0%22%3F%3E","",$strAddenda);
		$strAddenda = urldecode($strAddenda);
		//$srtAddenda = substr($srtAddenda, 21);

?>
