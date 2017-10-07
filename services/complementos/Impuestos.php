<?php
$this->impuestoslocales = $this->xml->createElement("implocal:ImpuestosLocales");
$this->impuestoslocales = $this->complementos->appendChild($this->impuestoslocales);

$totalRetenciones = 0;
foreach($_SESSION["impuestos"] as $key => $impuesto) {
    if(!isset($impuesto["parent"])) {
        continue;
    }
    if($impuesto["tasaIva"] > 0 ){
        $tasa = $impuesto["tasaIva"] / 100;
        $impuesto['importe'] = $impuesto['importe'] * (1 + $tasa);
    }
    $totalRetenciones += $impuesto['importe'];
}

$this->CargaAtt($this->impuestoslocales, array(
        "TotaldeRetenciones" => $this->Util()->CadenaOriginalVariableFormat($totalRetenciones,true,false),
        "TotaldeTraslados"   => $this->Util()->CadenaOriginalVariableFormat(0,true,false),
        "version" => $this->Util()->CadenaOriginalVariableFormat("1.0",false,false))
);

foreach($_SESSION["impuestos"] as $key => $impuesto) {

    if(!isset($impuesto["parent"])) {
        continue;
    }

    if($impuesto["tasaIva"] > 0){
        $tasa = $impuesto["tasaIva"] / 100;
        $impuesto['importe'] = $impuesto['importe'] * (1 + $tasa);
    }

    $this->impuestolocal = $this->xml->createElement("implocal:RetencionesLocales");
    $this->impuestolocal = $this->impuestoslocales->appendChild($this->impuestolocal);
    $this->CargaAtt($this->impuestolocal,array(
            "ImpLocRetenido" => $this->Util()->CadenaOriginalVariableFormat($impuesto['impuesto'],false,false),
            "TasadeRetencion"   => $this->Util()->CadenaOriginalVariableFormat($impuesto['tasa'],true,false),
            "Importe"          => $this->Util()->CadenaOriginalVariableFormat($impuesto['importe'],true,false)
        )
    );
}

$this->totalImpuestosLocales = ($totalRetenciones * -1);
?>