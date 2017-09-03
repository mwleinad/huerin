<?php

class Totales extends Comprobante
{
    public function calculate() {
        $values = explode("&", $_POST["form"]);
        foreach($values as $key => $val)
        {
            $array = explode("=", $values[$key]);
            $data[$array[0]] = $array[1];
        }

        if(!$_SESSION["conceptos"])
        {
            return false;
        }

        $data["subtotal"] = 0;
        $data["descuento"] = 0;
        $data["iva"] = 0;
        $data["ieps"] = 0;
        $data["ish"] = 0;
        $data["retIva"] = 0;
        $data["retIsr"] = 0;
        $data["total"] = 0;

        foreach($data as $key => $value)
        {
            $data[$key] = $this->Util()->RoundNumber($data[$key]);
        }

//echo "<pre>";
        foreach($_SESSION["conceptos"] as $key => $concepto)
        {
        }

        $paraRetencionIva = 0;
        foreach($_SESSION["conceptos"] as $key => $concepto) {
            $data["ieps"] += $concepto["totalIeps"];
            $data["ish"] += $concepto["totalIsh"];

            $_SESSION["conceptos"][$key]["descuento"] = $data["descuentoThis"];
            $_SESSION["conceptos"][$key]["importeTotal"] = $concepto["importe"] - $data["descuentoThis"];
            $_SESSION["conceptos"][$key]["totalIva"] = $_SESSION["conceptos"][$key]["importeTotal"] * ($_SESSION["conceptos"][$key]["tasaIva"] / 100);

        }
    }

}


?>