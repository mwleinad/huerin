<?php
		$xml = new DOMdocument();
$root = $xml->createElement("cfdi:Addenda");
$root = $xml->appendChild($root);

$request = $xml->createElement("AddendaContinentalTire");
$request = $root->appendChild($request);

$xmlGen = new XmlGen;

$documento = $xml->createElement("PO", 'CON');
$documento = $request->appendChild($documento);

$pedido = $xml->createElement("Pedido", $data['idPedido']);
$pedido = $request->appendChild($pedido);

$tipoProv = $xml->createElement("Tipo_Prov", $data['idSolicitudDePago']);
$tipoProv = $request->appendChild($tipoProv);

$posicionesPo = $xml->createElement("Posiciones_PO");
$posicionesPo = $request->appendChild($posicionesPo);

foreach($_SESSION["conceptos"] as $miConcepto)
{
	$posicion = $xml->createElement("Posicion");
	$posicion = $posicionesPo->appendChild($posicion);

	$atributos = array(
			"Embarque"=>$miConcepto["idRecepcion"],
			"Descripcion"=> $miConcepto["descripcion"],
			"Num_PosicionPO"=>$miConcepto["item"]
	);

	if($data["porcentajeRetIva"] > 0)
	{
		$atributos["Tasa_Retencion_IVA"] = number_format($data["porcentajeRetIva"], 2,".",",");
	}

	if($data["Tasa_Retencion_ISR"] > 0)
	{
		$atributos["Tasa_Retencion_ISR"] = number_format($data["porcentajeRetIsr"], 2,".",",");
	}

	$xmlGen->CargaAtt($posicion,
			$atributos
	);
	$ii++;
}

$codigo = $xml->createElement("Codigo_Compania", 154);
$codigo = $request->appendChild($codigo);

$strAddenda = $xml->saveXML();
$strAddenda = urlencode($strAddenda);
$strAddenda = str_replace("%3C%3Fxml+version%3D%221.0%22%3F%3E","",$strAddenda);
$strAddenda = urldecode($strAddenda);
		//$srtAddenda = substr($srtAddenda, 21);

?>
