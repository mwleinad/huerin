<?php

class Comprobante extends Producto
{
	function GenerarComprobanteAutomatico($data, $notaCredito = false, $fromMama = false, $comprobanteEmpresaId = 15)
	{
//		$myData = urlencode(serialize($data));
		
		//$empresa = $this->Info();
		$values = explode("&", $data["datosFacturacion"]);
		unset($data["datosFacturacion"]);
		foreach($values as $key => $val)
		{
			$array = explode("=", $values[$key]);
			$data[$array[0]] = $array[1];
		}
		//$vs = new User;
		if($data["NumCtaPago"] == 0)
		{
			$data["NumCtaPago"] = "";
		}
		
		//$vs->setRFC($data["rfc"]);
		//$vs->setCalle($data["calle"]);
		//$vs->setPais($data["pais"]);

		if(strlen($data["formaDePago"]) <= 0)
		{
			$vs->Util()->setError(10041, "error", "");
		}
		
		if(count($_SESSION["conceptos"]) < 1)
		{
			$vs->Util()->setError(10040, "error", "");
		}
		
		$myConceptos = urlencode(serialize($_SESSION["conceptos"]));
		
		$userId = $data["userId"];
		
		
		$totales = $this->GetTotalDesglosado($data);
//		print_r($totales);
//		if($vs->Util()->PrintErrors()){ return false; }
		
		if(!$data["tipoDeCambio"])
		{
			$data["tipoDeCambio"] = "1.00";
		}

		if(!$data["porcentajeDescuento"])
		{
			$data["porcentajeDescuento"] = "0";
		}

		if(!$data["porcentajeIEPS"])
		{
			$data["porcentajeIEPS"] = "0";
		}

		if(!$data["metodoDePago"])
		{
			$this->Util()->setError(10047, "error", "El metodo de pago no puede ser vacio");
		}
		
		$serie = $data["serie"];

		if(!$serie)
		{
			$this->Util()->setError(10047, "error");
		}

		if($this->Util()->PrintErrors()){ return false; }
		
		$folio = $serie["consecutivo"];
		$fecha = $this->Util()->FormatDateAndTime(time());
		
		//el tipo de comprobante lo determina tiposComprobanteId
		$tipoDeComprobante = "ingreso";

		$data["serie"] = $serie;
		$data["folio"] = $folio;
		$data["fecha"] = $fecha;
		$data["tipoDeComprobante"] = $tipoDeComprobante;
		$data["certificado"] = $serie["noCertificado"];

		$nodoReceptor = $data["nodoReceptor"];
		$nodoEmisor = $data["nodoEmisor"];

		//check tipo de cambio

		switch($_SESSION["version"])
		{
			case "auto":
			case "v3":
			case "construc":
				include_once(DOC_ROOT.'/classes/cadena_original_v3.class.php');break;
			case "2": 	
				include_once(DOC_ROOT.'/classes/cadena_original_v2.class.php');break;
		}
		$cadena = new Cadena;
		
		$cadenaOriginal = $cadena->BuildCadenaOriginal($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"]);
//		echo "ok|";
		$data["cadenaOriginal"] = utf8_encode($cadenaOriginal);
		$data["cadenaOriginal"] = $cadenaOriginal;
		
		$md5Cadena = utf8_decode($cadenaOriginal);
		$md5 = sha1($md5Cadena);

//		echo $md5;
//		print_r($totales);
		$sello = $this->GenerarSello($cadenaOriginal, $md5, $data["nodoReceptor"]["empresaId"]);
		$data["sello"] = $sello["sello"];
		$data["certificado"] = $sello["certificado"];

		$_SESSION["empresaId"] = $comprobanteEmpresaId;
		$empresa["empresaId"] = $comprobanteEmpresaId;
		$userId = 1;

		switch($_SESSION["version"])
		{
			case "auto":
			case "v3":
			case "construc":
				include_once(DOC_ROOT.'/classes/generate_xml_default.class.php');break;
			case "2":	
				include_once(DOC_ROOT.'/classes/generate_xml_v2.class.php');break;
		}
		$xmlGen = new XmlGen;
		$xml = $xmlGen->GenerateXML($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);
		
		//despues de la generacion del xml, viene el timbrado.
		$nufa = $data["nodoReceptor"]["empresaId"]."_".$serie["serie"]."_".$data["folio"];
		$rfcActivo = $this->getRfcActive();
		$root = DOC_ROOT."/empresas/".$data["nodoReceptor"]["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/";

			$nufa_dos = "SIGN_".$data["nodoReceptor"]["empresaId"]."_".$serie["serie"]."_".$data["folio"];
			$xmlFile = $root.$nufa.".xml";
			$zipFile = $root.$nufa.".zip";
			@unlink($zipFile);
			$signedFile = $root.$nufa."_signed.zip";
			$timbreFile = $root.$nufa."_timbre.zip";
//			$timbradoFile = $root."timbreCFDi.xml";
//			$timbradoFile = $root."/timbres/".$nufa.".xml";
			$timbradoFile = $root.$nufa_dos.".xml";

		$this->Util()->Zip($root, $nufa);
		
		$user = "STI070725SAA";
		$pw = "oobrotcfl";
		$pac = new Pac;
		$response = $pac->GetCfdi($user, $pw, $zipFile, $root, $signedFile, 15);
		
//		print_r($response);
		if($response["fault"])
		{
/*			echo "<b><br><br>Has encontrado un error al Sellar tu Comprobante. Favor de reportarnos este error con todos los detalles posibles. Gracias!!</b>";
			echo " ";
			echo $data["nodoReceptor"]["nombre"];
			echo " ";
			echo $data["nodoReceptor"]["rfc"];
			echo "<br>";
*/			return false;
		}
		

		//$fileTimbreXml = $pac->GetTimbreCfdi($user, $pw, $zipFile, $root, $timbreFile);
		$timbreXml = $pac->ParseTimbre($timbradoFile);
		
		$cadenaOriginalTimbre = $pac->GenerateCadenaOriginalTimbre($timbreXml);
		$cadenaOriginalTimbreSerialized = serialize($cadenaOriginalTimbre);
		$data["timbreFiscal"] = $cadenaOriginalTimbre;

		switch($empresa["empresaId"])
		{
			case 85: 
				include_once(DOC_ROOT."/classes/override_generate_pdf_85.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);break;
			case 86: 
				include_once(DOC_ROOT."/classes/override_generate_pdf_86.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);break;
			//moco
			case 91: 
			case 117: 
			case 126: 
			case 127: 
			case 139: 
			case 162: 
				include_once(DOC_ROOT."/classes/override_generate_pdf_126.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);break;
			case 91: 
			case 127: 
				include_once(DOC_ROOT."/classes/override_generate_pdf_91.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);break;
			case 115: 
				include_once(DOC_ROOT."/classes/override_generate_pdf_115.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);break;
			default: 
				switch($_SESSION["version"])
				{
					case "v3":
					case "construc": include_once(DOC_ROOT."/classes/override_generate_pdf_default.php"); break;
					case "auto": include_once(DOC_ROOT."/classes/override_generate_pdf_default_auto.php");break;
					case "2": include_once(DOC_ROOT."/classes/override_generate_pdf_default_2.php");break;
				}
				
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);
		}
		
		switch($data["tiposDeMoneda"])
		{
			case "MXN": $data["tiposDeMoneda"] = "peso"; break;
			case "USD": $data["tiposDeMoneda"] = "dolar"; break;
			case "EUR": $data["tiposDeMoneda"] = "euro"; break;
		}
		
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			INSERT INTO `comprobante` (
				`comprobanteId`, 
				`userId`, 
				`formaDePago`, 
				`condicionesDePago`, 
				`metodoDePago`, 
				`tasaIva`, 
				`tipoDeMoneda`, 
				`tipoDeCambio`, 
				`porcentajeRetIva`, 
				`porcentajeRetIsr`, 
				`tiposComprobanteId`, 
				`porcentajeIEPS`, 
				`porcentajeDescuento`, 
				`empresaId`, 
				`sucursalId`, 
				`observaciones`,
				`serie`,
				`folio`,
				`fecha`,
				`sello`,
				`noAprobacion`,
				`anoAprobacion`,
				`noCertificado`,
				`certificado`,
				`subtotal`,
				`descuento`,
				`motivoDescuento`,
				`total`,
				`tipoDeComprobante`,
				`xml`,
				`rfcId`,
				`ivaTotal`,
				`data`,
				`conceptos`,
				`impuestos`,
				`cadenaOriginal`,
				`timbreFiscal`
			) VALUES 
			(
			 	NULL, 
				'".$data["nodoReceptor"]["userId"]."', 
				'".$data["formaDePago"]."', 
				'".$data["condicionesDePago"]."', 
				'".$data["metodoDePago"]."', 
				'".$data["tasaIva"]."', 
				'".$data["tiposDeMoneda"]."', 
				'".$data["tipoDeCambio"]."', 
				'".$data["porcentajeRetIva"]."', 
				'".$data["porcentajeRetIsr"]."', 
				'".$data["tiposComprobanteId"]."', 
				'".$data["porcentajeIEPS"]."', 
				'".$data["porcentajeDescuento"]."', 
				'".$empresa["empresaId"]."', 
				'".$data["sucursalId"]."', 
				'".$data["observaciones"]."',
				'".$serie["serie"]."',
				'".$folio."',
				'".$fecha."',
				'".$data["sello"]."',
				'".$serie["noAprobacion"]."',
				'".$serie["anoAprobacion"]."',
				'".$serie["noCertificado"]."',
				'".$data["certificado"]."',
				'".$totales["subtotal"]."',
				'".$totales["descuento"]."',
				'".$data["motivoDescuento"]."',
				'".$totales["total"]."',
				'".$tipoDeComprobante."',
				'".$xml."',
				'".$data["nodoEmisor"]["rfc"]["rfcId"]."',
				'".$totales["iva"]."',
				'".$myData."',
				'".$myConceptos."',
				'".$myImpuestos."',
				'".$data["cadenaOriginal"]."',
				'".$cadenaOriginalTimbreSerialized."'
			)");
		$this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT comprobanteId FROM comprobante ORDER BY comprobanteId DESC LIMIT 1");
		$comprobanteId = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
			
		//insert conceptos
		foreach($_SESSION["conceptos"] as $concepto)
		{
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
				INSERT INTO `concepto` (
					`comprobanteId`, 
					`cantidad`, 
					`unidad`, 
					`noIdentificacion`, 
					`descripcion`, 
					`valorUnitario`, 
					`excentoIva`, 
					`importe`, 
					`userId`, 
					`empresaId`
				) VALUES (
					".$comprobanteId.", 
					".$concepto["cantidad"].", 
					'".$concepto["unidad"]."', 
					'".$concepto["noIdentificacion"]."', 
					'".$concepto["descripcion"]."', 
					".$concepto["valorUnitario"].", 
					'".$concepto["excentoIva"]."', 
					".$concepto["importe"].", 
					".$userId.", 
					".$empresa["empresaId"]."
					)");
			$this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
		}
		
		//finally we update the 'consecutivo
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE serie SET consecutivo = consecutivo + 1 WHERE serieId = ".$serie["serieId"]);
	//	echo $this->Util()->DB()->query;		
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();		
		return $timbreXml["UUID"];
	}//GenerarComprobante
		
	function GenerarComprobante($data, $notaCredito = false)
	{
		$vs = new User;
		$myData = urlencode(serialize($data));
		$empresa = $this->Info();
		$values = explode("&", $data["datosFacturacion"]);
		unset($data["datosFacturacion"]);
		foreach($values as $key => $val)
		{
			$array = explode("=", $values[$key]);
			$data[$array[0]] = $array[1];
		}

		$tipoSerie = explode("-", $data["tiposComprobanteId"]);
		
		if($tipoSerie[0] == 0)
		{
			$vs->Util()->setError(1, "error", "Por favor selecciona una serie");
		}
		if($vs->Util()->PrintErrors()){ return false; }
		
		$data["tiposComprobanteId"] = $tipoSerie[0];
		$data["tiposSerieId"] = $tipoSerie[1];

		if($data["tiposSerieId"] == "5")
		{
			$empresaIdFacturador = 15;
			$_SESSION['empresaId'] = 15;
			$empresa['empresaId'] = 15;
			$emisor = $emisorHuerin;
		}
		elseif($data["tiposSerieId"] == "51")
		{
			$empresaIdFacturador = 20;
			$_SESSION['empresaId'] = 20;
			$empresa['empresaId'] = 20;
			$emisor = $emisorBraun;
		}		
		else
		{
			$empresaIdFacturador = 15;
			$_SESSION['empresaId'] = 15;
			$empresa['empresaId'] = 15;
			$emisor = $emisorBraun;
		}		
				
		$data["tiposComprobanteId"] = $tipoSerie[0];
		$data["tiposSerieId"] = $tipoSerie[1];
		
		
		$vs->setRFC($data["rfc"]);
		$vs->setCalle($data["calle"]);
		$vs->setPais($data["pais"]);

		if(strlen($data["formaDePago"]) <= 0)
		{
			$vs->Util()->setError("", "error", "Por favor selecciona una serie");
		}

		if(count($_SESSION["conceptos"]) < 1)
		{
			$vs->Util()->setError(20040, "error", "");
		}
		
		$myConceptos = urlencode(serialize($_SESSION["conceptos"]));
		$myImpuestos = urlencode(serialize($_SESSION["impuestos"]));
		
		$userId = $data["userId"];
		
		$totales = $this->GetTotalDesglosado($data);
		//print_r($totales);
		//echo "jere";
		if($vs->Util()->PrintErrors()){ return false; }
		
		if(!$data["tipoDeCambio"])
		{
			$data["tipoDeCambio"] = "1.00";
		}

		if(!$data["porcentajeDescuento"])
		{
			$data["porcentajeDescuento"] = "0";
		}

		if(!$data["porcentajeIEPS"])
		{
			$data["porcentajeIEPS"] = "0";
		}
		
		//get active rfc
		$activeRfc =  $vs->getRfcActive();
		//get datos serie de acuerdo al tipo de comprobabte expedido.
		
		if(!$data["metodoDePago"])
		{
			$vs->Util()->setError(10047, "error", "El metodo de pago no puede ser vacio");
		}

		if($data["NumCtaPago"])
		{
			if(strlen($data["NumCtaPago"]) < 4)
			{
				$vs->Util()->setError(10047, "error", "El numero de cuenta debe de tener 4 digitos");
			}
		}
		
		if(!$data["tiposComprobanteId"])
		{
			$vs->Util()->setError(10047, "error");
		}

		if($vs->Util()->PrintErrors()){ return false; }
		
		if($notaCredito)
		{
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE tiposComprobanteId = '2' AND empresaId = ".$_SESSION["empresaId"]." AND rfcId = '".$activeRfc."' AND consecutivo <= folioFinal AND serieId = ".$data["tiposSerieId"]." ORDER BY serieId DESC LIMIT 1");
		}
		else
		{
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE tiposComprobanteId = ".$data["tiposComprobanteId"]." AND empresaId = ".$_SESSION["empresaId"]." AND rfcId = '".$activeRfc."' AND consecutivo <= folioFinal AND serieId = ".$data["tiposSerieId"]." ORDER BY serieId DESC LIMIT 1");
		}
		
		$serie = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		if(!$serie)
		{
			$vs->Util()->setError(10047, "error", "No existe la serie");
		}

		if($vs->Util()->PrintErrors()){ return false; }

		$folio = $serie["consecutivo"];
		$fecha = $this->Util()->FormatDateAndTime(time());
		//$fecha = "2012-02-07 14:55:52";
		
		//el tipo de comprobante lo determina tiposComprobanteId
		$tipoDeComprobante = $this->GetTipoComprobante($data["tiposComprobanteId"]);
		$data["comprobante"] = $this->InfoComprobante($data["tiposComprobanteId"]);

		$data["serie"] = $serie;
		$data["folio"] = $folio;
		$data["fecha"] = $fecha;
		$data["tipoDeComprobante"] = $tipoDeComprobante;
		$data["certificado"] = $serie["noCertificado"];

		//build informacion nodo emisor
		$myEmpresa = new Empresa;
		$myEmpresa->setEmpresaId($_SESSION["empresaId"], 1);
		$myEmpresa->setSucursalId($data["sucursalId"]);
		$nodoEmisor = $myEmpresa->GetSucursalInfo();

		$this->setRfcId($activeRfc);
		$nodoEmisorRfc = $this->InfoRfc();
		$data["nodoEmisor"]["sucursal"] = $nodoEmisor;
		$data["nodoEmisor"]["rfc"] = $nodoEmisorRfc;

		if(!$data["nodoEmisor"]["rfc"]["regimenFiscal"])
		{
			$vs->Util()->setError(10047, "error", "Necesitas el Regimen Fiscal, esto se actualiza en Datos Generales, en la opcion de edicion.");
			if($vs->Util()->PrintErrors()){ return false; }
		}

		if($_SESSION["version"] == "auto")
		{
			$rootQr = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/qrs/";
			$qrRfc = strtoupper($nodoEmisorRfc["rfc"]);
			$nufa = $serie["serieId"]."_".$serie["noAprobacion"]."_".$qrRfc.".png";
			//echo $rootQr.$nufa;
			if(!file_exists($rootQr.$nufa))
			{
				$nufa = $serie["serieId"]."_".$serie["noAprobacion"]."_".$qrRfc."_.png";
				if(!file_exists($rootQr.$nufa))
				{
					$nufa = $serie["serieId"].".png";
					if(!file_exists($rootQr.$nufa))
					{
						$vs->Util()->setError(10048, "error");
					}
				}
			}
	
			if($vs->Util()->PrintErrors()){ return false; }
			
		}
		$userId = $data["userId"];
																		 
		//build informacion nodo receptor
		$vs->setUserId($userId, 1);
		$nodoReceptor = $vs->GetUserInfo($userId);
		$data["nodoReceptor"] = $nodoReceptor;

		//checar si nos falta unidad en alguno
		foreach($_SESSION["conceptos"] as $concepto)
		{
			if($concepto["unidad"] == "")
			{
				$vs->Util()->setError(10048, "error", "El campo de Unidad no puede ser vacio");
			}
		}

		if($vs->Util()->PrintErrors()){ return false; }
		
		switch($_SESSION["version"])
		{
			case "auto":
			case "v3":
			case "construc":
				include_once(DOC_ROOT.'/classes/cadena_original_v3.class.php');break;
			case "2": 	
				include_once(DOC_ROOT.'/classes/cadena_original_v2.class.php');break;
		}
		$cadena = new Cadena;
		$cadenaOriginal = $cadena->BuildCadenaOriginal($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"]);
//		echo "ok|";
		$data["cadenaOriginal"] = utf8_encode($cadenaOriginal);
		$data["cadenaOriginal"] = $cadenaOriginal;
		$md5Cadena = utf8_decode($cadenaOriginal);

		$md5 = sha1($md5Cadena);

		$sello = $this->GenerarSello($cadenaOriginal, $md5);
		$data["sello"] = $sello["sello"];
		$data["certificado"] = $sello["certificado"];

		switch($_SESSION["version"])
		{
			case "auto":
			case "v3":
			case "construc":
				include_once(DOC_ROOT.'/classes/generate_xml_default.class.php');break;
			case "2":	
				include_once(DOC_ROOT.'/classes/generate_xml_v2.class.php');break;
		}
		
		$xmlGen = new XmlGen;
		$xml = $xmlGen->GenerateXML($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);

		//despues de la generacion del xml, viene el timbrado.
		if(1 == 1)
		{
			$nufa = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
			$rfcActivo = $this->getRfcActive();
			$root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/";
			$root_dos = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/timbres/";
			//	echo $root.$nufa;
			$nufa_dos = "SIGN_".$empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
			$xmlFile = $root.$nufa.".xml";
			$zipFile = $root.$nufa.".zip";
			@unlink($zipFile);
			$signedFile = $root.$nufa."_signed.zip";
			@unlink($signedFile);
			$timbreFile = $root.$nufa."_timbre.zip";
			@unlink($timbreFile);
//			$timbradoFile = $root."timbreCFDi.xml";
//			$timbradoFile = $root."/timbres/".$nufa.".xml";
			$timbradoFile = $root.$nufa_dos.".xml";
			@unlink($timbradoFile);
			
			
			$this->Util()->Zip($root, $nufa);
		
			$user = USER_PAC;
			$pw = PW_PAC;
			$pac = new Pac;
			$response = $pac->GetCfdi($user, $pw, $zipFile, $root, $signedFile, $empresa["empresaId"]);
			if($response == "fault")
			{
				echo "<b><br><br>Has encontrado un error al Sellar tu Comprobante. Favor de reportarnos este error con todos los detalles posibles. Gracias!!</b>";
				return false;
			}
	
			//$fileTimbreXml = $pac->GetTimbreCfdi($user, $pw, $zipFile, $root, $timbreFile, $empresa["empresaId"]);
			$timbreXml = $pac->ParseTimbre($timbradoFile);
			
			$cadenaOriginalTimbre = $pac->GenerateCadenaOriginalTimbre($timbreXml);
			$cadenaOriginalTimbreSerialized = serialize($cadenaOriginalTimbre);

			//add addenda
			if($_SESSION["impuestos"])
			{
				$nufa = "SIGN_".$empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
				$realSignedXml = $root.$nufa.".xml";
				$strAddenda = "<cfdi:Addenda>";
				foreach($_SESSION["impuestos"] as $impuesto)
				{
					$strAddenda .= "  <cfdi:impuesto tipo=\"".$impuesto["tipo"]."\" nombre=\"".$impuesto["impuesto"]."\" importe=\"".$impuesto["importe"]."\" tasa=\"".$impuesto["tasaIva"]."\" />";
				}
				$strAddenda .= "</cfdi:Addenda>";
				
				$fh = fopen($realSignedXml, 'r');
				$theData = fread($fh, filesize($realSignedXml));
				fclose($fh);
				$theData = str_replace("</cfdi:Complemento>", "</cfdi:Complemento>".$strAddenda, $theData);
				
		
				$fh = fopen($realSignedXml, 'w') or die("can't open file");
				fwrite($fh, $theData);
				fclose($fh);
			}

//		print_r($cadenaOriginalTimbre);
			$data["timbreFiscal"] = $cadenaOriginalTimbre;
		}
		//generatePDF
		//cambios 29 junio 2011

		switch($empresa["empresaId"])
		{
			default: 
				//include_once(DOC_ROOT."/classes/override_generate_pdf_default.php");
				$override = new Override;
				$pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);
		}
	//	return false;
		//cambios 29 junio 2011
		//insert new comprobante
		switch($data["tiposDeMoneda"])
		{
			case "MXN": $data["tiposDeMoneda"] = "peso"; break;
			case "USD": $data["tiposDeMoneda"] = "dolar"; break;
			case "EUR": $data["tiposDeMoneda"] = "euro"; break;
		}
		
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			INSERT INTO `comprobante` (
				`comprobanteId`, 
				`userId`, 
				`formaDePago`, 
				`condicionesDePago`, 
				`metodoDePago`, 
				`tasaIva`, 
				`tipoDeMoneda`, 
				`tipoDeCambio`, 
				`porcentajeRetIva`, 
				`porcentajeRetIsr`, 
				`tiposComprobanteId`, 
				`porcentajeIEPS`, 
				`porcentajeDescuento`, 
				`empresaId`, 
				`sucursalId`, 
				`observaciones`,
				`serie`,
				`folio`,
				`fecha`,
				`sello`,
				`noAprobacion`,
				`anoAprobacion`,
				`noCertificado`,
				`certificado`,
				`subtotal`,
				`descuento`,
				`motivoDescuento`,
				`total`,
				`tipoDeComprobante`,
				`xml`,
				`rfcId`,
				`ivaTotal`,
				`data`,
				`conceptos`,
				`impuestos`,
				`cadenaOriginal`,
				`timbreFiscal`
			) VALUES 
			(
			 	NULL, 
				'".$userId."', 
				'".$data["formaDePago"]."', 
				'".$data["condicionesDePago"]."', 
				'".$data["metodoDePago"]."', 
				'".$data["tasaIva"]."', 
				'".$data["tiposDeMoneda"]."', 
				'".$data["tipoDeCambio"]."', 
				'".$data["porcentajeRetIva"]."', 
				'".$data["porcentajeRetIsr"]."', 
				'".$data["tiposComprobanteId"]."', 
				'".$data["porcentajeIEPS"]."', 
				'".$data["porcentajeDescuento"]."', 
				'".$empresa["empresaId"]."', 
				'".$data["sucursalId"]."', 
				'".$data["observaciones"]."',
				'".$serie["serie"]."',
				'".$folio."',
				'".$fecha."',
				'".$data["sello"]."',
				'".$serie["noAprobacion"]."',
				'".$serie["anoAprobacion"]."',
				'".$serie["noCertificado"]."',
				'".$data["certificado"]."',
				'".$totales["subtotal"]."',
				'".$totales["descuento"]."',
				'".$data["motivoDescuento"]."',
				'".$totales["total"]."',
				'".$tipoDeComprobante."',
				'".$xml."',
				'".$data["nodoEmisor"]["rfc"]["rfcId"]."',
				'".$totales["iva"]."',
				'".$myData."',
				'".$myConceptos."',
				'".$myImpuestos."',
				'".$data["cadenaOriginal"]."',
				'".$cadenaOriginalTimbreSerialized."'
			)");
			$this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT comprobanteId FROM comprobante ORDER BY comprobanteId DESC LIMIT 1");
			$comprobanteId = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
		//insert conceptos
		foreach($_SESSION["conceptos"] as $concepto)
		{
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
				INSERT INTO `concepto` (
					`comprobanteId`, 
					`cantidad`, 
					`unidad`, 
					`noIdentificacion`, 
					`descripcion`, 
					`valorUnitario`, 
					`excentoIva`, 
					`importe`, 
					`userId`, 
					`empresaId`
				) VALUES (
					".$comprobanteId.", 
					".$concepto["cantidad"].", 
					'".$concepto["unidad"]."', 
					'".$concepto["noIdentificacion"]."', 
					'".$concepto["descripcion"]."', 
					".$concepto["valorUnitario"].", 
					'".$concepto["excentoIva"]."', 
					".$concepto["importe"].", 
					".$userId.", 
					".$empresa["empresaId"]."
					)");
			$this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
		}
		
		//finally we update the 'consecutivo
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE serie SET consecutivo = consecutivo + 1 WHERE serieId = ".$serie["serieId"]);
	//	echo $this->Util()->DB()->query;		
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();		
		return true;
	}//GenerarComprobante
	
	function CancelarComprobante($data, $id_comprobante, $notaCredito = false, $recipient)
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT noCertificado, xml, rfc, comprobante.empresaId, comprobante.rfcId FROM comprobante
			LEFT JOIN cliente ON cliente.userId = comprobante.userId
			WHERE comprobanteId = ".$id_comprobante);
	//	echo $this->Util()->DB()->query;		
		$row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();		
		$xml = $row["xml"];

		$info = $this->Info();
		//print_r($info);
		//print_r($row);
		if($info["version"] == "v3" || $info["version"] == "construc")
		{
			$rfcActivo = $this->getRfcActive();
			//$rfcActivo = $row["rfcId"];
			$root = DOC_ROOT."/empresas/".$row["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/SIGN_".$xml.".xml";		
			
			$fh = fopen($root, 'r');
			$theData = fread($fh, filesize($root));
			fclose($fh);
			
			$theData = explode("UUID", $theData);
			$theData = $theData[1];
			$theData = explode("FechaTimbrado", $theData);
			$theData = $theData[0];
			$uuid = str_replace("\"", "", $theData);
			$uuid = str_replace("=", "", $uuid);
			$uuid = str_replace(" ", "", $uuid);
			
			$path = DOC_ROOT."/empresas/".$row["empresaId"]."/certificados/".$rfcActivo."/".$row["noCertificado"].".cer.pfx";
			
			$user = USER_PAC;
			$pw = PW_PAC;
			$pac = new Pac;
	
			//get password
			$root = DOC_ROOT."/empresas/".$row["empresaId"]."/certificados/".$rfcActivo."/password.txt";		
			$fh = fopen($root, 'r');
			$password = fread($fh, filesize($root));
			
			fclose($fh);
	
			if(!$password)
			{
				$this->Util()->setError('', "error", "Tienes que actualizar tu certificado para que podamos obtener el password");
				$this->Util()->PrintErrors();
				return false;
			}
			$this->setRfcId(1);
			$nodoEmisorRfc = $this->InfoRfc();
			$response = $pac->CancelaCfdi($user, $pw, $nodoEmisorRfc["rfc"], $uuid, $path, $password);
			
		}
		
		if(!$response["cancelaCFDiReturn"]["text"])
		{
			$this->Util()->setError('', "complete", "Hubo un problema al cancelar tu Factura. Por favor permite que pasen al menos 24 horas antes de intentar de nuevo.");
			$this->Util()->PrintErrors();	
			return false;			
		}		
		//echo "here";
		
			//$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE comprobante SET status = '0' WHERE comprobanteId = ".$id_comprobante);
			//$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
			$fileName = $xml.".pdf";	
			$path = DOC_ROOT."/empresas/".$row["empresaId"]."/certificados/".$rfcActivo."/facturas/pdf/".$fileName;
			//chmod($path, 0777);
			$pdf = new FPDI();
	 
			$pagecount = $pdf->setSourceFile($path); 
			$tplidx = $pdf->importPage(1, '/MediaBox'); 
	 
			$pdf->addPage(); 
			$pdf->useTemplate($tplidx, 0, 0, 210); 
		 
			$pdf->AddFont('verdana','','verdana.php');
			$pdf->SetFont('verdana','',72);

			$pdf->SetY(100);
			$pdf->SetX(10);
 			$pdf->SetTextColor(200, 0, 0);
	  	$pdf->Cell(20,10,"CANCELADO",0,0,'L');
			$pdf->Output($path, 'F'); 
			//echo "jere";
			$this->Util()->setError('20027', "complete", "El folio ha sido cancelado exitosamente");
			$this->Util()->PrintErrors();			
			return true;			
		
	}//CancelarComprobante


	function GetTotalDesglosado($data)
	{
		if(!$_SESSION["conceptos"])
		{
			return false;
		}

		$data["subtotal"] = 0;
		$data["descuento"] = 0;
		$data["iva"] = 0;
		$data["ieps"] = 0;
		$data["retIva"] = 0;
		$data["retIsr"] = 0;
		$data["total"] = 0;

		foreach($data as $key => $value)
		{
			$data[$key] = $this->Util()->RoundNumber($data[$key]);
		}

		foreach($_SESSION["conceptos"] as $key => $concepto)
		{
			
			//cada concepto correrle los impuestos extra.
			if($_SESSION["impuestos"])
			{
				$importe = $concepto["importe"];
				foreach($_SESSION["impuestos"] as $keyImpuesto => $impuesto)
				{
//					print_r($impuesto);
					//impuesto extra, suma
					if($_SESSION["impuestos"][$keyImpuesto]["importe"] != 0)
					{
//						echo $_SESSION["impuestos"][$keyImpuesto]["importe"];													 
						if($impuesto["tipo"] == "impuesto")
						{
							$concepto["importe"] = $concepto["importe"] + $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "retencion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "deduccion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "amortizacion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						
						continue;
					}
					
					if($impuesto["tipo"] == "impuesto")
					{
						$concepto["importe"] = $concepto["importe"] + ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}
					elseif($impuesto["tipo"] == "retencion")
					{
						$concepto["importe"] = $concepto["importe"] - ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}
					elseif($impuesto["tipo"] == "deduccion")
					{
						$concepto["importe"] = $concepto["importe"] - ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}
	
				}//foreach
			}//impuestos

			$data["subtotal"] = $this->Util()->RoundNumber($data["subtotal"] + $concepto["importe"]);
			if($concepto["excentoIva"] == "si")
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = 0;
			}
			else
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = $data["tasaIva"];
			}
			//porcentaje de descuento
			if($data["porcentajeDescuento"])
			{
				$data["porcentajeDescuento"];
			}
			
			$data["descuentoThis"] = $this->Util()->RoundNumber($_SESSION["conceptos"][$key]["importe"] * ($data["porcentajeDescuento"] / 100));
			$data["descuento"] += $data["descuentoThis"]; 
			
			$afterDescuento = $_SESSION["conceptos"][$key]["importe"] - $data["descuentoThis"];
			if($concepto["excentoIva"] == "si")
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = 0;
			}
			else
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = $data["tasaIva"];
			}
	
			$data["ivaThis"] = $this->Util()->RoundNumber($afterDescuento * ($_SESSION["conceptos"][$key]["tasaIva"] / 100));
			$data["iva"] += $data["ivaThis"];
			$data["valorUnitario"] = $concepto["valorUnitario"];
			$concepto["importe"] = $importe;

		}
		//print_r($data);
		$afterDescuento = $data["subtotal"] - $data["descuento"];
		//ieps de descuento
		if(!$data["porcentajeIEPS"])
		{
			$data["porcentajeIEPS"] = 0;
		}
		$data["ieps"] = $this->Util()->RoundNumber($afterDescuento * ($data["porcentajeIEPS"] / 100));
		$afterImpuestos = $afterDescuento + $data["iva"] + $data["ieps"];

		$data["retIva"] = $this->Util()->RoundNumber($afterDescuento * ($data["porcentajeRetIva"] / 100));
		$data["retIsr"] = $this->Util()->RoundNumber($afterDescuento * ($data["porcentajeRetIsr"] / 100));
		$data["total"] = $this->Util()->RoundNumber($data["subtotal"] - $data["descuento"] + $data["iva"] + $data["ieps"] - $data["retIva"] - $data["retIsr"]);
		
		return $data;
	}
	
	protected function GetTipoComprobante($value)
	{
			$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT tipoDeComprobante FROM tiposComprobante WHERE tiposComprobanteId = ".$value." LIMIT 1");
			return $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
	}
	

	function GenerarSello($cadenaOriginal, $md5)
	{
		$root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/";
		
		if(!is_dir($root))
		{
			mkdir($root, 0777);
		}		

		
		$data["certificado"] = $arr['noCertificado'];
		$rfcActivo = $this->getRfcActive();
		$root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/";

		if(!is_dir($root))
		{
			mkdir($root, 0777);
		}		

		if ($handle = opendir($root)) 
		{
	    while (false !== ($file = readdir($handle))) 
			{
				$ext = substr($file, -7);
				 if($ext == "cer.pem")
				 {
					 $cert = $file;
				 }

				 if($ext == "key.pem")
				 {
					 $key = $file;
				 }
			}
		}

    closedir($handle);

		$file=$root.$key;      // Ruta al archivo
//		echo $md5;
		//write md5 to txt
		$fp = fopen ($root."/md5.txt", "w+");
    	fwrite($fp, $md5);
		fclose($fp);


		//sign the original md5 with private key
		exec("openssl dgst -sha1 -sign ".$file." -out ".$root."/md5sha1.txt ".$root."/md5.txt");

		$myFile = $root."/md5sha1.txt";
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		//generate public
		exec("openssl rsa -in ".$file." -pubout -out ".$root."/publickey.txt");		
				
		//verify
		exec("openssl dgst -sha1 -verify ".$root."/publickey.txt -out ".$root."/verified.txt -signature ".$root."/md5sha1.txt ".$root."/md5.txt");		
		
		//echo "here";
		$cadenaOriginalDecoded = utf8_decode($cadenaOriginal);

		$file = $root.$cert;      // Ruta al archivo
		$datos = file($file);
		$data["certificado"] = ""; 
		$carga=false;
		for ($i=0; $i<sizeof($datos); $i++) 
		{
				if (strstr($datos[$i],"END CERTIFICATE")) $carga=false;
				if ($carga)
				{ 
					$data["certificado"] .= trim($datos[$i]);
					
				}
				if (strstr($datos[$i],"BEGIN CERTIFICATE")) $carga=true;
		}
		$keyFile = $root.$key;
		$pkeyid = openssl_get_privatekey(file_get_contents($root.$key));
//		openssl_sign($cadenaOriginal, $crypttext, $pkeyid, OPENSSL_ALGO_SHA1);
		openssl_sign($cadenaOriginalDecoded, $crypttext, $pkeyid, OPENSSL_ALGO_SHA1);

		$data["sello"] = base64_encode($crypttext);  
		
		return $data;
	}

	function GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $nodosConceptos,$empresa, $cancelado = 0, $vistaPrevia = 0)
	{
	}
	
	/** Demo Testing PDF **/
	
	function GenerateDemoPDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $nodosConceptos,$empresa, $cancelado = 0)
	{
	}
	
	/** End Demo **/



	function GenerarSelloGral($cadenaOriginal, $md5, $certificado, $llave, $pass, $id_rfc)
	{
		$id_empresa = $_SESSION['empresaId'];
		
		$file = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/'.$certificado.'.pem';      // Ruta al archivo
		$root = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc;

		//write md5 to txt
		$fp = fopen ($root."/md5.txt", "w+");
    	fwrite($fp, $md5);
		fclose($fp);
		
		//generate .key.pem file
		exec('openssl pkcs8 -inform DER -in '.$root.'/'.$certificado.' -out '.$root.'/'.$certificado.'.pem -passin pass:'.$pass);
		
		//generate .cer.pem file
		exec('openssl x509 -inform DER -outform PEM -in '.$root.'/'.$llave.' -out '.$root.'/'.$llave.'.pem');

		//sign the original md5 with private key
		exec("openssl dgst -sha1 -sign ".$file." -out ".$root."/md5sha1.txt ".$root."/md5.txt");

		//generate public
		exec("openssl rsa -in ".$file." -pubout -out ".$root."/publickey.txt");		
				
		//verify
		exec("openssl dgst -sha1 -verify ".$root."/publickey.txt -out ".$root."/verified.txt -signature ".$root."/md5sha1.txt ".$root."/md5.txt");	
		
		//changed 02 agosto
		//generate merge
		exec('openssl pkcs12 -export -out '.$root.'/'.$llave.'.pfx -inkey '.$root.'/'.$certificado.'.pem -in '.$root.'/'.$llave.'.pem -passout pass:'.$pass);
		//changed 02 agosto
			
	
	}//GenerarSelloGral

	function GetLastComprobante()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante ORDER BY comprobanteId DESC");
		
		$row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		$row["path"] = urlencode(WEB_ROOT."/empresas/".$row["empresaId"]."/certificados/".$row["rfcId"]."/facturas/");
		
		return $row;
	}
	
