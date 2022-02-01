<?php

class Cfdi extends Comprobante
{
    public function Generar($data, $notaCredito = false)
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

        $sql ="select empresaId,rfcId from serie where tiposComprobanteId = '".$data['tiposComprobanteId']."' and serieId = '".$data['tiposSerieId']."' ";
        $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
        $currentRfc = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();
        $empresa['empresaId'] = $currentRfc['empresaId'];

        $vs = new User;

        include_once(DOC_ROOT."/addendas/addenda_campos.php");

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
            $vs->Util()->setError(10040, "error", "Debe agregar por lo menos un concepto");
        }

        if($data["fechaSobre"] != "")
        {
            $vs->Util()->ValidateString($data["fechaSobre"], 10, 10, "Fecha Factura");
        }

        if($data["folioSobre"] != "")
        {
            $vs->Util()->ValidateInteger($data["folioSobre"], 1000000000, 1);
        }

        // sustitucion de cfdis previos, encontrar los workflowId para su actualizacion de comprobanteId
        if ($data['cfdiRelacionadoSerie'] != "" && $data['cfdiRelacionadoFolio'] != "" && $data['tiposComprobanteId'] == "1") {
            $rfolio = $data['cfdiRelacionadoSerie'].$data['cfdiRelacionadoFolio'];
            $sql ="select comprobanteId, status from comprobante where lower(concat(serie,folio)) = '".strtolower($rfolio)."'";
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
            $cfdiRelacionado = $this->Util()->DBSelect($_SESSION['empresaId'])->GetRow();
            if ($cfdiRelacionado) {
                if ($cfdiRelacionado['status'] == '1') {
                    $vs->Util()->setError(10040, "error", "El CFDI relacionado debe estar cancelado: " . $rfolio);
                } else {
                    $data['cfdiRelacionadoId'] =  $cfdiRelacionado['comprobanteId'];
                    $sql ="select instanciaServicio.instanciaServicioId, servicio.inicioFactura, instanciaServicio.factura from instanciaServicio 
                           inner join servicio on instanciaServicio.servicioId=servicio.servicioId
                           where instanciaServicio.comprobanteId = '".$cfdiRelacionado['comprobanteId']."' ";
                    $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sql);
                    $affects = $this->Util()->DBSelect($_SESSION['empresaId'])->GetResult();
                    $data['workflowsIdUpdateInvoice'] = $affects;
                }
                if($data["tipoRelacion"] == "") {
                    $vs->Util()->setError(0, "error", "Seleccionar un tipo de relacion, para el cfdi relacionado");
                }
            } else {
                    $vs->Util()->setError(10040, "error", "El CFDI con folio ".$rfolio." no se encuentra registrado.");
            }

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
        $activeRfc = $currentRfc['rfcId'];
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
            $vs->Util()->setError(10047, "error","Tipo de comprobante no seleccionado");
        }
        if($vs->Util()->PrintErrors()){ return false; }

        if($notaCredito)
        {
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE tiposComprobanteId = '2' AND empresaId = ".$currentRfc["empresaId"]." AND rfcId = '".$activeRfc."' AND consecutivo <= folioFinal AND serieId = ".$data["tiposSerieId"]." ORDER BY serieId DESC LIMIT 1");
        }
        else
        {
            $sql = "SELECT * FROM serie WHERE tiposComprobanteId = ".$data["tiposComprobanteId"]." AND empresaId = ".$currentRfc["empresaId"]." AND rfcId = '".$activeRfc."' AND consecutivo <= folioFinal AND serieId = ".$data["tiposSerieId"]." ORDER BY serieId DESC LIMIT 1";

            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        }

        $serie = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$serie)
        {
            $vs->Util()->setError(10047, "error","Serie inhabilitada");
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

        $data["fechaPago"] = $fechaPago;

        //el tipo de comprobante lo determina tiposComprobanteId
        $tipoDeComprobante = $this->GetTipoComprobante($data["tiposComprobanteId"]);
        $data["comprobante"] = $this->InfoComprobante($data["tiposComprobanteId"]);

        //$fecha = "2017-10-12 19:57:09";
        $data["serie"] = $serie;
        $data["folio"] = $folio;
        $data["fecha"] = $fecha;
//		return;
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

        if($data['nodoEmisor']['rfc']['regimenFiscal'] == 'REGIMEN%20GENERAL%20DE%20LEY%20PERSONAS%20MORALES') {
            $data['nodoEmisor']['rfc']['regimenFiscal'] = '601';
        }

        $userId = $data["userId"];

        if($data['amortizacionFiniquitoSubtotal'] > 0 ||  $data['amortizacionFiniquitoIva'] > 0 || $data['amortizacion'] > 0 || $data['amortizacionIva'] > 0){
            $_SESSION['amortizacion']['amortizacionFiniquito'] = $data['amortizacionFiniquito'];
            $_SESSION['amortizacion']['amortizacionFiniquitoSubtotal'] = $data['amortizacionFiniquitoSubtotal'];
            $_SESSION['amortizacion']['amortizacionFiniquitoIva'] = $data['amortizacionFiniquitoIva'];
            $_SESSION['amortizacion']['amortizacion'] = $data['amortizacion'];
            $_SESSION['amortizacion']['amortizacionIva'] = $data['amortizacionIva'];
        } else {
            unset($_SESSION['amortizacion']);
        }

        include_once(DOC_ROOT.'/services/Xml.php');
        $xml = new Xml($data);

        //TODO might move to constructor
        if(!$xml->isNomina()){
            $vs->setUserId($userId, 1);
            $nodoReceptor = $vs->GetUserForInvoice($userId);
            $nodoReceptor["rfc"] = str_replace("&AMP;", "&", $nodoReceptor["rfc"]);
        } else {
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
        //workaround para mostrar observaciones en la vista previa
        $_SESSION['observaciones'] = $data["observaciones"];

        if($data["reviso"]){
            $_SESSION['firmas']['reviso'] = [
                "nombre" => 'REVISO',
                "valor" => urldecode($data["reviso"])
            ];
        } else {
            unset($_SESSION['firmas']['reviso']);
        }

        if($data["autorizo"]){
            $_SESSION['firmas']['autorizo'] = [
                "nombre" => 'AUTORIZO',
                "valor" => urldecode($data["autorizo"])
            ];
        } else {
            unset($_SESSION['firmas']['autorizo']);
        }

        if($data["recibio"]){
            $_SESSION['firmas']['recibio'] = [
                "nombre" => 'RECIBIO',
                "valor" => urldecode($data["recibio"])
            ];
        } else {
            unset($_SESSION['firmas']['recibio']);
        }

        if($data["vobo"]){
            $_SESSION['firmas']['vobo'] = [
                "nombre" => 'VoBo',
                "valor" => urldecode($data["vobo"])
            ];
        } else {
            unset($_SESSION['firmas']['vobo']);
        }

        if($data["pago"]){
            $_SESSION['firmas']['pago'] = [
                "nombre" => 'PAGO',
                "valor" => urldecode($data["pago"])
            ];
        } else {
            unset($_SESSION['firmas']['pago']);
        }

        //XML sin sello
        $xml = new Xml($data);

        $xml->Generate($totales, $_SESSION["conceptos"],$empresa);
        //XML con sello
        $this->setRfcId($currentRfc['rfcId']);
        $xmlConSello = $this->stamp($empresa, $serie, $data, $xml, $totales);

        if($data['format'] == 'vistaPrevia'){
            return $xmlConSello;
        }
        //Timbrado PAC
        include_once(DOC_ROOT."/services/Pac.php");
        $pac = new Pac33;

        $response = $pac->GetCfdi($xmlConSello);//sucede error de retenciones

        $_SESSION['errorPac'] = '';
        if($response['worked'] == false)
        {
            $_SESSION['errorPac'] = utf8_encode($response["response"]['faultstring']);
            $vs->Util()->setError(10047, "error", utf8_encode($response["response"]['faultstring']));
            if($vs->Util()->PrintErrors()){ return false; }
        }

        $timbreXml = $pac->ParseTimbre($xmlConSello['xmlSignedFile'], $data["sello"]);

        $cadenaOriginalTimbre = $pac->GenerateCadenaOriginalTimbre($timbreXml);
        $cadenaOriginalTimbreSerialized = serialize($cadenaOriginalTimbre);

        if($_SESSION["impuestos"] || $_SESSION["firmas"] || $_SESSION["amortizacion"]){

            include_once(DOC_ROOT."/services/Addendas/Impuestos.php");
            $impuestos = new Impuestos();
            $impuestos->generar($xmlConSello, $_SESSION["impuestos"], $_SESSION['firmas'], $_SESSION['amortizacion']);
        }

        $data["timbreFiscal"] = $cadenaOriginalTimbre;

        //cambios 29 junio 2011
        //insert new comprobante
        switch($data["tiposDeMoneda"])
        {
            case "MXN": $data["tiposDeMoneda"] = "peso"; break;
            case "USD": $data["tiposDeMoneda"] = "dolar"; break;
            case "EUR": $data["tiposDeMoneda"] = "euro"; break;
        }
        //comprobar de donde procede la factura
        switch($data['procedencia']){
            case 'whithInstance':
                $data['procedencia'] = 'fromInstance';
            break;
            case 'rifWhithoutInstance':
                $data['procedencia'] = 'fromRifNoInstance';
            break;
            default:
                $data['procedencia'] = 'manual';
            break;
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
				`timbreFiscal`,
				`procedencia`,
				`servicioId`,				
				`version`
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
				'".$xmlConSello['fileName']."',
				'".$data["nodoEmisor"]["rfc"]["rfcId"]."',
				'".$totales["iva"]."',
				'".$myData."',
				'".$myConceptos."',
				'".$myImpuestos."',
				'".$data["cadenaOriginal"]."',
				'".$cadenaOriginalTimbreSerialized."',
				'".$data['procedencia']."',
				'".$data['servicioId']."',
				'3.3'
			)");
        $this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT comprobanteId FROM comprobante ORDER BY comprobanteId DESC LIMIT 1");
        $comprobanteId = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();

        //finally we update the 'consecutivo
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("UPDATE serie SET consecutivo = consecutivo + 1 WHERE serieId = ".$serie["serieId"]);
        $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

        if (count ($data['workflowsIdUpdateInvoice']) > 0) {
           foreach($data['workflowsIdUpdateInvoice'] as $wupdate) {
               $idCompUpdate =  $wupdate['factura'] == 'Si' ? $comprobanteId : 0;
               $sqlCompUpdate =  "UPDATE instanciaServicio SET comprobanteId = '".$idCompUpdate."' where instanciaServicioId='".$wupdate['instanciaServicioId']."' ";
               $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlCompUpdate);
               $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
           }
        }

        if(!isset($data['notaVentaId']) && !isset($_SESSION['ticketsId']) && (!$xml->isPago() && !$xml->isNomina()))
        {
            //insert conceptos
            foreach($_SESSION["conceptos"] as $concepto)
            {
                $idServicio = $concepto['servicioId'] ?? 0;
                $fechaCorrespondiente = isset($concepto['fechaCorrespondiente'])
                                        ? "'".$concepto['fechaCorrespondiente']."'" : 'NULL';

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
						`empresaId`,
						`servicioId`,
						`fechaCorrespondiente`
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
						".$empresa["empresaId"].",
						'".$idServicio."',
						 $fechaCorrespondiente
						)");
                $this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
            }

        }
        //End notaVenta
        //Enviar por correo despues de crear factura.
        $razon =  new Razon;
        $razon->sendComprobante33($comprobanteId,false,true);
        return $comprobanteId;
    }//Generar

    private function stamp($empresa, $serie, $data, $xml, $totales){
        //despues de la generacion del xml, viene el timbrado.
        $fileName = $empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
        $rfcActivo = $this->getRfcId();
        $root = DOC_ROOT."/empresas/".$empresa["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/";

        $xmlFile = $root.$fileName.".xml";

        $cadenaOriginal = $xml->cadenaOriginal($xmlFile);
        //TODO acordarse que se le quito el utf8 decode
        $data["cadenaOriginal"] = $cadenaOriginal;
        $md5Cadena = $cadenaOriginal;

        $md5 = hash( 'sha256', $md5Cadena );

        $selloObject = new Sello;
        $selloObject->setRfcId($this->getRfcId());
        $selloObject->setEmpresaId($empresa['empresaId']);
;       $sello = $selloObject->generar($cadenaOriginal, $md5);
        $data["sello"] = $sello["sello"];
        $data["certificado"] = $sello["certificado"];

        $xml = new Xml($data);
        $xml->Generate($totales, $_SESSION["conceptos"],$empresa);

        $response['fileName'] = $fileName;
        $response['fileNamePreview'] = $empresa["empresaId"]."_certificados_".$rfcActivo."_facturas_xml";
        $response['root'] = $root;
        $response['xmlFile'] = $root.$fileName.".xml";
        $response['zipFile'] = $root.$fileName.".zip";
        $response['zipSignedFile'] = $root.$fileName."_signed.zip";

        $response['xmlSignedFile'] = $root."SIGN_".$fileName.".xml";
        return $response;
    }
}


?>
