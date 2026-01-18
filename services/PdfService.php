<?php
use Dompdf\Dompdf;
use Dompdf\Options;
class PdfService extends Producto{
    private $domPdf;
    private $smarty;
    private $qrService;

    public function __construct()
    {
        //$this->domPdf = new Dompdf();
        $this->smarty = new Smarty;
        $this->smarty->caching = false;
        $this->smarty->compile_check = true;

        $this->cfdiUtil = new CfdiUtil();
        $this->comprobantePago = new ComprobantePago();
        $this->qrService = new QrService();
    }

    public function generate($empresaId, $compInfo = [], $type = 'download') {
        $xmlReaderService = new XmlReaderService;

        //No se envio un nombre de archivo, buscar la serie y folio del comprobante
        if(empty($compInfo) || !is_array($compInfo)){
          echo "No se encontro informacion";
          exit;
        }

        $empresaId =$compInfo['empresaId'];
        $rfcActivo =$compInfo['rfcId'];
        if($compInfo['comprobanteId'])
            $fileName = "SIGN_".$compInfo['xml'];
        else
            $fileName = $compInfo['xml'];

        $this->setRfcId($rfcActivo);
        $rfcActivo = $this->getRfcActive();


        $this->smarty->assign('DOC_ROOT', DOC_ROOT);

        $xmlPath = DOC_ROOT.'/empresas/'.$empresaId.'/certificados/'.$rfcActivo.'/facturas/xml/'.$fileName.".xml";
         if(!file_exists($xmlPath)) {
             if($type == 'email')
                 return false;
             echo "No se encontro el documento.";
             exit(0);
         }
        $xmlData = $xmlReaderService->execute($xmlPath, $empresaId,$compInfo['comprobanteId']);
        $this->smarty->assign('xmlData', $xmlData);
        $this->smarty->assign('empresaId', $empresaId);

        $dompdf = new Dompdf();
        $dompdf->getOptions()->setChroot(DOC_ROOT."/empresas");
        $this->qrService->setRfcId($rfcActivo);
        $qrFile = $this->qrService->generate($xmlData);
        $this->smarty->assign('qrFile', $qrFile);

        $logo = "/empresas/".$empresaId."/qrs/".$xmlData['serie']["serieId"].".jpg";

        if(file_exists(DOC_ROOT.$logo)) {
            $this->smarty->assign('logo', DOC_ROOT.$logo);
        }

        $logoEscuela = DOC_ROOT."/images/header_333.jpg";

        if(file_exists($logoEscuela)) {
            $this->smarty->assign('logoEscuela', $logoEscuela);
        }

        $catalogos = $this->getFromCatalogo($xmlData, $empresaId);
        $this->smarty->assign('catalogos', $catalogos);

        $formasPagoMap = $this->mapCatalog('c_FormaPago', 'c_FormaPago', 'descripcion');
        $this->smarty->assign('formasPagoMap', $formasPagoMap);

        //Uncomment if you want to see a html version
        //$this->smarty->display(DOC_ROOT.'/templates/pdf/basico.tpl');exit;
        ob_clean();
        $html = $this->smarty->fetch(DOC_ROOT.'/templates/pdf/basico.tpl');
        $dompdf->loadHtml($html);


        $dompdf->setPaper('A4');

        $dompdf->render();

        if($type == 'download') {
            $dompdf->stream($fileName.'.pdf');
        } else if($type == 'view') {
            $dompdf->stream($fileName.'.pdf', array("Attachment" => false));
        } else {
            return $dompdf->output();
        }

        exit(0);
    }

    private function getFromCatalogo($xmlData, $empresaId){
        $sql = 'SELECT tipoDeComprobante FROM tiposComprobante
				WHERE tiposComprobanteId = "'.$xmlData["serie"]['tiposComprobanteId'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["EfectoComprobante"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT nombreRegimen FROM c_RegimenFiscal
				WHERE regimenId = "'.$xmlData["emisor"]['RegimenFiscal'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["RegimenFiscal"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT nombreRegimen FROM c_RegimenFiscal
				WHERE regimenId = "'.$xmlData["receptor"]['RegimenFiscalReceptor'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["RegimenFiscalReceptor"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT * FROM c_Impuesto';
        $this->Util()->DB()->setQuery($sql);
        $impuestos = $this->Util()->DB()->GetResult();

        $data["impuestos"] = [];
        foreach($impuestos as $key => $impuesto) {
            $data["impuestos"][$impuesto['c_Impuesto']] = $impuesto['descripcion'];
        }

        $sql = 'SELECT descripcion FROM c_FormaPago
				WHERE c_formaPago = "'.$xmlData["cfdi"]['FormaPago'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["FormaPago"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT descripcion FROM c_MetodoPago
				WHERE c_metodoPago = "'.$xmlData["cfdi"]['MetodoPago'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["MetodoPago"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT descripcion FROM c_UsoCfdi
				WHERE c_UsoCfdi = "'.$xmlData["receptor"]['UsoCFDI'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["UsoCFDI"] = mb_strtoupper($this->Util()->DB()->GetSingle());

        return $data;
    }
    private function mapCatalog($table, $keyField, $valueField) {
        $sql = 'SELECT '.$keyField.', '.$valueField.' FROM '.$table;
        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();
        $map = [];
        foreach($result as $row) {
            $map[$row[$keyField]] = $row[$valueField];
        }
        return $map;
    }
}
?>
