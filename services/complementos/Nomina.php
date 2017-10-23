<?php

$this->myComplementoNomina = $this->xml->createElement("nomina12:Nomina");
$this->myComplementoNomina = $this->complementos->appendChild($this->myComplementoNomina);

if($this->data["nodoReceptor"]["fechaInicioRelLaboral"] == "0000-00-00")
{
    $this->data["nodoReceptor"]["fechaInicioRelLaboral"] = "1969-01-01";
}

if(!$this->data["nodoReceptor"]["periodicidadPago"])
{
    $this->data["nodoReceptor"]["periodicidadPago"] = "Quincenal";
}

$versionNomina = VERSION_NOMINA_12;

$totalPercepciones = $_SESSION["conceptos"]["1"]["percepciones"]["totalGravado"] + $_SESSION["conceptos"]["1"]["percepciones"]["totalExcento"] + $this->horasExtraImporte;

$totalDeducciones = $_SESSION["conceptos"]["1"]["deducciones"]["totalGravado"] + $_SESSION["conceptos"]["1"]["deducciones"]["totalExcento"] + $_SESSION["conceptos"]["1"]["incapacidades"]["total"];

$totalOtrosPagos = 0;
foreach($_SESSION["otrosPagos"] as $key => $value)
{
    $totalOtrosPagos += $value["importe"];
}

if($this->data["nodoReceptor"]["periodicidadPago"] != 99)
{
    $tipoNomina = "O";
}
else
{
    $tipoNomina = "E";
}

$nominaMain = array(
    "Version"=>$versionNomina,
    "TipoNomina"=>$tipoNomina,
    "FechaPago"=>$this->Util()->CadenaOriginalVariableFormat($this->data["fechaPago"], false, false),
    "FechaInicialPago"=>$this->Util()->CadenaOriginalVariableFormat($this->data["fechaPago"], false, false),
    "FechaFinalPago"=>$this->Util()->CadenaOriginalVariableFormat($this->data["fechaPago"], false, false),
    "NumDiasPagados"=>$this->Util()->CadenaOriginalVariableFormat($this->data["numDiasPagados"], true, false, false, true),
    "TotalPercepciones"=>$this->Util()->CadenaOriginalVariableFormat($totalPercepciones, true, false),
);

if($totalDeducciones > 0)
{
    $nominaMain["TotalDeducciones"] = $this->Util()->CadenaOriginalVariableFormat($totalDeducciones, true, false);
}

if($totalOtrosPagos > 0)
{
    $nominaMain["TotalOtrosPagos"] = $this->Util()->CadenaOriginalVariableFormat($totalOtrosPagos, true, false);
}

$this->CargaAtt(
    $this->myComplementoNomina,
    $nominaMain
);

//nodo emisor
$emisorData = [];
if($this->data["nodoReceptor"]["registroPatronal"]) {
    $emisorData["RegistroPatronal"] =$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["registroPatronal"], false, false);
}

if(strlen($this->data["nodoEmisor"]["rfc"]["rfc"]) == 13 && strlen($this->data["nodoEmisor"]["rfc"]["curp"]) > 0)
{
    $emisorData["Curp"] = $this->Util()->CadenaOriginalVariableFormat($this->data["nodoEmisor"]["rfc"]["curp"],false,false);
}

if(count($emisorData) > 0) {
    $emisor = $this->xml->createElement("nomina12:Emisor");
    $emisor = $this->myComplementoNomina->appendChild($emisor);

    $this->CargaAtt(
        $emisor, $emisorData
    );
}

//nodo receptor (sin subcontratacion)
$antiguedad = $this->Util()->weeks($this->data["nodoReceptor"]["fechaInicioRelLaboral"], $this->data["fechaPago"]);
$antiguedad = "P".$antiguedad."W";

