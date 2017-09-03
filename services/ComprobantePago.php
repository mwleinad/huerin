<?php

class ComprobantePago extends Comprobante {

    private function generateSerieIfNotExists() {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie");
        $serieExistente = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        //Create series if it doesn't exists TODO do not let this serie to be deleted or modified, also hide it from other places
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE tiposComprobanteId = 10");
        $serie = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$serie) {

            $vs = new User;
            $activeRfc =  $vs->getRfcActive();
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("
			INSERT INTO `serie` (
				`empresaId`,
				`sucursalId`,
				`serie`,
				`folioInicial`,
				`folioFinal`,
				`tiposComprobanteId`,
				`lugarDeExpedicion`,
				`noCertificado`,
				`consecutivo`,
				`rfcId`,
				`sucursalAsignada`
			) VALUES
			(
				'".$_SESSION["empresaId"]."',
				'0',
				'COMPAGO',
				'1',
				'999999999',
				'10',
				'0',
				'".$serieExistente['noCertificado']."',
				'1',
				'".$activeRfc."',
				'0'
			)");
            return $this->Util()->DBSelect($_SESSION["empresaId"])->InsertData();
        }

        return $serie['serieId'];
    }

    private function addConcept() {
        unset($_SESSION["conceptos"]);
        $_SESSION["conceptos"][0] = [
            'claveProdServ' => '84111506',
            'cantidad' => 1,
            'claveUnidad' => 'ACT',
            'descripcion' => 'Pago',
            'valorUnitario' => 0,
            'importe' => 0,
            'unidad' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor

        ];
    }

    private function getCfdiRelacionado($infoComprobante) {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM comprobante WHERE comprobanteId = '".$infoComprobante["comprobanteId"]."'");
        $comprobante = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        return $comprobante;
    }

    public function generar($infoComprobante, $infoPago){
        $cfdi = new Cfdi();

        $serieId = $this->generateSerieIfNotExists();

        $comprobante = $cfdiRelacionado = $this->getCfdiRelacionado($infoComprobante);

        //no hay comprobante al que asignar el pago
        if(!$comprobante) {
            return;
        }

        $this->addConcept();

        $data = [
            'formatoNormal' => 0,
            'tiposComprobanteId' => '10-'.$serieId,
            'tiposDeMoneda' => 'XXX',
            'cfdiRelacionadoSerie' => $comprobante["serie"],
            'cfdiRelacionadoFolio' => $comprobante["folio"],
            'tipoRelacion' => '04',
            'userId' => $comprobante['userId'],
            'usoCfdi' => 'P01',
            'formaDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'metodoDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'infoPago' => $infoPago,
            'tiposDeMonedaPago' => $comprobante['tipoDeMoneda'],
            'tiposDeCambioPago' => $comprobante['tipoDeCambio'],
        ];

        if(!$comprobanteId = $cfdi->Generar($data)) {
            return null;
        }

        return $comprobanteId;
    }

    public function getPagos($comprobante, $impPagado) {
        $sql =  "SELECT COUNT(*) as pagos, SUM(payment.amount) as totalPagado FROM  payment
        LEFT JOIN notaVenta ON payment.notaVentaId = notaVenta.notaVentaId
        WHERE notaVenta.comprobanteId = '".$comprobante["comprobanteId"]."'";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        $infoPagos = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        $impSaldoAnt = $comprobante['total'] - $infoPagos['totalPagado'];
        $impSaldoInsoluto = $impSaldoAnt - $impPagado;

        $data["numParcialidad"] = $infoPagos['pagos'] + 1;
        $data["impSaldoAnt"] = $impSaldoAnt;
        $data["impPagado"] = $impPagado;
        $data["impSaldoInsoluto"] = $impSaldoInsoluto;

        return $data;
    }
}


?>