	function GetInfoComprobante($id_comprobante,$efectivo=false){
	
	if($efectivo)
	{
				$sqlQuery="SELECT * FROM instanciaServicio
			LEFT JOIN servicio ON instanciaServicio.servicioId=servicio.servicioId
			LEFT JOIN contract ON servicio.contractId=contract.contractId
			LEFT JOIN customer ON contract.customerId=customer.customerId
			WHERE contract.facturador='Efectivo' AND instanciaServicio.instanciaServicioId='".$id_comprobante."'";

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		$sqlQuery = 'SELECT * FROM payment WHERE instanciaServicioId = '.$id_comprobante;	
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row["payments"] = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

		$sqlQuery = 'SELECT SUM(amount) FROM payment WHERE instanciaServicioId = '.$id_comprobante;	
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row["payment"] = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
		
		$row['cxcAmountDiscount'] = $row['costo'] * ($row['cxcDiscount'] / 100);
		$row['costo'] = $row['costo'] - $row['cxcAmountDiscount'];
		
		$row["saldo"] = $row["costo"] - $row["payment"];
		return $row;	
	}
	else
	{
		$sqlQuery = 'SELECT comprobante.*,contract.facturador FROM comprobante 
					LEFT JOIN contract ON contract.contractId = comprobante.userId
						WHERE comprobanteId = '.$id_comprobante;	
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
		
		$sqlQuery = 'SELECT * FROM payment WHERE comprobanteId = '.$id_comprobante;	
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row["payments"] = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

		$sqlQuery = 'SELECT SUM(amount) FROM payment WHERE comprobanteId = '.$id_comprobante;	
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$row["payment"] = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();
		
		$row['cxcAmountDiscount'] = $row['total'] * ($row['cxcDiscount'] / 100);
		$row['total'] = $row['total'] - $row['cxcAmountDiscount'];
		
		$row["saldo"] = $row["total"] - $row["payment"];
		return $row;
	}
	}//GetInfoComprobante
	
