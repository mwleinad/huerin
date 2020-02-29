<?php

class CfdiUtil extends Comprobante
{
    public function getUUID($id) {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante
        WHERE comprobanteId = '".$id."' ");

        $cfdiRelacionado = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$cfdiRelacionado) {
            return null;
        }
        //retornar uuid para facturas que se recuperaron.
        if($cfdiRelacionado["timbreFiscal"]===""||$cfdiRelacionado["timbreFiscal"]===null)
        {
            $nameXml ="SIGN_".$cfdiRelacionado['xml'];
            $timbre = $this->getDataByXml($nameXml);
            return $timbre["uuid"];
        }
        $timbre = unserialize($cfdiRelacionado["timbreFiscal"]);
        return $timbre["UUID"];
    }

    public function getInfoComprobanteRelacionado($id) {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante
        WHERE comprobanteId = '".$id."' ");

        $cfdiRelacionado = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$cfdiRelacionado) {
            return null;
        }

        return $cfdiRelacionado;
    }

    public function cadenaOriginalTimbre($timbre){
        $cadenaOriginal = "||";
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($timbre["Version"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($timbre["UUID"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($timbre["FechaTimbrado"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($timbre["SelloCFD"]);
        $cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($timbre["NoCertificadoSAT"]);
        $cadenaOriginal .= "|";

        $cadena = utf8_encode($cadenaOriginal);
        $data["original"] = $cadena;
        $data["sha1"] = sha1($cadena);
        return $data;
    }

    public function getFilename($id) {

        $id = explode("_", $id);

        if(!isset($id[1])) {
            return null;
        }

        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT xml FROM comprobante
        WHERE comprobanteId = '".$id[1]."'");

        $xml = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();

        if(!$xml) {
            return null;
        }

        $fileName = "SIGN_".$xml;

        return $fileName;
    }
}


?>