<?php
class Xml extends Producto{

    public $data;
    private $totales;
    private $tipoDeCambio;

    private $horasExtraImporte;
    private $totalPercepciones;
    private $totalDeducciones;
    private $totalOtrosPagos;

    private $miEmpresa;
    private $nodosConceptos;
    //TODO move the xml to an object to make it global
    private $xml;
    private $root;
    private $cfdisRelacionados;
    private $emisor;
    private $receptor;
    private $totalImpuestosTrasladados = 0;
    private $totalImpuestosRetenidos = 0;
    private $totalImpuestosLocales = 0;

    private $trasladosGlobales;
    private $retencionesGlobales;

    private $tipoComprobante;

    private $complementos;
    private $impuestosLocales;
    private $impuestoLocal;
    private $myComplementoNomina;
    private $complementoDonataria;

    private $pagos;

    private $cfdiUtil;
    private $comprobantePago;
    private $uuidRelacionado;

    public function __construct($data)
    {
        $this->cfdiUtil = new CfdiUtil();
        $this->comprobantePago = new ComprobantePago();

        $this->data = $data;

        $this->setTipoComprobante();
    }

    public function CadenaOriginal($xmlFile) {
        $xslFile = DOC_ROOT."/xslt/cadenaoriginal_3_3.xslt";
        $xsl = new DOMDocument();
        $xsl->load($xslFile);

        $proc = new XSLTProcessor;
        $proc->importStyleSheet($xsl);

        $xml = new DOMDocument("1.0","UTF-8");
        $xml->load($xmlFile);
        $cadenaOriginal = $proc->transformToXML($xml);

        $cadenaOriginal = trim($cadenaOriginal);
        $cadenaOriginal = str_replace("\n        |", "|", $cadenaOriginal);

        return $cadenaOriginal;
    }

    function Generate($totales, $nodosConceptos, $empresa)
    {
        $this->totales = $totales;

        $this->miEmpresa = $this->Info();

        $this->nodosConceptos = $nodosConceptos;

        $this->xml = new DOMdocument("1.0","UTF-8");

        $this->formatDate();

        $this->getUUIDRelacionado();

        $this->buildNodoRoot();

        $this->buildNodoCfdisRelacionados();

        $this->buildNodoEmisor();

        $this->buildNodoReceptor();

        $this->buildNodoConceptos();

        $this->calcularImpuestosTrasladados();

        $this->calcularImpuestosRetenidos();

        $this->buildNodoImpuestos();

        $this->buildNodoComplementos();

        $this->buildNodoPagos();

        $this->CargaAtt($this->root, $this->buildRootData());

        if($this->isPago()) {
            $xsd = 'http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd';
        }

        if($this->isNomina()){
            $xsd = "http://www.sat.gob.mx/nomina http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina12.xsd";

            $this->root->setAttribute('xmlns:nomina12', "http://www.sat.gob.mx/nomina12");
            $this->root->setAttribute('xmlns:catNomina', "http://www.sat.gob.mx/sitio_internet/cfd/catalogos/Nomina");
            $this->root->setAttribute('xmlns:tdCFDI', "http://www.sat.gob.mx/sitio_internet/cfd/tipoDatos/tdCFDI");
            $this->root->setAttribute('xmlns:catCFDI', "http://www.sat.gob.mx/sitio_internet/cfd/catalogos");
        }

        if($this->isDonataria()){
            $xsd = "http://www.sat.gob.mx/donat http://www.sat.gob.mx/sitio_internet/cfd/donat/donat11.xsd";
        }

        if($this->totales['porcentajeISH'] > 0){
            $xsd = "http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd";
            $this->root->setAttribute('xmlns:implocal', "http://www.sat.gob.mx/implocal");
        }

        if(count($_SESSION["impuestos"]) > 0) {
            $xsd = "http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd";
            $this->root->setAttribute('xmlns:implocal', "http://www.sat.gob.mx/implocal");
        }

        $this->buildXsd($xsd);

//        print_r($this->save());

        return $this->save();
    }

    private function buildXsd($xsd = null){
        $this->root->setAttribute("xsi:schemaLocation", "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd ".$xsd);
    }