	function GetComprobantesByRfc(){
	
		global $user;
				
		$id_rfc = $this->getRfcActive();

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery('SELECT COUNT(*) FROM comprobante ORDER BY fecha DESC');
		$total = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/sistema/consultar-facturas");

		$sqlAdd = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];

		$sqlQuery = 'SELECT *, comprobante.status AS status, comprobante.comprobanteId AS comprobanteId FROM comprobante 
		LEFT JOIN instanciaServicio ON instanciaServicio.comprobanteId = comprobante.comprobanteId 
	ORDER BY fecha DESC '.$sqlAdd;
				
		$id_empresa = $_SESSION['empresaId'];
		
		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();
		
		$info = array();
		foreach($comprobantes as $key => $val){
		//	print_r($val);
			$user->setUserId($val['userId'],1);
			$usr = $user->GetUserInfo();
			$card['rfc'] = $usr['rfc'];
			$card['nombre'] = $usr['nombre'];
			$card['fecha'] = date('d-m-Y',strtotime($val['fecha']));

			
			$cadenaOriginal = explode("|", $val["cadenaOriginal"]);
			$card['subTotal'] = $val["subTotal"];
			$card['total'] = $val["total"];
//			$card['ivaTotal'] = $val["ivaTotal"];
																		 
			foreach($cadenaOriginal as $keyCadena => $cadena)
			{
				//print_r($cadena);
				if($cadena == "IVA")
				{
					if($countIvas == 1)
					{
						$card['ivaTotal'] = $cadenaOriginal[$keyCadena + 2];
						$countIvas = 0;
					}
					$countIvas++;
				}

				//print_r($cadena);
				if($cadena == "ISR")
				{
					$card['isrRet'] = $cadenaOriginal[$keyCadena + 1];
				}

			}

			$card['total_formato'] = number_format($card['total'],2,'.',',');
			$card['subtotal_formato'] = number_format($card['subTotal'],2,'.',',');
			$card['iva_formato'] = number_format($card['ivaTotal'],2,'.',',');
			$card['serie'] = $val['serie'];
			$card['folio'] = $val['folio'];
			$card['comprobanteId'] = $val['comprobanteId'];
			$card['status'] = $val['status'];
			$card['tipoDeComprobante'] = $val['tipoDeComprobante'];
			$card['instanciaServicioId'] = $val['instanciaServicioId'];

			$timbreFiscal = unserialize($val['timbreFiscal']);
			$card["uuid"] = $timbreFiscal["UUID"];
			$card['version'] = $val['version'];
			$card['xml'] = $val['xml'];

			$info[$key] = $card;
			
		}//foreach

