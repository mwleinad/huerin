<?php
$this->impuestoslocales = $this->xml->createElement("implocal:ImpuestosLocales");
$this->impuestoslocales = $this->complementos->appendChild($this->impuestoslocales);

$this->CargaAtt($this->impuestoslocales, array(
        "TotaldeRetenciones" => $this->Util()->CadenaOriginalVariableFormat(0,true,false),
        "TotaldeTraslados"   => $this->Util()->CadenaOriginalVariableFormat($this->totales['ish'],true,false),
        "version" => $this->Util()->CadenaOriginalVariableFormat("1.0",false,false))
);

$this->impuestolocal = $this->xml->createElement("implocal:TrasladosLocales");
$this->impuestolocal = $this->impuestoslocales->appendChild($this->impuestolocal);
$this->CargaAtt($this->impuestolocal,array(
        "ImpLocTrasladado" => $this->Util()->CadenaOriginalVariableFormat("ISH",false,false),
        "TasadeTraslado"   => $this->Util()->CadenaOriginalVariableFormat($this->totales['porcentajeISH'],true,false),
        "Importe"          => $this->Util()->CadenaOriginalVariableFormat($this->totales['ish'],true,false)
    )
);
$this->totalImpuestosLocales = $this->totales['ish'];
?>