<?php

class Impuestos extends Comprobante
{
    public function Generar($xmlConSello, $impuestos, $firmas, $amortizacionInfo)
    {
        $xml = new DOMdocument();
        $root = $xml->createElement("cfdi:Addenda");
        $root = $xml->appendChild($root);

        if(count($impuestos) > 0){
            foreach($impuestos as $impuesto){
                $request = $xml->createElement("AddendaImpuesto");
                $request = $root->appendChild($request);

                $this->CargaAtt($request, $impuesto);
            }
        }

        if(count($firmas) > 0){
            foreach($firmas as $firma){
                $request = $xml->createElement("AddendaFirma");
                $request = $root->appendChild($request);

                $this->CargaAtt($request, $firma);
            }
        }

        if(count($amortizacionInfo) > 0){
            $request = $xml->createElement("AddendaAmortizacion");
            $request = $root->appendChild($request);
            $this->CargaAtt($request, $amortizacionInfo);
        }

        $strAddenda = $xml->saveXML();
        $strAddenda = urlencode($strAddenda);
        $strAddenda = str_replace("%3C%3Fxml+version%3D%221.0%22%3F%3E","",$strAddenda);
        $strAddenda = urldecode($strAddenda);

        $fh = fopen($xmlConSello['xmlSignedFile'], 'r');
        $theData = fread($fh, filesize($xmlConSello['xmlSignedFile']));
        fclose($fh);
        $theData = str_replace("</cfdi:Complemento>", "</cfdi:Complemento>".$strAddenda, $theData);

        $fh = fopen($xmlConSello['xmlSignedFile'], 'w') or die("can't open file");
        fwrite($fh, $theData);
        fclose($fh);
    }

    //TODO mover a una funcion mas generica
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
}


?>