$receptor = $this->xml->createElement("nomina12:Receptor");
$receptor = $this->myComplementoNomina->appendChild($receptor);
$this->CargaAtt(
    $receptor,
    array(
        "Curp"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["curp"], false, false),
        "NumSeguridadSocial"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["numSeguridadSocial"], false, false),
        "FechaInicioRelLaboral"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["fechaInicioRelLaboral"], false, false),
        "Antigüedad"=>$this->Util()->CadenaOriginalVariableFormat($antiguedad, false, false),
        "TipoContrato"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["tipoContrato"], false, false),
        "TipoJornada"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["tipoJonada"], false, false),
        "TipoRegimen"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["tipoRegimen"], false, false),
        "NumEmpleado"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["numEmpleado"], false, false),
        "Departamento"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["departamento"], false, false),
        "Puesto"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["puesto"], false, false),
        "RiesgoPuesto"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["riesgoPuesto"], false, false),
        "PeriodicidadPago"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["periodicidadPago"], false, false),
        //"Banco"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["banco"], false, false),
        //"CuentaBancaria"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["clabe"], false, false),
        "SalarioBaseCotApor"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["salarioBaseCotApor"], true, false),
        "SalarioDiarioIntegrado"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["salarioDiarioIntegrado"], true, false),
        "ClaveEntFed"=>$this->Util()->CadenaOriginalVariableFormat($this->data["nodoReceptor"]["estado"], false, false),
    )
);

//percepciones
$percepcion = $this->xml->createElement("nomina12:Percepciones");
$percepcion = $this->myComplementoNomina->appendChild($percepcion);

$totalSueldos = 0;
$totalSeparacionIndemnizacion = 0;
$totalJubilacionPensionRetiro = 0;
foreach($_SESSION["percepciones"] as $myPercepcion)
{
    if($myPercepcion["tipoPercepcion"] != "022" &&
        $myPercepcion["tipoPercepcion"] != "023" &&
        $myPercepcion["tipoPercepcion"] != "025" &&
        $myPercepcion["tipoPercepcion"] != "039" &&
        $myPercepcion["tipoPercepcion"] != "044") {
        $totalSueldos += $myPercepcion["importeGravado"] + $myPercepcion["importeExcento"];
    }

    if($myPercepcion["tipoPercepcion"] == "022" ||
        $myPercepcion["tipoPercepcion"] == "023" ||
        $myPercepcion["tipoPercepcion"] == "025") {
        $totalSeparacionIndemnizacion += $myPercepcion["importeGravado"] + $myPercepcion["importeExcento"];
    }

    if($myPercepcion["tipoPercepcion"] == "039" ||
        $myPercepcion["tipoPercepcion"] == "044") {
        $totalJubilacionPensionRetiro += $myPercepcion["importeGravado"] + $myPercepcion["importeExcento"];
    }

}
$totalSueldos = $totalSueldos + $this->horasExtraImporte;
$_SESSION["conceptos"]["1"]["percepciones"]["totalExcento"] + $this->horasExtraImporte;
$totalGravado = $_SESSION["conceptos"]["1"]["percepciones"]["totalGravado"] + $this->horasExtraImporte;

$this->CargaAtt(
    $percepcion,
    array(
        "TotalSueldos"=>$this->Util()->CadenaOriginalVariableFormat($totalSueldos, true, false),
        "TotalSeparacionIndemnizacion"=>$this->Util()->CadenaOriginalVariableFormat($totalSeparacionIndemnizacion, true, false),
        "TotalJubilacionPensionRetiro"=>$this->Util()->CadenaOriginalVariableFormat($totalJubilacionPensionRetiro, true, false),
        "TotalGravado"=>$this->Util()->CadenaOriginalVariableFormat($totalGravado, true, false),
        "TotalExento"=>$this->Util()->CadenaOriginalVariableFormat($_SESSION["conceptos"]["1"]["percepciones"]["totalExcento"], true, false),
    )
);

foreach($_SESSION["percepciones"] as $myPercepcion)
{
    $percepciones = $this->xml->createElement("nomina12:Percepcion");
    $percepciones = $percepcion->appendChild($percepciones);
    $this->CargaAtt(
        $percepciones,
        array(
            "TipoPercepcion"=>$this->Util()->CadenaOriginalVariableFormat($myPercepcion["tipoPercepcion"], false, false),
            "Clave"=>$this->Util()->CadenaOriginalVariableFormat($myPercepcion["tipoPercepcion"], false, false),
            "Concepto"=>$this->Util()->CadenaOriginalVariableFormat($myPercepcion["nombrePercepcion"], false, false),
            "ImporteGravado"=>$this->Util()->CadenaOriginalVariableFormat($myPercepcion["importeGravado"], true, false),
            "ImporteExento"=>$this->Util()->CadenaOriginalVariableFormat($myPercepcion["importeExcento"], true, false),
        )
    );
}

