<?php

class Escuela extends Comprobante
{
    public function Generar($xmlConSello, $data)
    {
        $xml = new DOMdocument();
        $root = $xml->createElement("cfdi:Addenda");
        $root = $xml->appendChild($root);

        $request = $xml->createElement("AddendaEscuela");
        $request = $root->appendChild($request);

        $data = [
            "noControl"=>$data['nodoReceptor']['noControl'],
            "carrera"=>$data['nodoReceptor']['carrera'],
            "banco"=>$data['banco'],
            "fechaDeposito"=>$data['fechaDeposito'],
            "referencia"=>$data['referencia'],
        ];

        $this->CargaAtt($request, $data);

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