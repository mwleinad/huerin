<?php

class PacFinkok extends Util
{
    function GetCfdi($xmlFile, $signedXmlFile)
    {
        $fh = fopen($xmlFile, 'r');
        $xmlData = fread($fh, filesize($xmlFile));
        fclose($fh);

        $username = FINKOK_USER;
        $pw = FINKOK_PASS;

        $url = PROJECT_STATUS == "test"
            ?   "http://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl"
            :   "https://facturacion.finkok.com/servicios/soap/stamp.wsdl";

        $xmlData = base64_encode($xmlData);

        try {

            $client = new SoapClient($url);

            $params = array(
                'xml' => $xmlData,
                'username' => $username,
                'password' => $pw
            );

            $response = $client->__soapcall("stamp", array($params));
            if(count($response->stampResult->Incidencias->Incidencia)) {

                $return['worked'] = false;
                $return['response']['faultstring'] = utf8_decode($response->stampResult->Incidencias->Incidencia->MensajeIncidencia);

                return $return;
            }

            $data = $response->stampResult->xml;

            $fh = fopen($signedXmlFile, 'w') or die("can't open file");
            fwrite($fh, $data);
            fclose($fh);
            $return['worked'] = true;
            $return['response'] = $response;

            return $return;

        } catch (Throwable $error) {

            $return['worked']   = false;
            $return['response']['faultstring'] = "Error al conectar con el PAC ".$error->getMessage();
            return $return;
        }
    }

    function ParseTimbre($file)
    {
        $fh = fopen($file, 'r');
        $theData = fread($fh, filesize($file));
        $pos = strrpos($theData, "<tfd:TimbreFiscalDigital");
        $theData = substr($theData, $pos);

        $pos = strrpos($theData, "</cfdi:Complemento>");
        $theData = substr($theData, 0, $pos);

        $xml = @simplexml_load_string($theData);

        $data = array();
        foreach($xml->attributes() as $key => $attribute)
        {
            $data[$key] = (string)$attribute;
        }
        return $data;
    }

    function GenerateCadenaOriginalTimbre($data)
    {
        $cadenaOriginal = "||";
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["version"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["UUID"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["FechaTimbrado"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["selloCFD"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["noCertificadoSAT"]);
        $cadenaOriginal .= "|";

        $cadena = utf8_encode($cadenaOriginal);
        $data["original"] = $cadena;
        $data["sha1"] = sha1($cadena);
        return $data;
    }
}
