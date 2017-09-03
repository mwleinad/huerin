<?php

class CfdiUtil extends Comprobante
{
    public function getUUID($serie, $folio) {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante
        WHERE serie = '".$serie."'
        AND folio = '".$folio."'");

        $cfdiRelacionado = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$cfdiRelacionado) {
            return null;
        }

        $timbre = unserialize($cfdiRelacionado["timbreFiscal"]);
        return $timbre["UUID"];
    }

    public function getInfoComprobanteRelacionado($serie, $folio) {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante
        WHERE serie = '".$serie."'
        AND folio = '".$folio."'");

        $cfdiRelacionado = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$cfdiRelacionado) {
            return null;
        }

        return $cfdiRelacionado;
    }
}


?>