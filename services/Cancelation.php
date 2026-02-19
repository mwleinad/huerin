<?php

class Cancelation extends Main
{
    const REJECTED = 'Solicitud rechazada';
    const CANCELLED = 'Cancelado';
    const CANCELLED_WITHOUT_ACCEPT = 'Cancelado con aceptación';
    const CANCELLED_WITH_ACCEPT = 'Cancelado sin aceptación';
    const NOCANCELABLE = 'No cancelable';
    public function addPetition($userId, $cfdiId, $taxPayerId, $rTaxPayerId, $uuid, $total, $cancelationMotiveSat, $uuidSubstitution, $cancelationMotive, $status = CFDI_CANCEL_STATUS_PENDING){
        // Verificar si ya existe una petición pendiente (no eliminada, las eliminadas no cuentan por que son rechazos, pero si computan en intentos) para el mismo cfdiId
        $sql = "SELECT solicitud_cancelacion_id, attempts FROM pending_cfdi_cancel WHERE cfdi_id = '".$cfdiId."' AND deleted_at IS NULL";
        $this->Util()->DB()->setQuery($sql);
        $existingPetition = $this->Util()->DB()->GetRow();
        
        if($existingPetition) {
          
            // Incrementar intentos y actualizar (incluyendo status si es diferente de pending)
            $updateSql = "UPDATE pending_cfdi_cancel SET 
                            attempts = attempts + 1,
                            last_attempt_at = '".date("Y-m-d H:i:s")."',
                            cancelation_motive = '".$cancelationMotive."',
                            cancelation_motive_sat = '".$cancelationMotiveSat."',
                            uuid_substitution = '".$uuidSubstitution."',
                            status = '".$status."'
                          WHERE cfdi_id = '".$cfdiId."' AND deleted_at IS NULL";
            $this->Util()->DB()->setQuery($updateSql);
            return $this->Util()->DB()->UpdateData();
        } else {
            // Si no existe, crear nuevo registro
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
                    `uuid_substitution`,
                    `attempts`,
                    `last_attempt_at`,
                    `status`
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
                    '".$uuidSubstitution."',
                    1,
                    '".date("Y-m-d H:i:s")."',
                    '".$status."'
                );");
            return $this->Util()->DB()->InsertData();
        }
    }
    
    // Sumar intentos rechazados eliminados por que FINKOK los contabiliza
    public function getCancelationAttempts($cfdiId) {
        $sql = "SELECT SUM(attempts) AS attempts FROM pending_cfdi_cancel WHERE cfdi_id = '".$cfdiId."' GROUP BY cfdi_id";
        $this->Util()->DB()->setQuery($sql);
        $attempts = $this->Util()->DB()->GetSingle();
        
        return (int) $attempts;
    }
    
    // Método para obtener el historial completo de intentos de cancelación (incluyendo eliminados)
    public function getCancelationHistory($cfdiId) {
        $sql = "SELECT * FROM pending_cfdi_cancel WHERE cfdi_id = '".$cfdiId."' ORDER BY date_petition DESC";
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetResult();
    }
    
    // Método para obtener solo peticiones activas (no eliminadas)
    public function getActiveCancelationPetitions($cfdiId = null) {
        $sql = "SELECT * FROM pending_cfdi_cancel WHERE deleted_at IS NULL";
        if($cfdiId) {
            $sql .= " AND cfdi_id = '".$cfdiId."'";
        }
        $sql .= " ORDER BY date_petition DESC";
        $this->Util()->DB()->setQuery($sql);
        return $cfdiId ? $this->Util()->DB()->GetRow() : $this->Util()->DB()->GetResult();
    }
    
    // Método para restaurar una petición eliminada (si es necesario)
    public function restoreCancelRequest($id) {
        $this->Util()->DB()->setQuery("
            UPDATE
                pending_cfdi_cancel
            SET
                deleted_at = NULL
            WHERE
                solicitud_cancelacion_id = '".$id."'
        ");
        return $this->Util()->DB()->UpdateData();
    }
    
    public function getStatus($rfcE, $rfcR, $uuid, $total) {

        try {

            if(empty($rfcE) || empty($rfcR) || empty($uuid) || empty($total)) {
                throw new Exception('Datos insuficientes para consultar el estatus de cancelación.');
            }
      
            if(PROJECT_STATUS == 'test') {
                return (object) [
                    'ConsultaResult' => (object) [
                        'EstatusCancelacion' => 'S - Comprobante obtenido satisfactoriamente.',
                        'EsCancelable' =>'Cancelable con aceptación',
                        'Estado' => 'Vigente',
                        'ValidacionEFOS' => '200',
                    ]
                ];
            }

            $qr="?re=$rfcE&rr=$rfcR&tt=$total&id=$uuid";
            $consulta= array('expresionImpresa'=>$qr);
            $client = new SoapClient('https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?WSDL');
            $response =  $client->Consulta($consulta);
            
            return $response;
        } catch( Throwable $e ) {
                echo "Error al consultar el estatus de cancelación: " . $e->getMessage() . "\n";
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
        if($response->ConsultaResult->Estado === self::CANCELLED
            || $response->ConsultaResult->Estado === self::CANCELLED_WITHOUT_ACCEPT
            || $response->ConsultaResult->Estado === self::CANCELLED_WITH_ACCEPT) {

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
                //$this->updatePaymentIfExists($cfdi['cfdi_id']);
                $this->deleteCancelRequest($cfdi["solicitud_cancelacion_id"]);
            }

        }
    }

    private function deleteCancelRequest($id) {
        $this->Util()->DB()->setQuery("
			UPDATE
				pending_cfdi_cancel
			SET
				deleted_at = '".date("Y-m-d H:i:s")."'
			WHERE
				solicitud_cancelacion_id = '".$id."'");
        $this->Util()->DB()->UpdateData();
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

    public function updatePaymentIfExists($id) {

        $this->Util()->DB()->setQuery("UPDATE payment set paymentStatus='cancelado' WHERE comprobantePagoId = '".$id."'");
        $this->Util()->DB()->UpdateData();
    }
}
?>