    private function getUUIDRelacionado(){
        $this->uuidRelacionado = $this->cfdiUtil->getUUID($this->data['cfdiRelacionadoSerie'], $this->data['cfdiRelacionadoFolio']);
    }

    private function CargaAtt(&$nodo, $attr)
    {
        foreach ($attr as $key => $val)
        {
            if (strlen($val)>0)
            {
                $nodo->setAttribute($key,$val);
            }
        }
    }

    private function fromNominaChanges() {
        $this->tipoDeCambio = $this->Util()->CadenaOriginalVariableFormat($this->data["tipoDeCambio"], false,false, false, false, true);

        $this->horasExtraImporte = 0;
        if(count($_SESSION["horasExtras"]) > 0)
        {
            foreach($_SESSION["horasExtras"] as $myHoraExtra)
            {
                $this->horasExtraImporte += $myHoraExtra["importePagado"];
            }
        }

        $this->totalPercepciones = $_SESSION["conceptos"]["1"]["percepciones"]["totalGravado"] +
            $_SESSION["conceptos"]["1"]["percepciones"]["totalExcento"];

        $this->totalDeducciones = $_SESSION["conceptos"]["1"]["deducciones"]["totalGravado"] +
            $_SESSION["conceptos"]["1"]["deducciones"]["totalExcento"] +
            $_SESSION["conceptos"]["1"]["incapacidades"]["total"];

        $this->totalOtrosPagos = 0;
        foreach($_SESSION["otrosPagos"] as $key => $value)
        {
            $this->totalOtrosPagos += $value["importe"];
        }

        $this->totales["subtotal"] = $this->totalPercepciones + $this->totalOtrosPagos + $this->horasExtraImporte;
        $this->totales["descuento"] = $this->totalDeducciones;
        $this->totales["total"] = $this->totales["subtotal"] - $this->totales["descuento"] ;
    }

    private function buildNodoCfdisRelacionados() {

        if(!$this->uuidRelacionado) {
            return;
        }

        $this->cfdisRelacionados = $this->xml->createElement("cfdi:CfdiRelacionados");
        $this->cfdisRelacionados = $this->root->appendChild($this->cfdisRelacionados);

        $cfdiRelacionadosData = array(
            "TipoRelacion"=>$this->Util()->CadenaOriginalVariableFormat($this->data['tipoRelacion'],false,false),
        );
        $this->CargaAtt($this->cfdisRelacionados, $cfdiRelacionadosData);

        $cfdiRelacionado = $this->xml->createElement("cfdi:CfdiRelacionado");
        $cfdiRelacionado = $this->cfdisRelacionados->appendChild($cfdiRelacionado);

        $cfdiRelacionadoData = array(
            "UUID"=>$this->Util()->CadenaOriginalVariableFormat($this->uuidRelacionado,false,false),
        );

        $this->CargaAtt($cfdiRelacionado, $cfdiRelacionadoData);
        //Para comprobante tipo P el tipo debe de ser 04 (si existen errores)
        //CFDIs relacionados 0, 1 o mas (probablemente lo limitare a uno)
        //CFDI relacionado
        //TipoRelacion
        //01 o 02, no pueden ser registradas tipos T, P o N
        //03, no se pueden registar tipos E, P o N
        //04 si es del tipo I o E puede sustituir a un comprobante tipo I o E de lo contrario debe de ser
        //del mismo tipo
        //05, debe de ser del tipo T y el comprobante relacionado debe ser del tipo I o E
        //06, debe de ser del tipo I o E, y el comprobante relacionado debe de ser del tipo Y
        //UUID

    }

    private function buildNodoRoot() {

        $this->root = $this->xml->createElement("cfdi:Comprobante");
        $this->root = $this->xml->appendChild($this->root);

        $this->root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd");
        $this->root->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/3");
        $this->root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");

        if($this->totales['porcentajeISH'] > 0){
            $this->root->setAttribute("xmlns:implocal", "http://www.sat.gob.mx/implocal");
        }

        //recibo de donataria
        if($this->isDonataria()) {
            $this->root->setAttribute("xmlns:implocal", "http://www.sat.gob.mx/donat");
        }

        if($this->isPago()) {
            $this->root->setAttribute("xmlns:pago10", "http://www.sat.gob.mx/Pagos");
        }

        $this->tipoDeCambio = $this->Util()->CadenaOriginalVariableFormat($this->data["tipoDeCambio"], true,false, true);
        if($this->isNomina()) {
            $this->fromNominaChanges();
        }


    }

