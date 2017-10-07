<?php

$this->complementoDonataria = $this->xml->createElement("donat:Donatarias");
$this->complementoDonataria = $this->complementos->appendChild($this->complementoDonataria);
$this->complementoDonataria->setAttribute("xmlns:donat", "http://www.sat.gob.mx/donat");

$this->CargaAtt(
    $this->complementoDonataria,
    array(
        "version"=>VERSION_DONATARIAS,
        "noAutorizacion"=>$this->Util()->CadenaOriginalVariableFormat($this->miEmpresa["noAutorizacion"], false, false),
        "fechaAutorizacion"=>$this->Util()->CadenaOriginalVariableFormat($this->miEmpresa["fechaAutorizacion"], false, false),
        "leyenda"=>$this->Util()->CadenaOriginalVariableFormat($this->miEmpresa["leyenda"], false, false),
    )
);
?>