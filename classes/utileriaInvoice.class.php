<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 11/06/2019
 * Time: 11:59 PM
 */

class UtileriaInvoice extends Comprobante
{
    private $listInvoicesActiveInSat = [];
    function setListInvoicesActiveInSat($value){
        if(!is_array($value))
            $value = [];
        $this->listInvoicesActiveInSat = $value;
    }
    function getListInvoicesActiveInSat(){
        return $this->listInvoicesActiveInSat;
    }
    function countInvoicesActiveInSat(){
        $total = count($this->listInvoicesActiveInSat);
        $this->Util()->setError(0,"complete","$total facturas encontradas con status de cancelado en plataforma y activo en el SAT.");
        $this->Util()->PrintErrors();
        return true;
    }
    function cancelInvoiceInSatByFolio($serie,$folio){
        $sql = "select a.comprobanteId,concat(a.serie,a.folio) as folio,a.fecha,a.total,a.xml,a.status,a.empresaId,a.version,a.timbreFiscal,a.noCertificado,a.tiposComprobanteId,b.name,b.rfc,b.type as tipoPersona from comprobante a 
                inner join contract b on a.userId=b.contractId
                where a.serie='$serie' and a.folio='$folio' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        if(!$row){
            $this->Util()->setError(0,"error","No se encontro informacion con los parametros proporcionados");
            $this->Util()->PrintErrors();
            return false;
        }
       $this->findStatusInvoiceByDocument($row);
       $this->handleCancelationInvoiceActiveInSat();
    }
    function checkStatusInSat($serie,$folio){
        $sql = "select a.comprobanteId,concat(a.serie,a.folio) as folio, a.rfcId, a.fecha,a.total,a.xml,a.status,a.empresaId,a.version,a.timbreFiscal,a.noCertificado,a.tiposComprobanteId,b.name,b.rfc,b.type as tipoPersona from comprobante a 
                inner join contract b on a.userId=b.contractId
                where a.serie='$serie' and a.folio='$folio' ";
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();
        if(!$row){
            $this->Util()->setError(0,"error","No se encontro informacion con los parametros proporcionados");
            $this->Util()->PrintErrors();
            return false;
        }
        $response = $this->findStatusInvoiceByDocument($row);
        switch($response["getCFDiStatusReturn"]["status"]){
            case 'Vigente';
                 $mensaje = "La factura con folio $serie$folio se encuentra activa en el SAT";
                 $typeMsj =  "complete";
                 /*if($_POST["cancelar"]){
                     $mensaje .="Y se a realizado la cancelacion correctamente";
                 }*/
            break;
            case 'Cancelado':
                $mensaje = "La factura con folio $serie$folio se encuentra cancelada en el SAT";
                $typeMsj =  "error";
            break;
            default:
                $mensaje = "Error al solicitar status en el SAT";
                $typeMsj =  "error";
            break;
        }
        $this->setListInvoicesActiveInSat([]);
        $this->Util()->setError(0,$typeMsj,$mensaje);
        $this->Util()->PrintErrors();
        return true;
    }
    function findStatusInvoiceByDocument($datos){
        global $rfc;
        switch($datos["empresaId"]){
            case 15: $rfcActivo = 1; break;
            case 21: $rfcActivo = 30; break;
            default: $rfcActivo = 1; break;
        }
        $sql = "select noCertificado from serie where rfcId = '".$datos['rfcId']."' limit 1";
        $this->Util()->DB()->setQuery($sql);
        $datos['noCertificado'] = $this->Util()->DB()->GetSingle();

        $rfc->setRfcId($rfcActivo);
        $rfcEmisor = $rfc->InfoRfc();
        $uuid = "";
        $xml = $datos["xml"];
        $empresaId = $datos["empresaId"];
        $timbreUnserialize = unserialize($datos["timbreFiscal"]);
        if(!is_array($timbreUnserialize))
            $timbreUnserialize = [];

        if($timbreUnserialize["UUID"]!=""){
           $uuid = $timbreUnserialize["UUID"];
        }else{
           echo $root = DOC_ROOT."/empresas/$empresaId/certificados/$rfcActivo/facturas/xml/SIGN_$xml.xml";
            if(!file_exists($root))
                return false;

            $fh = fopen($root, 'r');
            $data = fread($fh, filesize($root));
            fclose($fh);
            $data = explode("UUID", $data);
            if($data['tiposComprobanteId']==10)
                $data = $data[2];
            else
                $data = $data[1];
            $data = explode("FechaTimbrado", $data);
            $data = $data[0];
            $uuid = str_replace("\"", "", $data);
            $uuid = str_replace("=", "", $uuid);
            $uuid = str_replace(" ", "", $uuid);
            $uuid = substr($uuid, 0, 36);
        }
        if($uuid=="")
            return false;
        //certificados
        $path = DOC_ROOT."/empresas/$empresaId/certificados/$rfcActivo/".$datos["noCertificado"].".cer.pfx";
        if(!file_exists($path))
            return false;
        //get password
        $root_password = DOC_ROOT."/empresas/$empresaId/certificados/$rfcActivo/password.txt";
        if(!file_exists($root_password))
            return false;
        $fh = fopen($root_password, 'r');
        $password = fread($fh, filesize($root_password));
        fclose($fh);

        $pac = new Pac;
        $cad["path"] = $path;
        $cad["comprobanteId"] = $datos["comprobanteId"];
        $cad["rfcEmisor"] = $rfcEmisor["rfc"];
        $cad["rfc"] =$datos["rfc"];
        $cad["uuid"] = $uuid;
        $cad["total"]=$datos["total"];
        $cad["password"] = $password;
        $response = $pac->getStatusCfdi(USER_PAC,PW_PAC,$rfcEmisor["rfc"],$datos["rfc"],$uuid,$datos["total"],$path,$password);
        if($response["getCFDiStatusReturn"]["status"]=='Vigente')
                $this->listInvoicesActiveInSat[] = $cad;

        return $response;
    }
    function findInvoicesActiveInSat(){
        $invoices = $this->getListGeneralComprobantes(0,1,2017);
        foreach($invoices as $key=>$value){
           $response = $this->findStatusInvoiceByDocument($value);
        }
    }
    function handleCancelationInvoiceActiveInSat(){
        global $cancelation;
        $invoices = $this->getListInvoicesActiveInSat();
        if(count($invoices)<=0)
        {
            $this->Util()->setError(0,"complete","0 facturas canceladas, por tener status correcto en plataforma y SAT");
            $this->Util()->PrintErrors();
            return false;
        }
        $cancelados =0;
        $noCancelados =0;

        $pac = new Pac();
        foreach($invoices as $key=>$value){
            $response = $pac->CancelaCfdi2018(USER_PAC,PW_PAC,$value["rfcEmisor"],$value["rfc"],$value["uuid"],$value["total"],$value["path"],$value["password"]);
            dd($response);
            if($response['cancelado'])
            {
                $motivo_cancelacion = "Se solicita nuevamente la cancelacion, documento aun vigente en el SAT y en plataforma interna ya se encuentra cancelada";
                if($response['conAceptacion']){
                    $cancelation->addPetition($_SESSION['User']['userId'],$value["comprobanteId"],$value["rfcEmisor"],$value['rfc'],$value["uuid"],$value['total'],$motivo_cancelacion);
                }else{
                    $sqlQuery = 'UPDATE comprobante SET motivoCancelacion = "'.$motivo_cancelacion.'", status = "0", fechaPedimento = "'.date("Y-m-d").'",usuarioCancelacion="'.$_SESSION['User']['userId'].'" WHERE comprobanteId = '.$value["comprobanteId"];
                    $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
                    $this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
                }
                $cancelados++;
            }else{
                $noCancelados++;
            }
        }
        $this->setListInvoicesActiveInSat([]);
        $this->Util()->setError(0,"complete","$cancelados facturas canceladas correctamente");
        $this->Util()->setError(0,"complete","$noCancelados facturas sin cancelar");
        $this->Util()->PrintErrors();
        return true;

    }

}