    public function setTipoComprobante() {
        $this->tipoComprobante = strtoupper(substr($this->data["tipoDeComprobante"],0,1));
    }

    private function isTraspaso() {
        return $this->tipoComprobante == 'T';
    }

    public function isPago() {
        return $this->tipoComprobante == 'P';
    }

    private function isIngreso() {
        return $this->tipoComprobante == 'I';
    }

    private function isEgreso() {
        return $this->tipoComprobante == 'E';
    }

    public function isNomina() {
        return $this->tipoComprobante == 'N';
    }

    public function isDonataria() {
        return $this->data['comprobante']["tiposComprobanteId"] == 9;
    }

    private function buildRootData() {

        $rootData["Version"] = "3.3";
        $rootData["Serie"] = $this->Util()->CadenaOriginalVariableFormat($this->data["serie"]["serie"],false,false);
        $rootData["Folio"] = $this->Util()->CadenaOriginalVariableFormat($this->data["folio"],false,false);
        $rootData["Fecha"] = $this->Util()->CadenaOriginalVariableFormat($this->data["fecha"],false,false);
        $rootData["Sello"] = $this->data["sello"];

        //TODO viene del catalogo de formas de pago
        if(!$this->isPago()) {
            $rootData["FormaPago"] = $this->Util()->CadenaOriginalVariableFormat($this->data["formaDePago"],false,false);
        }

        $rootData["NoCertificado"] = $this->Util()->CadenaOriginalVariableFormat($this->data["serie"]["noCertificado"],false,false);

        $rootData["Certificado"] = $this->data["certificado"];

        if($this->isIngreso() || $this->isEgreso()){
            $rootData["CondicionesDePago"] = $this->Util()->CadenaOriginalVariableFormat($this->data["condicionesDePago"],false,false);
        }

        $totalRetenciones = 0;
        if(count($_SESSION["impuestos"]) > 0) {
            foreach($_SESSION["impuestos"] as $key => $impuesto) {

                if(!isset($impuesto["parent"])) {
                    continue;
                }
                if($impuesto["tasaIva"] > 0){
                    $tasa = $impuesto["tasaIva"] / 100;
                    $impuesto['importe'] = $impuesto['importe'] * (1 + $tasa);
                }
                $totalRetenciones += $impuesto['importe'];
            }
            $this->totales["subtotal"] = $this->totales["subtotal"] + $totalRetenciones;
        }

        $rootData["SubTotal"] = $this->Util()->CadenaOriginalVariableFormat($this->totales["subtotal"],true,false);
        if($this->isTraspaso() || $this->isPago()){
            $rootData["SubTotal"] = 0;
        }

        //Calcular descuento
        $descuento = $this->calcularDescuentos();
        if($descuento > 0){
            $rootData["Descuento"] = $this->Util()->CadenaOriginalVariableFormat($descuento, true,false);
        }

        $rootData["Moneda"] = $this->Util()->CadenaOriginalVariableFormat($this->data["tiposDeMoneda"], false,false);

        $decimals = 2;
        if($this->data["tiposDeMoneda"] == "MXN") {
            $decimals = 0;
        }


        //TODO algo raro con el % de asignacion, no me preocupare de ello por ahora, checar si
        //puede aplicar cuando sea MXN
        if(!$this->isPago() && !$this->isNomina()){
            $rootData["TipoCambio"] =  $this->Util()->CadenaOriginalFormat($this->tipoDeCambio, $decimals,false);;
        }

        $total = $this->totales["subtotal"] - $descuento + $this->totalImpuestosTrasladados - $this->totalImpuestosRetenidos + $this->totalImpuestosLocales;
        //Si el campo es del tipo T o P debe de ser 0
        $rootData["Total"] = $this->Util()->CadenaOriginalVariableFormat($total, true,false);

        if($this->isPago() || $this->isTraspaso()){
            $rootData["Total"] = 0;
        }

        $rootData["TipoDeComprobante"] = $this->tipoComprobante;

        //TODO viene de catalogo metodo de pago
        //Revisar tipo de pago PPD, es posible que no lo soportemos por ahora.
        if(!$this->isPago()){
            $rootData["MetodoPago"] = $this->Util()->CadenaOriginalVariableFormat($this->data["metodoDePago"],false,false);
        }

        $rootData["LugarExpedicion"] = $this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["cp"],false,false);

