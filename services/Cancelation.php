<?php

class Cancelation extends Main
{
    const REJECTED = 'Solicitud rechazada';
    const CANCELLED = 'Cancelado';
    const NOCANCELABLE = 'No cancelable';
    public function addPetition($userId, $cfdiId, $taxPayerId, $rTaxPayerId, $uuid, $total, $cancelationMotive){
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
				`cancelation_motive`

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
				'".$cancelationMotive."'

		);");
        return $this->Util()->DB()->InsertData();
    }
    public function getStatus($rfcE, $rfcR, $uuid, $total) {
        require_once(DOC_ROOT.'/libs/nusoap.php');
        $client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
        $client->useHTTPPersistentConnection();

        if(PROJECT_STATUS == "test")
            $isTest = true;
        else
            $isTest = false;

        $params = array(
            'user' => USER_PAC,
            'password' => PW_PAC,
            'rfcE' => $rfcE,
            'rfcR' => $rfcR,
            'uuid' => $uuid,
            'total' => $total,
            'test'=>$isTest
        );
        $status = $client->call('getCFDiStatus', $params, 'http://cfdi.service.ediwinws.edicom.com/');
        return $status['getCFDiStatusReturn'];
    }
    public function processCancelation($cfdi, $response) {
        if($response['status'] === self::REJECTED || $response['isCancelable'] === self::NOCANCELABLE) {
            $this->deleteCancelRequest($cfdi["solicitud_cancelacion_id"]);
        }
        if($response['status']=== self::CANCELLED) {
            $date = date("Y-m-d");
            $sqlQuery = "UPDATE comprobante SET
                              motivoCancelacion = '".$cfdi['cancelation_motive']."', 
                              status = '0', 
                              fechaPedimento = '".date("Y-m-d")."',
                              usuarioCancelacion='".$cfdi['user_cancelation']."'
                              WHERE comprobanteId = '".$cfdi['cfdi_id']."' ";
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sqlQuery);
            $this->Util()->DBSelect($_SESSION['empresaId'])->UpdateData();
            $this->updateInstanciaIfExist($cfdi['cfdi_id']);
            $this->deleteCancelRequest($cfdi["solicitud_cancelacion_id"]);
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
            $sQuery = "select instanciaServicioId from instanciaServicio where comprobanteId = '".$id."' ";
            $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sQuery);
            $workIds = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();
            $workIds = count($workIds) > 0 ? array_column($workIds, 'instanciaServicioId') : [];
            if (count($workIds)) {
                $sQueryUp =  "update instanciaServicio set comprobanteId= '0' where instanciaServicioId  IN (".implode(',', $workIds).") ";
                $this->Util()->DBSelect($_SESSION['empresaId'])->setQuery($sQueryUp);
                $rowsAffects = $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
                if($rowsAffects)
                    $servicio->resetDateLastProcessInvoice($row['userId']);
            }
        }
        return true;
    }
}
?>
