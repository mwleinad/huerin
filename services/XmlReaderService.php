<?php

class XmlReaderService extends Comprobante
{
    function execute($pathXml, $empresaId,$id=0)
    {
        $xml = simplexml_load_file($pathXml);
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('c',$ns['cfdi']);
        $xml->registerXPathNamespace('t',$ns['tfd']);
        $xml->registerXPathNamespace('pago10',$ns['pago10']);
        $xml->registerXPathNamespace('impLocal',$ns['implocal']);
        $xml->registerXPathNamespace('nomina12',$ns['nomina12']);
        $xml->registerXPathNamespace('donat',$ns['donat']);
        $xml->registerXPathNamespace('AddendaEscuela',$ns['AddendaEscuela']);
        $xml->registerXPathNamespace('AddendaImpuesto',$ns['AddendaImpuesto']);
        $xml->registerXPathNamespace('AddendaFirma',$ns['AddendaFirma']);
        $xml->registerXPathNamespace('AddendaAmortizacion',$ns['AddendaAmortizacion']);

        //cfdi
        $data["cfdi"] = $xml->xpath('//cfdi:Comprobante')[0];

        //Obtenemos la informacion del Comprobante, esto es solo para saber si esta cancelado. Lo demas se obtiene del xml
        $sql = "SELECT * FROM comprobante
				WHERE comprobanteId ='$id' ";
        $this->Util()->DBSelect($empresaId)->setQuery($sql);
        $data["db"] = $this->Util()->DBSelect($empresaId)->GetRow();
        $this->Util()->DB()->setQuery("SELECT status FROM pending_cfdi_cancel WHERE cfdi_id = '".$data["db"]['comprobanteId']."'");
        $data["db"]["cfdi_cancel_status"] = $this->Util()->DB()->GetSingle();

        if($_SESSION['observaciones']){
            $data['db']['observaciones'] = $_SESSION['observaciones'];
        }

        $sql = "SELECT * FROM serie
				WHERE serie = '".$data["cfdi"]['Serie']."' and rfcId='".$data['db']['rfcId']."' ";
        $this->Util()->DBSelect($empresaId)->setQuery($sql);
        $data["serie"] = $this->Util()->DBSelect($empresaId)->GetRow();

        //Emisor
        $data["emisor"] = $xml->xpath('//cfdi:Emisor')[0];
        $data["receptor"] = $xml->xpath('//cfdi:Receptor')[0];

        //Conceptos
        //El punto hace que sea relativo al elemento, y solo una / es para buscar exactamente eso
        foreach($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $con){
            $concepto["concepto"] = $con;
            $concepto["traslados"] = $con->xpath('./cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
            $concepto["retenciones"] = $con->xpath('./cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion');
            $concepto["cuentaPredial"] = $con->xpath('./cfdi:CuentaPredial')[0];

            $data["conceptos"][] = $concepto;
        }

        //Impuestos
        if(isset($xml->xpath('/cfdi:Comprobante/cfdi:Impuestos')[0])){
            //El punto hace que sea relativo al elemento
            $impuesto["impuestos"] = $xml->xpath('/cfdi:Comprobante/cfdi:Impuestos')[0];
            $impuesto["traslados"] = $impuesto["impuestos"]->xpath('./cfdi:Traslados/cfdi:Traslado');
            $impuesto["retenciones"] = $impuesto["impuestos"]->xpath('.//cfdi:Retenciones/cfdi:Retencion');

            $data["impuestos"] = $impuesto;
        }

        //Timbre fiscal
        foreach($xml->xpath('//t:TimbreFiscalDigital') as $con){
            $data['timbreFiscal'] = $con;
        }

        $cfdiUtil = new CfdiUtil();
        $data["timbre"] = $cfdiUtil->cadenaOriginalTimbre($data['timbreFiscal']);

        //Importe en letra
        $temp = new CNumeroaLetra ();
        $temp->setMayusculas(1);
        $temp->setGenero(1);
        $temp->setMoneda($data['cfdi']["Moneda"]);
        $temp->setDinero(1);
        $temp->setPrefijo('');
        $temp->setSufijo('');
        $temp->setNumero($data['cfdi']["Total"]);
        $data["letra"] = $temp->letra();

        //Complementos ImpuestosLocales
        $donatarias = $xml->xpath('//donat:Donatarias ');

        if(isset($donatarias[0])){
            $data['donatarias'] = $donatarias[0];
        }

        if(isset($impuestos[0])){
            $data['impuestosLocales']['totales'] = $impuestos[0];

            $trasladosLocales = $impuestos[0]->xpath('//implocal:TrasladosLocales');

            foreach($trasladosLocales as $con){
                if($con['ImpLocTrasladado'] == 'ISH') {
                    $data['impuestosLocales']['ish'] = $con;
                } else {
                    $data['impuestosLocales']['traslados'] = $con;
                }
            }
        }

        $impuestos = $xml->xpath('//impLocal:ImpuestosLocales');

        if(isset($impuestos[0])){
            $data['impuestosLocales']['totales'] = $impuestos[0];

            $trasladosLocales = $impuestos[0]->xpath('//implocal:TrasladosLocales');

            foreach($trasladosLocales as $con){
                if($con['ImpLocTrasladado'] == 'ISH') {
                    $data['impuestosLocales']['ish'] = $con;
                } else {
                    $data['impuestosLocales']['traslados'] = $con;
                }
            }
        }

        $pagos = $xml->xpath('//pago10:Pagos');

        if(isset($pagos[0])){
            $pagos = $pagos[0]->xpath('//pago10:Pago');

            foreach($pagos as $pago){
                $card['data'] = $pagos[0];
                $card['pago'] = $pago[0];
                $card['doctoRelacionado'] =  $pago[0]->xpath('//pago10:DoctoRelacionado ')[0];

                $data["pagos"][] = $card;
            }
        }

        $nomina = $xml->xpath('//nomina12:Nomina');

        if(isset($nomina[0])){
            $card["data"] = $nomina[0];
            $card['emisor'] = $nomina[0]->xpath('//nomina12:Emisor')[0];
            $card['receptor'] = $nomina[0]->xpath('//nomina12:Receptor')[0];

            $card['receptor']['Antiguedad'] = $card['receptor']["Antigüedad"];

            $card['percepciones']["data"] = $nomina[0]->xpath('//nomina12:Percepciones')[0];

            $percepciones = $card['percepciones']["data"]->xpath('//nomina12:Percepcion');

            //TODO horas extra
            foreach($percepciones as $percepcion) {
                $card['percepciones']["percepcion"][] = $percepcion;
            }

            $card['deducciones']["data"] = $nomina[0]->xpath('//nomina12:Deducciones')[0];

            if($card['deducciones']["data"]){
                $deducciones = $card['deducciones']["data"]->xpath('//nomina12:Deduccion');

                //TODO partes como CompensacionSaldosAFavor SubsidioAlEmpleo etc
                foreach($deducciones as $deduccion) {
                    $card['deducciones']["deduccion"][] = $deduccion;
                }
            }

            $card['otrosPagos']["data"] = $nomina[0]->xpath('//nomina12:OtrosPagos')[0];

            if(isset($card['otrosPagos']["data"])) {
                $otrosPagos = $card['otrosPagos']["data"]->xpath('//nomina12:OtroPago');


                foreach($otrosPagos as $otroPago) {
                    $card['otrosPagos']["otroPago"][] = $otroPago;
                }
            }

            $card['incapacidades']["data"] = $nomina[0]->xpath('//nomina12:Incapacidades')[0];

            if(isset($card['incapacidades']["data"])) {
                $incapacidades = $card['incapacidades']["data"]->xpath('//nomina12:Incapacidad');

                foreach($incapacidades as $incapacidad) {
                    $card['incapacidades']["incapacidad"][] = $incapacidad;
                }
            }

            $data['nomina'] = $card;
        }

        //Addenda escuela
        $escuela = $xml->xpath('//AddendaEscuela');

        if(isset($escuela[0])){
            $data['escuela'] = $escuela[0];
        }

        //Addenda impuestos
        $impuestos = $xml->xpath('//AddendaImpuesto');

        if(count($impuestos) > 0) {
            $impuestosLocales = [];
            foreach($impuestos as $key => $impuesto){
                if(strpos($impuesto['impuesto'][0], '16% IVA') === false){
                    $impuestosLocales[$key]['impuesto'] = $impuesto;

                    $nextImpuesto = $impuestos[$key + 1];
                    if(strpos($nextImpuesto['impuesto'][0], '16% IVA') !== false){
                        $impuestosLocales[$key]['extra'] = $nextImpuesto;
                    }
                }
            }
            //print_r($impuestosLocales);
            $data['impuestosLocales'] = $impuestosLocales;
        }

        //Workaround as the impuestos only exists in the addenda, this is to show them in the preview
        if(count($impuestos) == 0 && $_SESSION['impuestos']){
            $impuestosLocales = [];
            foreach($_SESSION['impuestos'] as $key => $impuesto){
                //print_r($impuesto);
                if(strpos($impuesto['impuesto'], '16% IVA') === false){
                    $impuestosLocales[$key]['impuesto'] = $impuesto;

                    $nextImpuesto = $_SESSION['impuestos'][$key + 1];
                    if(strpos($nextImpuesto['impuesto'], '16% IVA') !== false){
                        $impuestosLocales[$key]['extra'] = $nextImpuesto;
                    }
                }
            }
            $data['impuestosLocales'] = $impuestosLocales;
        }

        //Addenda firmas
        $firmas = $xml->xpath('//AddendaFirma');

        if(count($firmas) > 0) {
            $firmasLocales = [];
            foreach($firmas as $key => $firma){
                $firmasLocales[$key] = $firma;
            }
            $data['firmasLocales'] = $firmasLocales;
        }

        //Workaround as the firmas only exists in the addenda, this is to show them in the preview
        if(count($firmas) == 0 && $_SESSION['firmas']){
            $firmasLocales = [];
            foreach($_SESSION['firmas'] as $key => $firma){
                $firmasLocales[$key] = $firma;
            }
            $data['firmasLocales'] = $firmasLocales;
        }

        //Addenda amortizacion
        $amortizacionInfo = $xml->xpath('//AddendaAmortizacion');
        if(count($amortizacionInfo) > 0) {
            $data['amortizacionData'] = $amortizacionInfo[0];
        }

        //Workaround as the firmas only exists in the addenda, this is to show them in the preview
        if(count($amortizacionInfo) == 0 && $_SESSION['amortizacion']){
            $amortizacionData = [];
            foreach($_SESSION['amortizacion'] as $key => $firma){
                $amortizacionData[$key] = $firma;
            }
            $data['amortizacionData'] = $amortizacionData;
        }

        return $data;
    }
}



?>