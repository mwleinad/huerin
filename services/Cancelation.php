<?php

class Cancelation extends Main
{
    const REJECTED = 'Solicitud rechazada';
    const CANCELLED = 'Cancelado';
    public function addPetition($userId, $cfdiId, $taxPayerId, $rTaxPayerId, $uuid, $total, $cancelationMotive) {
        $this->Util()->DB()->setQuery("
			INSERT INTO
				pending_cfdi_cancel
			(
				`user_cancelation`,
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
        if($response['status'] === self::REJECTED) {
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
}
?>