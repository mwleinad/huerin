<?php

class Cfdi extends Comprobante
{

    function Generar($data, $notaCredito = false)
    {
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
        elseif($data["tiposSerieId"] == "52")
        {
            $empresaIdFacturador = 21;
            $_SESSION['empresaId'] = 21;
            $empresa['empresaId'] = 21;
            $emisor = $emisorBhsc;
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

        $vs = new User;

        //include_once(DOC_ROOT."/addendas/addenda_campos.php");
        if($vs->Util()->PrintErrors()){ return false; }

        if(!is_numeric($data["numDiasPagados"]) && $data["fromNomina"])
        {
            $vs->Util()->setError(10041, "error", "El numero de dias pagados debe de ser un numero");
        }

        if(strlen($data["formaDePago"]) <= 0)
        {
            $vs->Util()->setError(10041, "error", "");
        }

        if(count($_SESSION["conceptos"]) < 1)
        {
            $vs->Util()->setError(10040, "error", "");
        }

        if($data["fechaSobre"] != "")
        {
            $vs->Util()->ValidateString($data["fechaSobre"], 10, 10, "Fecha Factura");
        }

        if($data["folioSobre"] != "")
        {
            $vs->Util()->ValidateInteger($data["folioSobre"], 1000000000, 1);
        }

        $sobreescribirFecha = false;
        $data["sobreescribirFecha"] = false;
        if($data["fechaSobre"] != "" && $data["folioSobre"] > 0)
        {
            $sobreescribirFecha = true;
            $data["sobreescribirFecha"] = true;
        }

        if($vs->Util()->PrintErrors()){ return false; }

        $myConceptos = urlencode(serialize($_SESSION["conceptos"]));
        $myImpuestos = urlencode(serialize($_SESSION["impuestos"]));

        $userId = $data["userId"];

        $totales = $this->GetTotalDesglosado($data);
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
            $sql = "SELECT * FROM serie WHERE tiposComprobanteId = ".$data["tiposComprobanteId"]." AND empresaId = ".$_SESSION["empresaId"]." AND rfcId = '".$activeRfc."' AND consecutivo <= folioFinal AND serieId = ".$data["tiposSerieId"]." ORDER BY serieId DESC LIMIT 1";
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        }

        $serie = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
        if(!$serie)
        {
            $vs->Util()->setError(10047, "error");
        }

        if($vs->Util()->PrintErrors()){ return false; }

        if($sobreescribirFecha === true)
        {
            $folio = $data["folioSobre"];
        }
        else
        {
            $folio = $serie["consecutivo"];
        }
        $fecha = $this->Util()->FormatDateAndTime(time() - 600);
        $fechaPago = $this->Util()->FormatDate(time());

        if($_SESSION["empresaId"] == 292)
        {
            //$fecha = "2016-06-30 21:49:52";
        }
        //$fecha = "2017-08-19 10:49:52";

        $data["fechaPago"] = $fechaPago;

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


        $userId = $data["userId"];

        //build informacion nodo receptor
        if(!$data["fromNomina"])
        {
            $vs->setUserId($userId, 1);
            $nodoReceptor = $vs->GetUserInfo();

            $nodoReceptor["rfc"] = str_replace("&AMP;", "&", $nodoReceptor["rfc"]);

        }
        else
        {
            $usuario = new Usuario;
            $usuario->setUsuarioId($userId);
            $nodoReceptor = $usuario->InfoUsuario();

        }
        $data["nodoReceptor"] = $nodoReceptor;
        //checar si nos falta unidad en alguno
        foreach($_SESSION["conceptos"] as $concepto)
        {
            if($concepto["unidad"] == "")
            {
                $vs->Util()->setError(10048, "error", "El campo de Unidad no puede ser vacio");
            }
        }

        include_once(DOC_ROOT.'/services/Xml.php');

        $xml = new Xml;
        $xml->Generate($data, $totales, $_SESSION["conceptos"],$empresa);

        //despues de la generacion del xml, viene el timbrado.
        $nufa = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
        $rfcActivo = $this->getRfcActive();

        $root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/";

        $xmlFile = $root.$nufa.".xml";

        $cadenaOriginal = $xml->cadenaOriginal($xmlFile);
        $data["cadenaOriginal"] = utf8_encode($cadenaOriginal);
        $md5Cadena = utf8_decode($cadenaOriginal);

        $md5 = hash( 'sha256', $md5Cadena );

        $selloObject = new Sello;
        $sello = $selloObject->generar($cadenaOriginal, $md5);
        $data["sello"] = $sello["sello"];
        $data["certificado"] = $sello["certificado"];

        $xml = new Xml;
        $xml->Generate($data, $totales, $_SESSION["conceptos"],$empresa);

        //Timbrado (esto es para edicom)
        $nufa_dos = "SIGN_".$_SESSION["empresaId"]."_".$serie["serie"]."_".$data["folio"];
        $zipFile = $root.$nufa.".zip";
        @unlink($zipFile);
        $signedFile = $root.$nufa."_signed.zip";
        $timbradoFile = $root.$nufa_dos.".xml";

        $this->Util()->Zip($root, $nufa);

        $xmlDb = $nufa;
        //$signedFile = $root."SIGN_".$nufa.".xml";

        $user = USER_PAC;
        $pw = PW_PAC;
        $pac = new Pac;
        $response = $pac->GetCfdi($user, $pw, $zipFile, $root, $signedFile, $empresa["empresaId"]);

        if($response["fault"])
        {
            return false;
        }

        //$fileTimbreXml = $pac->GetTimbreCfdi($user, $pw, $zipFile, $root, $timbreFile);
        $timbreXml = $pac->ParseTimbre($timbradoFile);
/*
         * ESTO ES CON FINKOK
         *         if(is_array($response))
        {
            if($response["tipo"] == "error")
            {
                $vs->Util()->setError(10047, "error", utf8_encode($response["msg"]));
                if($vs->Util()->PrintErrors()){ return false; }
            }
        }

        $timbreXml = $pac->ParseTimbre($response, $data["sello"]);
*/
        $cadenaOriginalTimbre = $pac->GenerateCadenaOriginalTimbre($timbreXml);
        $cadenaOriginalTimbreSerialized = serialize($cadenaOriginalTimbre);

        $data["timbreFiscal"] = $cadenaOriginalTimbre;

        switch($data["metodoDePago"])
        {
            case "01": $data["metodoDePagoLetra"] = "Efectivo"; break;
            case "02": $data["metodoDePagoLetra"] = "Cheque"; break;
            case "03": $data["metodoDePagoLetra"] = "Transferencia"; break;
            case "04": $data["metodoDePagoLetra"] = "Tarjetas de Credito"; break;
            case "05": $data["metodoDePagoLetra"] = "Monederos electronicos"; break;
            case "06": $data["metodoDePagoLetra"] = "Dinero electronico"; break;
            case "08": $data["metodoDePagoLetra"] = "Vales de despensa"; break;
            case "28": $data["metodoDePagoLetra"] = "Tarjeta de Debito"; break;
            case "29": $data["metodoDePagoLetra"] = "Tarjeta de Servici"; break;
            case "99": $data["metodoDePagoLetra"] = "Otros"; break;
        }

        switch($empresa["empresaId"])
        {
            default:
                //include_once(DOC_ROOT."/classes/override_generate_pdf_default.php");
                $override = new Override;
                $pdf = $override->GeneratePDF($data, $serie, $totales, $nodoEmisor, $nodoReceptor, $_SESSION["conceptos"],$empresa);
        }
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
				`version`,
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
				'".$nufa."',
				'".$data["nodoEmisor"]["rfc"]["rfcId"]."',
				'".$totales["iva"]."',
				'".$myData."',
				'".$myConceptos."',
				'".$myImpuestos."',
				'".$data["cadenaOriginal"]."',
				'3.3',
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
        $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

        return $comprobanteId;
    }//Generar
}


?>