		$data["items"] = $info;
		$data["pages"] = $pages;
		$data["total"] = $total;

		return $data;
		
	}//GetComprobantesByRfc
	
	function SearchComprobantesByRfc($values){
	
		global $user;
		
		$sqlSearch = '';
		
		if($values['folio'] && !$values["folioA"]){
			$sqlSearch .= ' AND c.folio = "'.$values['folio'].'"';
		}//if

		if($values['folio'] && $values["folioA"]){
			$sqlSearch .= ' AND c.folio >= "'.$values['folio'].'" AND c.folio <="'.$values["folioA"].'"';
		}//if
		
		if($values['rfc']){
			$sqlSearch .= ' AND contract.rfc LIKE "%'.$values['rfc'].'%"';
		}//if
		
		if($values['nombre']){
			$sqlSearch .= ' AND (customer.nameContact LIKE "%'.$values['nombre'].'%" OR contract.name LIKE "%'.$values['nombre'].'%")';
		}//if
				
		if($values['mes']){
			$sqlSearch .= ' AND EXTRACT(MONTH FROM c.fecha) = '.$values['mes'];
		}//if
		
		if($values['anio'])
			$sqlSearch .= ' AND EXTRACT(YEAR FROM c.fecha) = '.intval($values['anio']);		
		
		if($values['status_activo'] != '')		
			$sqlSearch .= ' AND c.status = "'.$values['status_activo'].'"';
		
		if($values['comprobante'])
			$sqlSearch .= ' AND c.tiposComprobanteId = '.$values['comprobante'];
		
		if($values['sucursal'])
			$sqlSearch .= ' AND c.sucursalId = '.$values['sucursal'];

		if($values['facturador'])
			$sqlSearch .= ' AND c.rfcId = '.$values['facturador'];
		
		$id_rfc = $this->getRfcActive();

		//$sqlQuery = 'SELECT * FROM comprobante ORDER BY serie ASC, fecha DESC, comprobanteId DESC';
		//echo "jere";
		$sqlQuery = 'SELECT c.*, c.status AS status , c.comprobanteId AS comprobanteId, tipoServicio.nombreServicio AS concepto, contract.name AS name, contract.rfc AS rfc, contract.contractId AS contractId, instanciaServicio.instanciaServicioId
			FROM comprobante as c
			LEFT JOIN contract ON contract.contractId = c.userId
			LEFT JOIN customer ON customer.customerId = contract.customerId 
			LEFT JOIN instanciaServicio ON instanciaServicio.comprobanteId = c.comprobanteId
			LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId			
			LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
			WHERE 1 '.$sqlSearch.' ORDER BY c.serie ASC, fecha DESC, c.comprobanteId DESC '.$sqlAdd;
	//	exit;	 	
		$id_empresa = $_SESSION['empresaId'];
		
		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$comprobantes = $this->Util()->DBSelect($id_empresa)->GetResult();

		$sqlQuery = 'SELECT nombreServicio FROM tipoServicio';

		$this->Util()->DBSelect($id_empresa)->setQuery($sqlQuery);
		$servicios = $this->Util()->DBSelect($id_empresa)->GetResult();
		
		$info = array();
		//echo count($comprobantes);
		foreach($comprobantes as $key => $val){
			//print_r($val);
			$user->setUserId($val['userId'],1);
			$usr = $user->GetUserInfo();
			$card['serie'] = $val['serie'];
			$card['folio'] = $val['folio'];
			$card['rfc'] = $usr['rfc'];
			$cadenaOAux = strtolower($val["cadenaOriginal"]);
			$auxC = true;
			foreach ($servicios as $serv) {
				$servicioAux = "|".strtolower($serv['nombreServicio'])." correspondiente ";
				$index = strpos($cadenaOAux,$servicioAux);
				if($index){
					$inicio=strripos(substr($val["cadenaOriginal"], 0,$index+1), "|");
					$aux=explode("|",substr($val["cadenaOriginal"], $inicio));
					$card['concepto'] = $aux[1];
					$auxC = false;
					continue;
				}
			}
			if ($auxC){
				foreach ($servicios as $serv) {
					$servicioAux = "|".strtolower($serv['nombreServicio'])." ";
					$index = strpos($cadenaOAux,$servicioAux);
					if($index){
						$inicio=strripos(substr($val["cadenaOriginal"], 0,$index+1), "|");
						$aux=explode("|",substr($val["cadenaOriginal"], $inicio));
						$card['concepto']  = $aux[1];
						$auxC = false;
						continue;
					}
				}
			}
			if ($auxC){
				foreach ($servicios as $serv) {
					$servicioAux = "|".strtolower($serv['nombreServicio'])."|";
					$index = strpos($cadenaOAux,$servicioAux);
					if($index){
						$inicio=strripos(substr($val["cadenaOriginal"], 0,$index+1), "|");
						$aux=explode("|",substr($val["cadenaOriginal"], $inicio));
						$card['concepto']  = $aux[1];
						$auxC = false;
						continue;
					}
				}
			}
			if($auxC){
				$servicioAux = " correspondiente";
				$index = strpos($cadenaOAux,$servicioAux);
				if($index){
					$inicio=strripos(substr($val["cadenaOriginal"], 0,$index), "|");
					$aux=explode("|",substr($val["cadenaOriginal"], $inicio));
					$card['concepto'] = $aux[1];
					$auxC = false;
				}
				else{
					$servicioAux = " del mes de ";
					$index = strpos($cadenaOAux,$servicioAux);
					if($index){
						$inicio=strripos(substr($val["cadenaOriginal"], 0,$index), "|");
						$aux=explode("|",substr($val["cadenaOriginal"], $inicio));
						$card['concepto'] = $aux[1];
						$auxC = false;
					}
				}
			}
			if($auxC){
				$card['concepto'] = $val['concepto'];
			}
			$card['nombre'] = $usr['nombre'];
			$card['fecha'] = date('d/m/Y',strtotime($val['fecha']));
			$card['subTotal'] = $val['subTotal'];
			$card['porcentajeDescuento'] = $val['porcentajeDescuento'];
			$card['descuento'] = $val['descuento'];
			$card['ivaTotal'] = $val['ivaTotal'];
			$card['total'] = $val['total'];
			$card['total_formato'] = number_format($val['total'],2,'.',',');
			$card['subtotal_formato'] = number_format($val['subTotal'],2,'.',',');
			$card['iva_formato'] = number_format($val['ivaTotal'],2,'.',',');
			$card['tipoDeMoneda'] = $val['tipoDeMoneda'];
			$card['tipoDeCambio'] = $val['tipoDeCambio'];
			$card['porcentajeRetIva'] = $val['porcentajeRetIva'];
			$card['porcentajeRetIsr'] = $val['porcentajeRetIsr'];
			$card['porcentajeIEPS'] = $val['porcentajeIEPS'];
			$card['comprobanteId'] = $val['comprobanteId'];
			$card['status'] = $val['status'];
			$card['tipoDeComprobante'] = $val['tipoDeComprobante'];

			$card['instanciaServicioId'] = $val['instanciaServicioId'];

			$timbreFiscal = unserialize($val['timbreFiscal']);
			$card["uuid"] = $timbreFiscal["UUID"];
			
			$info[$key] = $card;
			
		}//foreach
		
		$data["items"] = $info;
		$data["pages"] = $pages;
		$data["total"] = count($comprobantes);

		return $data;
		
	}//SearchComprobantesByRfc
	
	function GenerateQR($data, $totales, $nodoEmisor, $nodoReceptor, $empresa, $serie)
	{
		$total = $this->Util()->RoundNumber($totales["total"], 6);
		$total = $this->Util()->PadStringLeft(number_format($total, 6, ".", ""), 14, "0");
		$cadenaCodigoBarras = "?re=".$data["nodoEmisor"]["rfc"]["rfc"]."&rr=".$nodoReceptor["rfc"]."&tt=".$total."&id=".$data["timbreFiscal"]["UUID"];

		$nufa = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
		
		$rfcActivo = $this->getRfcActive();
		$root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/qr/";
		$rootFacturas = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/";
		
		if(!is_dir($rootFacturas))
		{
			mkdir($rootFacturas, 0777);
		}

		if(!is_dir($root))
		{
			mkdir($root, 0777);
		}
		
		//$pdf->Output($root.$nufa.".pdf", "F");
		QRcode::png($cadenaCodigoBarras, $root.$nufa.".png", 'L', 4, 2);

	}

	public function SendComprobante($id_comprobante){
		
		global $comprobante;
		
		$compInfo = $comprobante->GetInfoComprobante($id_comprobante);
		
		$id_cliente = $compInfo['userId'];
		$user = new User;
		$user->setUserId($id_cliente, 1);
		$usrInfo = $user->GetUserInfo();
		
		$nombre = $usrInfo['nombre'];
		$email = $usrInfo['email'];
//		$email = 'jmponce@braunhuerin.com.mx';

		$emails = array();
		$emails = $this->Util()->ExplodeEmails($email);
		@array_push($emails, SEND_TO); 
		@array_push($emails, SEND_TO2); 
		@array_push($emails, SEND_TO3); 
		//print_r($emails);
		//exit;
		/*** Archivo PDF ***/
		
		$id_rfc = $compInfo['rfcId'];
		$id_empresa = $compInfo['empresaId'];
		$serie = $compInfo['serie'];
		$folio = $compInfo['folio'];
		
		$archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
				
		$enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo; 
		
		if($_SESSION["version"] == "v3" || $_SESSION["version"] == "construc")
		{
			$archivo_xml = "SIGN_".$id_empresa.'_'.$serie.'_'.$folio.'.xml';
		}
		else
		{
			$archivo_xml = $id_empresa.'_'.$serie.'_'.$folio.'.xml';
		}

		$enlace_xml = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo_xml; 
		
		/*** End Archivo PDF ***/
	
		$empresa = new Empresa;
		$info = $empresa->GetPublicEmpresaInfo();

		$mail = new PHPMailer();
		$mail->Subject = 'Envio de Factura con Folio No. '.$folio;
		$fromName = "Administrador del Sistema";
		$mail->AddReplyTo(FROM_MAIL, $fromName);
		$mail->SetFrom(FROM_MAIL, $fromName);
		
		$mail->MsgHTML($body);
		$mail->SMTPAuth   = true;
		$mail->Host       = "mail.avantika.com.mx";
		$mail->Port       = 587;
		$mail->Username   = "smtp@avantika.com.mx";
		$mail->Password   = "smtp1234";
		//$mail->SMTPSecure="ssl";
		$mail->SMTPDebug=1;
		
		foreach($emails as $email)
		{
        	echo $email = trim($email);
        	$mail->AddAddress($email, 'Estimado Cliente');
		}
		
		$body = "<pre>Favor de revisar el archivo adjunto para ver factura.\r\n";
		$body .= "<br><br>";
		$body .= "...::: NOTIFICACION AUTOMATICA --- NO RESPONDER :::...<br><br>";
		
		if($compInfo["empresaId"] == 15)
		{
			$body .= "CFDi emitido por:BRAUN HUERIN SC<br>";
			$body .= "R.F.C: BHU120320CQ1<br>";
			$body .= "Numero de Cuenta al que debe de realizar su deposito:<br><br>";
			$body .= "Nombre    Braun Huerin S.C.<br><br>";
			$body .= "Banco     BBV Bancomer<br>";
			$body .= "Cuenta    0189768785<br>";
			$body .= "Clabe     012180-001897-687857<br><br>";
		}
		elseif($compInfo["empresaId"] == 20)
		{
			$body .= "CFDi emitido por: JACOBO BRAUN BRUCKMAN<br>";
			$body .= "R.F.C: BABJ701019LD7<br>";
			$body .= "Numero de Cuenta al que debe de realizar su deposito:<br><br>";
			$body .= "Nombre    Jacobo  Braun Bruckman<br><br>";
			$body .= "Banco     Banamex<br>";
			$body .= "Cuenta    6115476 Suc 378<br>";
			$body .= "Clabe      002180037861154769<br><br>";
		}
		
		$body .= "Gracias.<br>";
		$mail->Body = $body;
		
		//Adjuntamos un archivo
		$contract = new Contract;
		$contract->setContractId($compInfo["userId"]);
		$contrato = $contract->Info();

		$name = str_replace("&amp;", "", $contrato["name"]);
		$name = str_replace(" ", "_", $name);
			
		if (file_exists($enlace)) {
       		$mail->AddAttachment($enlace, 'Factura_'.$folio.'_'.$name.'.pdf');
		} else {
			$this->Util()->setError('', 'complete', "El documento no existe ");
			$this->Util()->PrintErrors();
			return false;
		}		
		
		if (file_exists($enlace_xml)) {
		   $mail->AddAttachment($enlace_xml, 'XML_Factura_'.$folio.'_'.$name.'.xml');
		} else {
			$this->Util()->setError('', 'complete', "El documento xml no existe ");
			$this->Util()->PrintErrors();
			return false;
		}
    
		if($mail->Send())
		{		
			$this->Util()->setError(20023, 'complete', "Has enviado el comprobante al contacto administrativo: ".$email);
      		$this->Util()->PrintErrors();
      		return true;
		}
		else
		{
			$this->Util()->setError(20023, 'complete', 'Hubo un error al enviar el comprobante, el correo de la cuenta es correcto?');
      		$this->Util()->PrintErrors();
      		return false;
		}
		
		$this->Util()->PrintErrors();	
	}//SendComprobante
}//Comprobante