//nodo horas extra
if(count($_SESSION["horasExtras"]) > 0)
{
    foreach($_SESSION["horasExtras"] as $myHoraExtra)
    {
        $percepciones = $this->xml->createElement("nomina12:Percepcion");
        $percepciones = $percepcion->appendChild($percepciones);
        //tipo horas extra hacerlo hard code
        $this->CargaAtt(
            $percepciones,
            array(
                "TipoPercepcion"=>$this->Util()->CadenaOriginalVariableFormat("019", false, false),
                "Clave"=>$this->Util()->CadenaOriginalVariableFormat("003", false, false),
                "Concepto"=>$this->Util()->CadenaOriginalVariableFormat("Horas Extra", false, false),
                "ImporteGravado"=>$this->Util()->CadenaOriginalVariableFormat($myHoraExtra["importePagado"], true, false),
                "ImporteExento"=>$this->Util()->CadenaOriginalVariableFormat(0, true, false),
            )
        );

        $horasExtra = $this->xml->createElement("nomina12:HorasExtra");
        $horasExtra = $percepciones->appendChild($horasExtra);

        if($myHoraExtra["dias"] == 0)
        {
            $myHoraExtra["dias"] = 1;
        }

        $this->CargaAtt(
            $horasExtra,
            array(
                "Dias"=>$this->Util()->CadenaOriginalVariableFormat($myHoraExtra["dias"], false, false),
                "TipoHoras"=>$this->Util()->CadenaOriginalVariableFormat($myHoraExtra["tipoHoras"], false, false),
                "HorasExtra"=>$this->Util()->CadenaOriginalVariableFormat($myHoraExtra["horasExtra"], false, false),
                "ImportePagado"=>$this->Util()->CadenaOriginalVariableFormat($myHoraExtra["importePagado"], true, false)
            )
        );
    }
}

if($totalSeparacionIndemnizacion > 0){

    $separacionIndemnizacion = $this->xml->createElement("nomina12:SeparacionIndemnizacion");
    $separacionIndemnizacion = $percepcion->appendChild($separacionIndemnizacion);

    $aniosServicio = ceil($this->Util()->weeks($this->data["nodoReceptor"]["fechaInicioRelLaboral"], $this->data["fechaPago"]) / 52);
    $ingresoAcumulable = 0;
    if($totalSueldos > $totalSeparacionIndemnizacion) {
        $ingresoAcumulable = $totalSeparacionIndemnizacion;
    } else {
        $ingresoAcumulable = $totalSueldos;
    }

    $ingresoNoAcumulable = $totalSeparacionIndemnizacion - $totalSueldos;

    if($ingresoNoAcumulable < 0) {
        $ingresoNoAcumulable = 0;
    }

    $this->CargaAtt(
        $separacionIndemnizacion,
        array(
            "TotalPagado"=>$this->Util()->CadenaOriginalVariableFormat($totalSeparacionIndemnizacion, false, false),
            "NumAñosServicio"=>$this->Util()->CadenaOriginalVariableFormat($aniosServicio, false, false),
            "UltimoSueldoMensOrd"=>$this->Util()->CadenaOriginalVariableFormat($totalSueldos, false, false),
            "IngresoAcumulable"=>$this->Util()->CadenaOriginalVariableFormat($ingresoAcumulable, true, false),
            "IngresoNoAcumulable"=>$this->Util()->CadenaOriginalVariableFormat($ingresoNoAcumulable, true, false)
        )
    );
}