        //TODO confirmacion, pendiente por ahora, posiblemente no se soporte
        //$rootData["Confirmacion"] = $this->Util()->CadenaOriginalVariableFormat($this->data["NumCtaPago"],false,false);

        //TODO No debe existir el nodo impuestos si el tipo es T, P o N

        return $rootData;
    }

    private function calcularDescuentos() {
        if($this->isNomina()){
            return $this->totalDeducciones;
        }

        if($this->isTraspaso() || $this->isPago() || $this->data["porcentajeDescuento"] <= 0){
            return 0;
        }

        $descuentoTotal = 0;

        foreach($this->nodosConceptos as $key => $concepto) {
            $descuento = $this->Util()->CadenaOriginalVariableFormat($concepto["descuento"], true, false);
            $this->nodosConceptos[$key]["descuento"] = $descuento;
            $descuentoTotal += $descuento;
        }

        return $descuentoTotal;
    }

    private function calcularImpuestosTrasladados() {
        if(count($this->trasladosGlobales ) == 0) {
            return;
        }

        foreach($this->trasladosGlobales as $keyImpuesto => $impuesto ) {
            foreach($impuesto as $keyTasa => $data) {
                $this->totalImpuestosTrasladados += $this->Util()->CadenaOriginalVariableFormat($data['importe'],true,false);
            }
        }
    }

    private function calcularImpuestosRetenidos() {
        if(count($this->retencionesGlobales ) == 0) {
            return;
        }

        foreach($this->retencionesGlobales as $keyImpuesto => $impuesto ) {
            foreach($impuesto as $keyTasa => $importe) {
                $this->totalImpuestosRetenidos += $this->Util()->CadenaOriginalVariableFormat($importe,true,false);
            }
        }
    }

    private function buildNodoEmisor() {
        $this->emisor = $this->xml->createElement("cfdi:Emisor");
        $this->emisor = $this->root->appendChild($this->emisor);

        $emisorData = array(
            "Rfc"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["rfc"],false,false),
            "Nombre"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["razonSocial"],false,false),
            "RegimenFiscal"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["regimenFiscal"],false,false)
        );

        $this->CargaAtt($this->emisor, $emisorData);

    }

    private function buildNodoReceptor() {
        $this->receptor = $this->xml->createElement("cfdi:Receptor");
        $this->receptor = $this->root->appendChild($this->receptor);

        $receptorData = array(
            "Rfc"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["rfc"],false,false),
            "Nombre"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["nombre"],false,false),
            //TODO Residencia fiscal, viene del catalogo c_Pais, obligatorio para rfcs extranjeros
            //NumRegIdTrib, obligatorio con complemento de comercio exterior (no se agregara ya que no lo soportamos)
            //solo para extranjeros
            "UsoCFDI"=>$this->Util()->CadenaOriginalVariableFormat($this->data["usoCfdi"],false,false)
        );

        $this->CargaAtt($this->receptor, $receptorData);
    }

    private function formatDate() {
        //$this->data["fecha"] = "2017-05-20T10:45:09";
        $this->data["fecha"] = explode(" ", $this->data["fecha"]);
        $this->data["fecha"] = $this->data["fecha"][0]."T".$this->data["fecha"][1];
    }

    private function buildNodoConceptos() {
        $conceptos = $this->xml->createElement("cfdi:Conceptos");
        $conceptos = $this->root->appendChild($conceptos);


        foreach($this->nodosConceptos as $concepto)
        {
            $myConcepto = $this->xml->createElement("cfdi:Concepto");

            if($this->isNomina()) {
                $cantidad = $this->Util()->CadenaOriginalVariableFormat($concepto["cantidad"],false,false,false,false,true);
                $concepto["unidad"] = "";
                $concepto["descripcion"] = "Pago de nómina";
                $concepto["valorUnitario"] = $this->totales["subtotal"];
                $concepto["importe"] = $this->totales["subtotal"];
                $concepto["claveProdServ"] = '84111505';
                $concepto["claveUnidad"] = 'ACT';
                $concepto["descuento"] = $this->totalDeducciones;
            } else {
                $cantidad = $this->Util()->CadenaOriginalVariableFormat($concepto["cantidad"],true,false);
            }

            if($this->isPago()){
                $cantidad = $this->Util()->CadenaOriginalFormat($concepto["cantidad"],0,false);
            }

            $conceptoData = array(
                "ClaveProdServ"=>$this->Util()->CadenaOriginalVariableFormat($concepto["claveProdServ"],false,false),
            );

            if(!$this->isPago()) {
                $conceptoData["NoIdentificacion"] = $this->Util()->CadenaOriginalVariableFormat($concepto["noIdentificacion"],false,false);
            }

            $conceptoData["Cantidad"] = $cantidad;
            $conceptoData["ClaveUnidad"] = $this->Util()->CadenaOriginalVariableFormat($concepto["claveUnidad"],false,false);

            if(!$this->isPago()) {
                $conceptoData["Unidad"] = $this->Util()->CadenaOriginalVariableFormat($concepto["unidad"],false,false);
            }

            $conceptoData["Descripcion"] = $concepto["descripcion"];

            if($this->isPago()) {
                $conceptoData["ValorUnitario"] = $this->Util()->CadenaOriginalFormat($concepto["valorUnitario"], 0, false);
            } else{
                $conceptoData["ValorUnitario"] = $this->Util()->CadenaOriginalVariableFormat($concepto["valorUnitario"],true,false);
            }

            if($this->isPago()) {
                $conceptoData["Importe"] = $this->Util()->CadenaOriginalFormat($concepto["importe"], 0, false);
            } else{
                $conceptoData["Importe"] = $this->Util()->CadenaOriginalVariableFormat($concepto["importe"],true,false);
            }

            if($concepto["descuento"] > 0) {
                $conceptoData["Descuento"] = $this->Util()->CadenaOriginalVariableFormat($concepto["descuento"],true,false);
            }

            $myConcepto = $conceptos->appendChild($myConcepto);
            $this->CargaAtt($myConcepto, $conceptoData);

            if(!$this->isPago() && !$this->isNomina()) {
                //Si alguno de los impuestos o retenciones existe, este nodo debe existir sino no
                if($concepto["totalIva"] + $concepto["totalIeps"] > 0 || ($this->totales["retIva"] + $this->totales["retIsr"] > 0)) {
                    $impuestosConcepto = $this->xml->createElement("cfdi:Impuestos");
                    $impuestosConcepto = $myConcepto->appendChild($impuestosConcepto);
                }

                //Si alguno de los impuestos existe el siguiente nodo debe de existir
                if($concepto["totalIva"] > 0 || $concepto["porcentajeIeps"] > 0) {
                    $trasladosConcepto = $this->xml->createElement("cfdi:Traslados");
                    $trasladosConcepto = $impuestosConcepto->appendChild($trasladosConcepto);

                    //si esta exento de iva no debemos de agregar el nodo
                    if($concepto["totalIva"] > 0) {
                        $trasladoConcepto = $this->xml->createElement("cfdi:Traslado");
                        $trasladoConcepto = $trasladosConcepto->appendChild($trasladoConcepto);

                        $tasa = $concepto["tasaIva"] / 100;

                        //Recalculamos importe e iva si tenemos impuesots, estoy es un workaround para no cambiar el desglose
                        if($_SESSION['impuestos']){
                            $concepto["importeTotal"] = $concepto['importe'] - $concepto['descuento'];
                            $concepto["totalIva"] = $concepto["importeTotal"] * $tasa;
                        }

                        $this->CargaAtt($trasladoConcepto, array(
                                "Base" => $this->Util()->CadenaOriginalVariableFormat($concepto["importeTotal"],true,false),
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat("002",false,false),
                                "TipoFactor" => $this->Util()->CadenaOriginalVariableFormat("Tasa",false,false),
                                "TasaOCuota" => $this->Util()->CadenaOriginalFormat($tasa,6,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($concepto["totalIva"],true,false)
                            )
                        );

                        //construye nodo impuestos globales
                        $this->trasladosGlobales['002'][(string)$tasa]["importe"] +=  $this->Util()->CadenaOriginalVariableFormat($concepto["totalIva"],true,false);
                        $this->trasladosGlobales['002'][(string)$tasa]["tasaOCuota"] =  'Tasa';
                    }

                    if($concepto["totalIeps"] > 0) {
                        $trasladoConcepto = $this->xml->createElement("cfdi:Traslado");
                        $trasladoConcepto = $trasladosConcepto->appendChild($trasladoConcepto);

                        $tasaIeps = $concepto["porcentajeIeps"] / 100;

                        $this->CargaAtt($trasladoConcepto, array(
                                "Base" => $this->Util()->CadenaOriginalVariableFormat($concepto["importeTotal"],true,false),
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat("003",false,false),
                                "TipoFactor" => $this->Util()->CadenaOriginalVariableFormat($concepto["iepsTasaOCuota"],false,false),
                                "TasaOCuota" => $this->Util()->CadenaOriginalFormat($tasaIeps,6,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($concepto["totalIeps"],true,false)
                            )
                        );

                        //construye nodo impuestos globales
                        $this->trasladosGlobales['003'][(string)$tasaIeps]["importe"] += $this->Util()->CadenaOriginalVariableFormat($concepto["totalIeps"],true,false);
                        $this->trasladosGlobales['003'][(string)$tasaIeps]["tasaOCuota"] = $concepto["iepsTasaOCuota"];
                    }
                }

                if($this->totales["retIva"] + $this->totales["retIsr"] > 0) {

                    $retencionesConcepto = $this->xml->createElement("cfdi:Retenciones");
                    $retencionesConcepto = $impuestosConcepto->appendChild($retencionesConcepto);

                    if($this->totales["retIva"] > 0 && $concepto["tasaIva"] > 0) {
                        $retencionConcepto = $this->xml->createElement("cfdi:Retencion");
                        $retencionConcepto = $retencionesConcepto->appendChild($retencionConcepto);

                        $tasa = $this->totales["porcentajeRetIva"] / 100;

                        $this->CargaAtt($retencionConcepto, array(
                                "Base" => $this->Util()->CadenaOriginalVariableFormat($concepto["importeTotal"],true,false),
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat("002",false,false),
                                "TipoFactor" => $this->Util()->CadenaOriginalVariableFormat("Tasa",false,false),
                                "TasaOCuota" => $this->Util()->CadenaOriginalFormat($tasa,6,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($concepto["totalRetencionIva"],true,false)
                            )
                        );
                        //construye nodo impuestos globales
                        $this->retencionesGlobales['002'][(string)$tasa] +=  $this->Util()->CadenaOriginalVariableFormat($concepto["totalRetencionIva"],true,false);
                    }

                    if($this->totales["retIsr"] > 0) {
                        $retencionConcepto = $this->xml->createElement("cfdi:Retencion");
                        $retencionConcepto = $retencionesConcepto->appendChild($retencionConcepto);

                        $tasa = $this->totales["porcentajeRetIsr"] / 100;

                        $this->CargaAtt($retencionConcepto, array(
                                "Base" => $this->Util()->CadenaOriginalVariableFormat($concepto["importeTotal"],true,false),
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat("001",false,false),
                                "TipoFactor" => $this->Util()->CadenaOriginalVariableFormat("Tasa",false,false),
                                "TasaOCuota" => $this->Util()->CadenaOriginalFormat($tasa,6,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($concepto["totalRetencionIsr"],true,false)
                            )
                        );

                        //construye nodo impuestos globales
                        $this->retencionesGlobales['001'][(string)$tasa] +=  $this->Util()->CadenaOriginalVariableFormat($concepto["totalRetencionIsr"],true,false);
                    }
                }
            }

            if(strlen($concepto["cuentaPredial"]) > 0)
            {
                $cuentaPredial = $this->xml->createElement("cfdi:CuentaPredial");
                $cuentaPredial = $myConcepto->appendChild($cuentaPredial);
                $this->CargaAtt($cuentaPredial, array(
                        "Numero"=>$this->Util()->CadenaOriginalVariableFormat($concepto["cuentaPredial"],false,false),
                    )
                );
            }
        }
    }

    private function buildNodoImpuestos() {
        //TODO return si no hay impuestos
        if($this->isPago() || $this->isNomina()) {
            return;
        }

        $impuestos = $this->xml->createElement("cfdi:Impuestos");
        $impuestos = $this->root->appendChild($impuestos);

        if(!$this->isNomina())
        {
            //impuestos version inicial.
            /*$this->CargaAtt($impuestos, array(
                    "TotalImpuestosRetenidos" => $this->Util()->CadenaOriginalVariableFormat($this->totalImpuestosRetenidos,true,false),
                    "TotalImpuestosTrasladados" => $this->Util()->CadenaOriginalVariableFormat($this->totalImpuestosTrasladados, true, false))
            );*/
            // se separaron los impuestos por que en test retencionesGlobales causaba problemas. comprobaba la suma de los hijos y no existian
            if(count($this->retencionesGlobales) > 0)
            {
                $this->CargaAtt($impuestos, array(
                        "TotalImpuestosRetenidos" => $this->Util()->CadenaOriginalVariableFormat($this->totalImpuestosRetenidos,true,false))
                );
            }
            if(count($this->trasladosGlobales) > 0) {
                $this->CargaAtt($impuestos, array(
                        "TotalImpuestosTrasladados" => $this->Util()->CadenaOriginalVariableFormat($this->totalImpuestosTrasladados, true, false))
                );
            }

            if(count($this->retencionesGlobales) > 0) {
                $retenciones = $this->xml->createElement("cfdi:Retenciones");
                $retenciones = $impuestos->appendChild($retenciones);

                foreach($this->retencionesGlobales as $keyImpuesto => $impuesto ) {
                    foreach($impuesto as $keyTasa => $importe) {
                        $retencion = $this->xml->createElement("cfdi:Retencion");
                        $retencion = $retenciones->appendChild($retencion);

                        $this->CargaAtt($retencion, array(
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat($keyImpuesto,false,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($importe,true,false))
                        );
                    }
                }
            }

            if(count($this->trasladosGlobales) > 0) {
                $traslados = $this->xml->createElement("cfdi:Traslados");
                $traslados = $impuestos->appendChild($traslados);

                foreach($this->trasladosGlobales as $keyImpuesto => $impuesto ) {
                    foreach($impuesto as $keyTasa => $data) {
                        $traslado = $this->xml->createElement("cfdi:Traslado");
                        $traslado = $traslados->appendChild($traslado);

                        $this->CargaAtt($traslado, array(
                                "Impuesto" => $this->Util()->CadenaOriginalVariableFormat($keyImpuesto,false,false),
                                "TipoFactor" => $this->Util()->CadenaOriginalVariableFormat($data['tasaOCuota'],false,false),
                                "TasaOCuota" => $this->Util()->CadenaOriginalFormat($keyTasa,6,false),
                                "Importe" => $this->Util()->CadenaOriginalVariableFormat($data['importe'],true,false)
                            )
                        );
                    }
                }
            }
        }
    }

    private function buildNodoPagos() {

        if(!$this->isPago()) {
            return;
        }

        $this->complementos = $this->xml->createElement("cfdi:Complemento");
        $this->complementos = $this->root->appendChild($this->complementos);

        $this->pagos = $this->xml->createElement("pago10:Pagos");
        $this->pagos = $this->complementos->appendChild($this->pagos);

        $this->CargaAtt($this->pagos, array(
                "Version" => '1.0',
            )
        );

        $pago = $this->xml->createElement("pago10:Pago");
        $pago = $this->pagos->appendChild($pago);

        $fechaPago = $this->data['infoPago']->fecha.'T12:00:00';

        switch($this->data['infoPago']->metodoPago) {
            case "Efectivo": $metodoPago = "01";break;
            case "Deposito": $metodoPago = "04";break;
            case "Transferencia": $metodoPago = "03";break;
            case "Cheque": $metodoPago = "02";break;
            default: $metodoPago = "99";break;
        }

        switch($this->data['tiposDeMonedaPago']) {
            case "peso": $tipoDeMoneda = "MXN"; break;
            case "dolar": $tipoDeMoneda = "USD"; break;
            case "euro": $tipoDeMoneda = "EUR"; break;
        }

        $pagoData = [
            "FechaPago" => $fechaPago,
            "FormaDePagoP" => $metodoPago,
            "MonedaP" => $tipoDeMoneda,
            "Monto" => $this->Util()->CadenaOriginalFormat($this->data['infoPago']->amount,2,false),
            "NumOperacion" => $this->data['infoPago']->operacion,
        ];

        if($this->data['tiposDeMonedaPago'] != 'peso'){
            $pagoData['TipoCambioP'] = $this->Util()->CadenaOriginalFormat($this->data['tiposDeCambioPago'],4,false);
        }

        $this->CargaAtt($pago, $pagoData);

        $doctoRelacionado = $this->xml->createElement("pago10:DoctoRelacionado");
        $doctoRelacionado = $pago->appendChild($doctoRelacionado);

        $comprobante = $this->cfdiUtil->getInfoComprobanteRelacionado($this->data['cfdiRelacionadoSerie'], $this->data['cfdiRelacionadoFolio']);
        $infoPagos = $this->comprobantePago->getPagos($comprobante, $this->data['infoPago']->amount);

        $doctoRelacionadoData = [
            "IdDocumento" => $this->uuidRelacionado,
            "Serie" => $this->data['cfdiRelacionadoSerie'],
            "Folio" => $this->data['cfdiRelacionadoFolio'],
            "MonedaDR" => $tipoDeMoneda,
            "MetodoDePagoDR" => 'PPD',
            "NumParcialidad" => $infoPagos['numParcialidad'],
            'ImpSaldoAnt' => $this->Util()->CadenaOriginalFormat($infoPagos['impSaldoAnt'],2,false),
            'ImpPagado' => $this->Util()->CadenaOriginalFormat($infoPagos['impPagado'],2,false),
            'ImpSaldoInsoluto' => $this->Util()->CadenaOriginalFormat($infoPagos['impSaldoInsoluto'],2,false),
        ];

        if($this->data['tiposDeMonedaPago'] != 'peso'){
            $doctoRelacionadoData['TipoCambioDR'] = $this->Util()->CadenaOriginalFormat($this->data['tiposDeCambioPago'],4,false);
        }

        $this->CargaAtt($doctoRelacionado, $doctoRelacionadoData);
    }

    private function buildNodoComplementos() {
        if(!$this->isIngreso() && !$this->isNomina()) {
            return;
        }

        $this->complementos = $this->xml->createElement("cfdi:Complemento");
        $this->complementos = $this->root->appendChild($this->complementos);

        if($this->isNomina()) {
            include(DOC_ROOT."/services/complementos/Nomina.php");
        }

        if($this->isDonataria())
        {
            include(DOC_ROOT."/services/complementos/Donatarias.php");
        }

        if($this->totales['porcentajeISH'] > 0){
            include(DOC_ROOT."/services/complementos/Ish.php");
            $this->xsdImplocal = "http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd";
        }

        if(count($_SESSION["impuestos"]) > 0) {
            include(DOC_ROOT."/services/complementos/Impuestos.php");
            $this->xsdImplocal = "http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd";
        }
    }

    private function save() {
        $nufa = $this->miEmpresa["empresaId"]."_".$this->data["serie"]["serie"]."_".$this->data["folio"];

        $rfcActivo = $this->getRfcActive();
        $root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/xml/";
        $rootFacturas = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/";

        if(!is_dir($rootFacturas))
        {
            mkdir($rootFacturas, 0777);
        }

        if(!is_dir($root))
        {
            mkdir($root, 0777);
        }
        //print_r($this->xml);exit;

        return $this->xml->save($root.$nufa.".xml");
    }
}
?>