require(DOC_ROOT.'/pdf/fpdf.php');
require(DOC_ROOT.'/pdf/fpdi.php');

class PDF extends FPDF
{
	
	var $widths;
	var $aligns;
	var $borders;
//Page header
function Header()
{
    //Logo
		//$this->Image('facturabg.png',0,0, 210, 297);		
    //Arial bold 15
    $this->SetFont('Arial','B',10);
		$this->SetY(0);
		$this->SetX(10);
    $this->Cell(190,10,'',0,0,'C');
    //Line break
    $this->Ln(20);
}

//Page footer
function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-8);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');

}

function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2f %.2f m', ($x+$r)*$k, ($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-$y)*$k ));
        if (strpos($angle, '2')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$yc)*$k));
        if (strpos($angle, '3')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-($y+$h))*$k));
        if (strpos($angle, '4')===false)
            $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$yc)*$k ));
        if (strpos($angle, '1')===false)
        {
            $this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$y)*$k ));
            $this->_out(sprintf('%.2f %.2f l', ($x+$r)*$k, ($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k, 
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
		
	function WordWrap(&$text, $maxwidth)
	{
			$text = trim($text);
			if ($text==='')
					return 0;
			$space = $this->GetStringWidth(' ');
			$lines = explode("\n", $text);
			$text = '';
			$count = 0;
	
			foreach ($lines as $line)
			{
					$words = preg_split('/ +/', $line);
					$width = 0;
	
					foreach ($words as $word)
					{
							$wordwidth = $this->GetStringWidth($word);
							if ($width + $wordwidth <= $maxwidth)
							{
									$width += $wordwidth + $space;
									$text .= $word.' ';
							}
							else
							{
									$width = $wordwidth + $space;
									$text = rtrim($text)."\n".$word.' ';
									$count++;
							}
					}
					$text = rtrim($text)."\n";
					$count++;
			}
			$text = rtrim($text);
			return $count;
	}
	
	function SetWidths($w)
	{
			//Set the array of column widths
			$this->widths=$w;
	}
	
	function SetAligns($a)
	{
			//Set the array of column alignments
			$this->aligns=$a;
	}

	function SetBorders($a)
	{
			//Set the array of column alignments
			$this->borders=$a;
	}
	
	function Row($data, $pixelHeight = 5)
	{
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb, $this->NbLines($this->widths[$i], $data[$i]));
			$h=$pixelHeight * $nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++)
			{
					$w=$this->widths[$i];
					$b=$this->borders[$i];
					
					if(!$b)
					{
						$b = 0;
					}
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		//			print_r($a);
					//Save the current position
					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border
					//$this->Rect($x, $y, $w, $h);
					//Print the text
					$this->MultiCell($w, $pixelHeight, $data[$i], $b, $a);
					//Put the position to the right of the cell
					$this->SetXY($x+$w, $y);
			}
			//Go to the next line
			$this->Ln($h);
	}
	
	function CheckPageBreak($h)
	{
			//If the height h would cause an overflow, add a new page immediately
			if($this->GetY()+$h>$this->PageBreakTrigger)
					$this->AddPage($this->CurOrientation);
	}
	
	function NbLines($w, $txt)
	{
			//Computes the number of lines a MultiCell of width w will take
			$cw=&$this->CurrentFont['cw'];
			if($w==0)
					$w=$this->w-$this->rMargin-$this->x;
			$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			$s=str_replace("\r", '', $txt);
			$nb=strlen($s);
			if($nb>0 and $s[$nb-1]=="\n")
					$nb--;
			$sep=-1;
			$i=0;
			$j=0;
			$l=0;
			$nl=1;
			while($i<$nb)
			{
					$c=$s[$i];
					if($c=="\n")
					{
							$i++;
							$sep=-1;
							$j=$i;
							$l=0;
							$nl++;
							continue;
					}
					if($c==' ')
							$sep=$i;
					$l+=$cw[$c];
					if($l>$wmax)
					{
							if($sep==-1)
							{
									if($i==$j)
											$i++;
							}
							else
									$i=$sep+1;
							$sep=-1;
							$j=$i;
							$l=0;
							$nl++;
					}
					else
							$i++;
			}
			return $nl;
	}	
}

