<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 15/01/2019
 * Time: 02:46 PM
 */

class ControlFromXml extends Comprobante
{
    function updatePaymentsFromXml(){
        global $catalogo,$cancelation;
        $facturas = [];
        $directorio = opendir(DIR_FROM_XML);
        while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
        {
            if(strpos($archivo,'zip')!==false || strpos($archivo,'COMPAGO')===false || strpos($archivo,'SIGN')===false)
                continue;
            $name_complemento = explode(".",$archivo);
            //si esta registrado en la tabla comprobante el, se debe excluir por que ya esta tomado en cuenta.
            $xml_comp = str_replace("SIGN_","",$name_complemento[0]);
            $this->Util()->DB()->setQuery("select comprobanteId from comprobante where xml='".$xml_comp."' ");
            $findXml = $this->Util()->DB()->GetSingle();
            if($findXml)
                continue;

            //no duplicar
            $this->Util()->DB()->setQuery("select payment_id from payment_from_xml_static where name_xml_complemento='".$name_complemento[0]."' ");
            $isRegister = $this->Util()->DB()->GetSingle();
            if($isRegister)
                continue;

            $document = [];
            $pathXml =  DIR_FROM_XML."/".$archivo;
            $xml = simplexml_load_file($pathXml);
            $ns = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('c',$ns['cfdi']);
            $xml->registerXPathNamespace('t',$ns['tfd']);

            $xmlCfdi= $xml->xpath('//cfdi:Comprobante')[0];
            $data["emisor"] = $xml->xpath('//cfdi:Emisor')[0];
            $data["receptor"] = $xml->xpath('//cfdi:Receptor')[0];

            //aplicar $filtor si existe
            $dateExp = explode('T',(string)$xmlCfdi['Fecha']);
            $document['fecha'] = (string)$dateExp[0];
            foreach($xml->xpath('//t:TimbreFiscalDigital') as $con){
                $data['timbreFiscal'] = $con;
            }
            $document['total'] = (string)$xmlCfdi['Total'];
            $document['subtotal'] = (string)$xmlCfdi['SubTotal'];
            $document['folioComplete'] =(string)$xmlCfdi['Serie'].(string)$xmlCfdi['Folio'];
            $document['folio'] =(string)$xmlCfdi['Folio'];
            $document['serie'] =(string)$xmlCfdi['Serie'];
            $document['receptorRfc'] = (string)$data['receptor']['Rfc'];
            $document['receptorName'] = (string)$data['receptor']['Nombre'];
            $document['emisorRfc'] = (string)$data['emisor']['Rfc'];
            $document['emisorName'] =(string)$data['emisor']['Nombre'];
            $document['uuid'] =(string)$data['timbreFiscal']['UUID'];
            //find docmento relacionado, se sabe que la plataforma solo relaciona un uuid por complemento asi que accesar al primero nada mas
            $pagos = $xml->xpath('//pago10:Pagos');
            $cad = [];
            if(isset($pagos[0])){
               $pagos = $pagos[0]->xpath('//pago10:Pago');
               $cad['pago'] = $pagos[0];
               $cad['relacion'] = $pagos[0]->xpath('//pago10:DoctoRelacionado ')[0];
               $document['docRelacionado'] =  $cad;
            }
            //find FormaDePagoP
            $rowMetodoPago = $catalogo->getFormaPagoByClave($document['docRelacionado']['pago']['FormaDePagoP']);
            $metodoPago = $rowMetodoPago["descripcion"];
            $amount =$document['docRelacionado']['pago']['Monto'];
            $deposito =  $amount;
            $paymentDate = $document['fecha'];
            $ext="";
            $serieR = $document['docRelacionado']['relacion']['Serie'];
            $folioR = $document['docRelacionado']['relacion']['Folio'];
            $folio = $serieR.$folioR;
            $name_xml ="SIGN_21_$serieR"."_".$folioR;
            $uuid = $document['docRelacionado']['relacion']['IdDocumento'];
            $name_xml_complemento = $name_complemento[0];
            $status_compago = $cancelation->getStatus($document['emisorRfc'],$document['receptorRfc'],$document['uuid'],$document['total']);
            switch($status_compago['status']){
                case 'Cancelado':
                    $status = "cancelado";
                break;
                default:
                    $status = "activo";
                break;
            }

            $sql =  "insert into payment_from_xml_static
                     (
                      metodoDePago,
                      amount,
                      deposito,
                      paymentDate,
                      ext,
                      folio,
                      name_xml,
                      uuid,
                      name_xml_complemento,
                      payment_status
                     )values
                     (
                      '".$metodoPago."',
                      '".$amount."',
                      '".$deposito."',
                      '".$paymentDate."',
                      '".$ext."',
                      '".$folio."',
                      '".$name_xml."',
                      '".$uuid."',
                      '".$name_xml_complemento."',
                      '".$status."'
                     )";

            $this->Util()->DB()->setQuery($sql);
            $this->Util()->DB()->InsertData();
        }
        $this->Util()->setError(10046, "complete", "Se ha actualizado correctamente los pagos..");
        $this->Util()->PrintErrors();
        return true;
    }

}