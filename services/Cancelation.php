<?php

class Cancelation extends Main
{
    const REJECTED = 'Solicitud rechazada';
    const CANCELLED = 'Cancelado';
    const NOCANCELABLE = 'No cancelable';
    public function addPetition($userId, $cfdiId, $taxPayerId, $rTaxPayerId, $uuid, $total, $cancelationMotiveSat, $uuidSubstitution, $cancelationMotive){
        $sql = "delete from pending_cfdi_cancel where cfdi_id = '".$cfdiId."' ";
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->DeleteData();

        $this->Util()->DB()->setQuery("
			INSERT INTO
				pending_cfdi_cancel
			(
				`user_cancelation`,
				`date_petition`,
				`cfdi_id`,
				`rfc_e`,
				`rfc_r`,
				`uuid`,
				`total`,
				`cancelation_motive`,
				`cancelation_motive_sat`,
				`uuid_substitution`
		    )
            VALUES
		    (

				'".$userId."',
				'".date("Y-m-d H:i:s")."',
				'".$cfdiId."',
				'".$taxPayerId."',
				'".$rTaxPayerId."',
				'".$uuid."',
				'".$total."',
				'".$cancelationMotive."',
				'".$cancelationMotiveSat."',
				'".$uuidSubstitution."'

		);");
        return $this->Util()->DB()->InsertData();
    }
    public function getStatus($rfcE, $rfcR, $uuid, $total) {

        $qr="?re=$rfcE&rr=$rfcR&tt=$total&id=$uuid";
        $consulta= array('expresionImpresa'=>$qr);

        try {
            $client = new SoapClient('https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?WSDL');
            $response =  $client->Consulta($consulta);
            return $response;
        } catch( Throwable $e ) {
            return false;
        }
    }
    public function processCancelation($cfdi, $response) {
        global $servicio;

        if(!$response)
            return false;

        if($response->ConsultaResult->EstatusCancelacion === self::REJECTED || $response->ConsultaResult->EsCancelable === self::NOCANCELABLE) {
            $this->deleteCancelRequest($cfdi["solicitud_cancelacion_id"]);
        }
        if($response->ConsultaResult->Estado === self::CANCELLED) {

            $sqlQuery = "UPDATE comprobante SET
                              motivoCancelacion = '".$cfdi['cancelation_motive']."', 
                              motivoCancelacionSat = '".$cfdi['cancelation_motive_sat']."',
                              uuidSustitucion = '".$cfdi['uuid_substitution']."',
                              status = '0', 
                              fechaPedimento = '".date("Y-m-d", strtotime($cfdi['date_petition']))."',
                              fechaCancelacion = '".date("Y-m-d")."',
                              usuarioCancelacion='".$cfdi['user_cancelation']."'
                              WHERE comprobanteId = '".$cfdi['cfdi_id']."' ";
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlQuery);
            $affects = $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();

            $sQuery = "SELECT fecha, comprobanteId, userId FROM comprobante WHERE comprobanteId = '".$cfdi['cfdi_id']."' ";
            $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sQuery);
            $row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
            if($row)
                $servicio->resetDateLastProcessInvoice($row['userId']);
            if($affects > 0) {
                $this->updateInstanciaIfExist($cfdi['cfdi_id']);
                $this->deleteCancelRequest($cfdi["solicitud_cancelacion_id"]);
            }
        }
    }

    private function deleteCancelRequest($id) {
        $this->Util()->DB()->setQuery("
			DELETE FROM
				pending_cfdi_cancel
			WHERE
				solicitud_cancelacion_id = '".$id."'");
        $this->Util()->DB()->DeleteData();
    }

    /*
     * set comprobanteId inside instanciaServicio table and reset lastProccessInvoice='0000-00-00' inside contract table
     * @param $id
     * return true | false
     */
    public function updateInstanciaIfExist ($id) {
        global $servicio;
        if(!$id)
            return false;

        $sQuery = "SELECT fecha, comprobanteId, userId FROM comprobante WHERE comprobanteId = '".$id."' ";
        $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sQuery);
        $row = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

        $firstDayCurrentMonth =  $this->Util()->getFirstDate(date('Y-m-d'));
        $lastDayCurrentMonth = date('Y')."-".date('m')."-".$this->Util()->getLastDayMonth((int)date('Y'), (int)date('m'));
        $firstDayDateInvoice = $this->Util()->getFirstDate(date('Y-m-d', strtotime($row['fecha'])));

        if($firstDayCurrentMonth === $firstDayDateInvoice && date('Y-m-d') != $lastDayCurrentMonth ) {
            $sQuery = "update instanciaServicio set comprobanteId = 0 WHERE comprobanteId = '".$id."' ";
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sQuery);
            $rowsAffects = $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
            if($rowsAffects)
                $servicio->resetDateLastProcessInvoice($row['userId']);
        }
        return true;
    }
}
?>
