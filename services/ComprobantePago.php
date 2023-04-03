<?php
class ComprobantePago extends Comprobante {

    private function generateSerieIfNotExists() {
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE rfcId = '".$this->getRfcId()."'");
        $serieExistente = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        //Create series if it doesn't exists TODO do not let this serie to be deleted or modified, also hide it from other places
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM serie WHERE tiposComprobanteId = 10 AND rfcId = '".$this->getRfcId()."'");
        $serie = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        if(!$serie) {

            $vs = new User;
            $vs->setRfcId($this->getRfcId());
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
				`rfcId`
			) VALUES
			(
				'".$_SESSION["empresaId"]."',
				'1',
				'".$serieExistente['serie']."_COMPAGO',
				'1',
				'999999999',
				'10',
				'1',
				'".$serieExistente['noCertificado']."',
				'1',
				'".$activeRfc."'
			)");
            echo $this->Util()->DBSelect($_SESSION["empresaId"])->query;
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

        $_SESSION["empresaId"] = $infoComprobante['empresaId'];
        $this->setRfcId($infoComprobante['rfcId']);
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
            'cfdiRelacionadoId' => $comprobante["comprobanteId"],
            'tipoRelacion' => '04',
            'userId' => $comprobante['userId'],
            'usoCfdi' =>  'CP01', // quitar $comprobante['version'] == '3.3' ? 'P01' :
            'formaDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'metodoDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'infoPago' => $infoPago,
            'tiposDeMonedaPago' => $comprobante['tipoDeMoneda'],
            'tiposDeCambioPago' => $comprobante['tipoDeCambio'],
            'versionDR' => $comprobante['version'], // control interno para aplicar complemento 1.0 o 2.0
        ];

        if(!$comprobanteId = $cfdi->Generar($data)) {
            return null;
        }

        return $comprobanteId;
    }
    public function generarFromXml($infoComprobante, $infoPago){
        $cfdi = new Cfdi();

        $_SESSION["empresaId"] = $infoComprobante['empresaId'];

        $serieId = $this->generateSerieIfNotExists();

        $this->addConcept();
        $data = [
            'formatoNormal' => 0,
            'tiposComprobanteId' => '10-'.$serieId,
            'tiposDeMoneda' => 'XXX',
            'cfdiRelacionadoSerie' => $infoComprobante["serie"],
            'cfdiRelacionadoFolio' => $infoComprobante["folio"],
            'tipoRelacion' => '04',
            'userId' => $infoComprobante['userId'],
            'usoCfdi' => 'P01',
            'formaDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'metodoDePago' => 'NO DEBE EXISTIR', //esto lo quita en la clase xml, pero la clase cfdi espera un valor
            'infoPago' => $infoPago,
            'tiposDeMonedaPago' => $infoComprobante['tipoDeMoneda'],
            'tiposDeCambioPago' => $infoComprobante['tipoDeCambio'],
            'fromXml'=>true,
            'uuid'=>$infoComprobante['uuid'],
            'totalFactura'=>$infoComprobante['total'],
        ];
        if(!$comprobanteId = $cfdi->Generar($data)) {
            return null;
        }

        return $comprobanteId;
    }

    public function getPagos($comprobante, $impPagado) {
        $sql =  "SELECT COUNT(*) as pagos, SUM(payment.amount) as totalPagado FROM  payment 
        LEFT JOIN comprobante ON payment.comprobanteId = comprobante.comprobanteId
        WHERE comprobante.comprobanteId = '".$comprobante["comprobanteId"]."' and payment.paymentStatus='activo' ";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        $infoPagos = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        $impSaldoAnt = $comprobante['total'] - $infoPagos['totalPagado'];
        $impSaldoInsoluto = $impSaldoAnt - $impPagado;

        $data["numParcialidad"] = $infoPagos['pagos'] + 1;
        $data["impSaldoAnt"] = $impSaldoAnt;
        $data["impPagado"] = $impPagado;
        $data["impSaldoInsoluto"] = $impSaldoInsoluto;

        if($data["impSaldoInsoluto"] < 0){
            $data["impSaldoInsoluto"] = 0;
        }

        return $data;
    }
    public function getPagosFromXml($comprobante, $impPagado) {
        $sql =  "SELECT COUNT(*) as pagos, SUM(amount) as totalPagado FROM  payment_from_xml
        WHERE uuid = '".$comprobante["uuid"]."' and payment_status='activo' ";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql);
        $infoPagos = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        //pagos2
        $sql2 =  "SELECT COUNT(*) as pagos, SUM(amount) as totalPagado FROM  payment_from_xml_static
        WHERE uuid = '".$comprobante["uuid"]."' and payment_status='activo' ";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sql2);
        $infoPagos2 = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        $totalPagado = $infoPagos['totalPagado']+$infoPagos2['totalPagado'];

        $impSaldoAnt = $comprobante['totalFactura'] - $totalPagado;
        $impSaldoInsoluto = $impSaldoAnt - $impPagado;

        $data["numParcialidad"] = $infoPagos['pagos'] + $infoPagos2['pagos'] + 1;
        $data["impSaldoAnt"] = $impSaldoAnt;
        $data["impPagado"] = $impPagado;
        $data["impSaldoInsoluto"] = $impSaldoInsoluto;

        if($data["impSaldoInsoluto"] < 0){
            $data["impSaldoInsoluto"] = 0;
        }

        return $data;
    }
}


?>