//nodo deducciones
if(count($_SESSION["deducciones"]) > 0 || count($_SESSION["incapacidades"]) > 0)
{
    $totalOtrasDeducciones = 0;
    $totalImpuestosRetenidos = 0;

    if(count($_SESSION["deducciones"]) > 0) {
        foreach($_SESSION["deducciones"] as $myDeduccion)
        {
            if($myDeduccion["tipoDeduccion"] == "002")
            {
                $totalImpuestosRetenidos += $myDeduccion["importeExcento"] + $myDeduccion["importeGravado"];
            }
            else
            {
                $totalOtrasDeducciones += $myDeduccion["importeExcento"] + $myDeduccion["importeGravado"];
            }
        }
    }

    if(count($_SESSION["incapacidades"]) > 0)
    {
        foreach($_SESSION["incapacidades"] as $myIncapacidad)
        {
            $totalOtrasDeducciones += $myIncapacidad["descuento"];
        }
    }

    $deduccion = $this->xml->createElement("nomina12:Deducciones");
    $deduccion = $this->myComplementoNomina->appendChild($deduccion);

    $atributosDeduccion["TotalOtrasDeducciones"] = $this->Util()->CadenaOriginalVariableFormat($totalOtrasDeducciones, true, false);

    if($totalImpuestosRetenidos > 0)
    {
        $atributosDeduccion["TotalImpuestosRetenidos"] =$this->Util()->CadenaOriginalVariableFormat($totalImpuestosRetenidos, true, false);
    }

    $this->CargaAtt(
        $deduccion,
        $atributosDeduccion
    );

    if(count($_SESSION["deducciones"]) > 0) {

        foreach($_SESSION["deducciones"] as $myDeduccion)
        {
            $deducciones = $this->xml->createElement("nomina12:Deduccion");
            $deducciones = $deduccion->appendChild($deducciones);

            $importe = $myDeduccion["importeGravado"] + $myDeduccion["importeExcento"];
            $this->CargaAtt(
                $deducciones,
                array(
                    "TipoDeduccion"=>$this->Util()->CadenaOriginalVariableFormat($myDeduccion["tipoDeduccion"], false, false),
                    "Clave"=>$this->Util()->CadenaOriginalVariableFormat($myDeduccion["tipoDeduccion"], false, false),
                    "Concepto"=>$this->Util()->CadenaOriginalVariableFormat($myDeduccion["nombreDeduccion"], false, false),
                    "Importe"=>$this->Util()->CadenaOriginalVariableFormat($importe, true, false),
                )
            );
        }
    }
}//count deducciones


//nodo otros pagos
if(count($_SESSION["otrosPagos"]) > 0)
{
    $otroPago = $this->xml->createElement("nomina12:OtrosPagos");
    $otroPago = $this->myComplementoNomina->appendChild($otroPago);

    $this->CargaAtt(
        $otroPago,
        array(
        )
    );

    foreach($_SESSION["otrosPagos"] as $myOtroPago)
    {
        $otrosPagos = $this->xml->createElement("nomina12:OtroPago");
        $otrosPagos = $otroPago->appendChild($otrosPagos);
        $this->CargaAtt(
            $otrosPagos,
            array(
                "TipoOtroPago"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["tipoOtroPago"], false, false),
                "Clave"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["tipoOtroPago"], false, false),
                "Concepto"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["nombreOtroPago"], false, false),
                "Importe"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["importe"], true, false),
            )
        );

        if($myOtroPago["tipoOtroPago"] == "002")
        {
            $subsidio = $this->xml->createElement("nomina12:SubsidioAlEmpleo");
            $subsidio = $otrosPagos->appendChild($subsidio);
            $this->CargaAtt(
                $subsidio,
                array(
                    "SubsidioCausado"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["importe"], true, false),
                )
            );
        }

        if($myOtroPago["tipoOtroPago"] == "004")
        {
            $subsidio = $this->xml->createElement("nomina12:CompensacionSaldosAFavor");
            $subsidio = $otrosPagos->appendChild($subsidio);
            $this->CargaAtt(
                $subsidio,
                array(
                    "SaldoAFavor"=>$this->Util()->CadenaOriginalVariableFormat($myOtroPago["importe"], true, false),
                    "Año"=>$this->Util()->CadenaOriginalVariableFormat(date("Y"), true, false),
                    "RemanenteSalFav"=>$this->Util()->CadenaOriginalVariableFormat("0", true, false),
                )
            );
        }
    }
}

//nodo incapacidades
if(count($_SESSION["incapacidades"]) > 0)
{
    $incapacidad = $this->xml->createElement("nomina12:Incapacidades");
    $incapacidad = $this->myComplementoNomina->appendChild($incapacidad);

    foreach($_SESSION["incapacidades"] as $myIncapacidad)
    {
        $incapacidades = $this->xml->createElement("nomina12:Incapacidad");
        $incapacidades = $incapacidad->appendChild($incapacidades);
        $this->CargaAtt(
            $incapacidades,
            array(
                "DiasIncapacidad"=>$this->Util()->CadenaOriginalVariableFormat($myIncapacidad["diasIncapacidad"], true, false,false,false,true),
                "TipoIncapacidad"=>$this->Util()->CadenaOriginalVariableFormat($myIncapacidad["tipoIncapacidad"], false, false),
                "ImporteMonetario"=>$this->Util()->CadenaOriginalVariableFormat($myIncapacidad["descuento"], true, false)
            )
        );
    }
}

?>