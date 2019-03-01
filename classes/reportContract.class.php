<?php
use Dompdf\Dompdf;

class ReportContract extends Contract
{
    function generateReportCompletePdfByContract($type='save'){
        global $contractRep;
        $sql = "select a.contractId,a.name,a.rfc,a.address,a.noExtAddress,a.noIntAddress,a.coloniaAddress,a.estadoAddress,a.paisAddress,a.cpAddress,b.nameContact
                from contract a inner join customer b on a.customerId=b.customerId where contractId='".$this->getContractId()."' ";
        $this->Util()->DB()->setQuery($sql);
        $contrato = $this->Util()->DB()->GetRow();
        if(!$contrato)
            return false;

        $sql =  "select a.costo,a.inicioFactura,a.inicioOperaciones,a.status,b.nombreServicio from servicio a
                 inner join tipoServicio b on a.tipoServicioId=b.tipoServicioId  and b.status='1'
                 where a.contractId='".$contrato['contractId']."' order by b.nombreServicio asc  ";
        $this->Util()->DB()->setQuery($sql);
        $servicios =  $this->Util()->DB()->GetResult();

        $encargados =  $contractRep->encargadosArea($contrato['contractId']);

        $this->Util()->Smarty()->assign("contrato",$contrato);
        $this->Util()->Smarty()->assign("servicios",$servicios);
        $this->Util()->Smarty()->assign("encargados",$encargados);
        $html =  $this->Util()->Smarty()->fetch(DOC_ROOT."/templates/molds/pdf-log-contract-servicio.tpl");
        $dompdf =  new Dompdf();
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $fileName  = str_replace(" ","",$contrato['name']);
        if($type == 'download') {
            $dompdf->stream($fileName.'.pdf');
        } else if($type == 'view') {
            $dompdf->stream($fileName.'.pdf', array("Attachment" => false));
        } else {
           $output =  $dompdf->output();
            file_put_contents(DOC_ROOT."/sendFiles/$fileName.pdf", $output);

        }
    }
}