class PDF_ImageAlpha extends PDF{ 

	//Private properties 
	var $tmpFiles = array();  
	
	/******************************************************************************* 
	*                                                                              * 
	*                               Public methods                                 * 
	*                                                                              * 
	*******************************************************************************/ 
	function Image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0) 
	{ 
			//Put an image on the page 
			if(!isset($this->images[$file])) 
			{ 
					//First use of image, get info 
					if($type=='') 
					{ 
							$pos=strrpos($file,'.'); 
							if(!$pos) 
									$this->Error('Image file has no extension and no type was specified: '.$file); 
							$type=substr($file,$pos+1); 
					} 
					$type=strtolower($type); 
					$mqr=@get_magic_quotes_runtime(); 
					@set_magic_quotes_runtime(0); 
					if($type=='jpg' || $type=='jpeg') 
							$info=$this->_parsejpg($file); 
					elseif($type=='png'){ 
							$info=$this->_parsepng($file); 
							if ($info=='alpha') return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link); 
					} 
					else 
					{ 
							//Allow for additional formats 
							$mtd='_parse'.$type; 
							if(!method_exists($this,$mtd)) 
									$this->Error('Unsupported image type: '.$type); 
							$info=$this->$mtd($file); 
					} 
					@set_magic_quotes_runtime($mqr); 
					 
					if ($isMask){ 
				$info['cs']="DeviceGray"; // try to force grayscale (instead of indexed) 
			} 
					$info['i']=count($this->images)+1; 
					if ($maskImg>0) $info['masked'] = $maskImg;### 
					$this->images[$file]=$info; 
			} 
			else 
					$info=$this->images[$file]; 
			//Automatic width and height calculation if needed 
			if($w==0 && $h==0) 
			{ 
					//Put image at 72 dpi 
					$w=$info['w']/$this->k; 
					$h=$info['h']/$this->k; 
			} 
			if($w==0) 
					$w=$h*$info['w']/$info['h']; 
			if($h==0) 
					$h=$w*$info['h']/$info['w']; 
			 
			if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageFormat[0]:$this->CurPageFormat[1]) + 10; // embed hidden, ouside the canvas 
			$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i'])); 
			if($link) 
					$this->Link($x,$y,$w,$h,$link); 
					 
			return $info['i']; 
	} 
	
	// needs GD 2.x extension 
	// pixel-wise operation, not very fast 
	function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link='') 
	{ 
			$tmp_alpha = tempnam('.', 'mska'); 
			$this->tmpFiles[] = $tmp_alpha; 
			$tmp_plain = tempnam('.', 'mskp'); 
			$this->tmpFiles[] = $tmp_plain; 
			 
			list($wpx, $hpx) = getimagesize($file); 
			$img = imagecreatefrompng($file); 
			$alpha_img = imagecreate( $wpx, $hpx ); 
			 
			// generate gray scale pallete 
			for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img, $c, $c, $c); 
			 
			// extract alpha channel 
			$xpx=0; 
			while ($xpx<$wpx){ 
					$ypx = 0; 
					while ($ypx<$hpx){ 
							$color_index = imagecolorat($img, $xpx, $ypx); 
							$alpha = 255-($color_index>>24)*255/127; // GD alpha component: 7 bit only, 0..127! 
							imagesetpixel($alpha_img, $xpx, $ypx, $alpha); 
					++$ypx; 
					} 
					++$xpx; 
			} 
	
			imagepng($alpha_img, $tmp_alpha); 
			imagedestroy($alpha_img); 
			 
			// extract image without alpha channel 
			$plain_img = imagecreatetruecolor ( $wpx, $hpx ); 
			imagecopy ($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx ); 
			imagepng($plain_img, $tmp_plain); 
			imagedestroy($plain_img); 
			 
			//first embed mask image (w, h, x, will be ignored) 
			$maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true);  
			 
			//embed image, masked with previously embedded mask 
			$this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg); 
	} 
	
	function Close() 
	{ 
			parent::Close(); 
			// clean up tmp files 
			foreach($this->tmpFiles as $tmp) @unlink($tmp); 
	} 
	
	/******************************************************************************* 
	*                                                                              * 
	*                               Private methods                                * 
	*                                                                              * 
	*******************************************************************************/ 
	function _putimages() 
	{ 
			$filter=($this->compress) ? '/Filter /FlateDecode ' : ''; 
			reset($this->images); 
			while(list($file,$info)=each($this->images)) 
			{ 
					$this->_newobj(); 
					$this->images[$file]['n']=$this->n; 
					$this->_out('<</Type /XObject'); 
					$this->_out('/Subtype /Image'); 
					$this->_out('/Width '.$info['w']); 
					$this->_out('/Height '.$info['h']); 
					 
					if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ### 
					 
					if($info['cs']=='Indexed') 
							$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]'); 
					else 
					{ 
							$this->_out('/ColorSpace /'.$info['cs']); 
							if($info['cs']=='DeviceCMYK') 
									$this->_out('/Decode [1 0 1 0 1 0 1 0]'); 
					} 
					$this->_out('/BitsPerComponent '.$info['bpc']); 
					if(isset($info['f'])) 
							$this->_out('/Filter /'.$info['f']); 
					if(isset($info['parms'])) 
							$this->_out($info['parms']); 
					if(isset($info['trns']) && is_array($info['trns'])) 
					{ 
							$trns=''; 
							for($i=0;$i<count($info['trns']);$i++) 
									$trns.=$info['trns'][$i].' '.$info['trns'][$i].' '; 
							$this->_out('/Mask ['.$trns.']'); 
					} 
					$this->_out('/Length '.strlen($info['data']).'>>'); 
					$this->_putstream($info['data']); 
					unset($this->images[$file]['data']); 
					$this->_out('endobj'); 
					//Palette 
					if($info['cs']=='Indexed') 
					{ 
							$this->_newobj(); 
							$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal']; 
							$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>'); 
							$this->_putstream($pal); 
							$this->_out('endobj'); 
					} 
			} 
	} 
	
	// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels. 
	// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script. 
	function _parsepng($file) 
	{ 
			//Extract info from a PNG file 
			$f=fopen($file,'rb'); 
			if(!$f) 
					$this->Error('Can\'t open image file: '.$file); 
			//Check signature 
			if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) 
					$this->Error('Not a PNG file: '.$file); 
			//Read header chunk 
			fread($f,4); 
			if(fread($f,4)!='IHDR') 
					$this->Error('Incorrect PNG file: '.$file); 
			$w=$this->_readint($f); 
			$h=$this->_readint($f); 
			$bpc=ord(fread($f,1)); 
			if($bpc>8) 
					$this->Error('16-bit depth not supported: '.$file); 
			$ct=ord(fread($f,1)); 
			if($ct==0) 
					$colspace='DeviceGray'; 
			elseif($ct==2) 
					$colspace='DeviceRGB'; 
			elseif($ct==3) 
					$colspace='Indexed'; 
			else { 
					fclose($f);      // the only changes are  
					return 'alpha';  // made in those 2 lines 
			} 
			if(ord(fread($f,1))!=0) 
					$this->Error('Unknown compression method: '.$file); 
			if(ord(fread($f,1))!=0) 
					$this->Error('Unknown filter method: '.$file); 
			if(ord(fread($f,1))!=0) 
					$this->Error('Interlacing not supported: '.$file); 
			fread($f,4); 
			$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>'; 
			//Scan chunks looking for palette, transparency and image data 
			$pal=''; 
			$trns=''; 
			$data=''; 
			do 
			{ 
					$n=$this->_readint($f); 
					$type=fread($f,4); 
					if($type=='PLTE') 
					{ 
							//Read palette 
							$pal=fread($f,$n); 
							fread($f,4); 
					} 
					elseif($type=='tRNS') 
					{ 
							//Read transparency info 
							$t=fread($f,$n); 
							if($ct==0) 
									$trns=array(ord(substr($t,1,1))); 
							elseif($ct==2) 
									$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1))); 
							else 
							{ 
									$pos=strpos($t,chr(0)); 
									if($pos!==false) 
											$trns=array($pos); 
							} 
							fread($f,4); 
					} 
					elseif($type=='IDAT') 
					{ 
							//Read image data block 
							$data.=fread($f,$n); 
							fread($f,4); 
					} 
					elseif($type=='IEND') 
							break; 
					else 
							fread($f,$n+4); 
			} 
			while($n); 
			if($colspace=='Indexed' && empty($pal)) 
					$this->Error('Missing palette in '.$file); 
			fclose($f); 
			return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data); 
	} 
	

	
} 


?>