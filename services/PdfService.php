<?php
use Dompdf\Dompdf;

class PdfService extends Producto{
    private $domPdf;
    private $smarty;

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

    public function generate($empresaId, $fileName, $type = 'download') {

        $xmlReaderService = new XmlReaderService;

        //No se envio un nombre de archivo, buscar la serie y folio del comprobante
        if(strpos($fileName, 'UID') !== false){
            $fileName = $this->cfdiUtil->getFilename($fileName);
        }

        $empresaId = explode("_", $fileName);

        if($empresaId[0] == 'SIGN'){
            $empresaId = $empresaId[1];
        } else {
            $empresaId = $empresaId[0];
        }

        $_SESSION['empresaId'] = $empresaId;
        $rfcActivo = $this->getRfcActive();


        $this->smarty->assign('DOC_ROOT', DOC_ROOT);

        $xmlPath = DOC_ROOT.'/empresas/'.$empresaId.'/certificados/'.$rfcActivo.'/facturas/xml/'.$fileName.".xml";
        $xmlData = $xmlReaderService->execute($xmlPath, $empresaId);
        $this->smarty->assign('xmlData', $xmlData);
        $this->smarty->assign('empresaId', $empresaId);

        $dompdf = new Dompdf(array(
            'debugLayout' => true,));

        $qrFile = $this->qrService->generate($xmlData);
        $this->smarty->assign('qrFile', $qrFile);

        $logo = DOC_ROOT."/empresas/".$empresaId."/qrs/".$xmlData['serie']["serieId"].".jpg";

        if(file_exists($logo)) {
            $this->smarty->assign('logo', $logo);
        }

        $logoEscuela = DOC_ROOT."/images/header_333.jpg";

        if(file_exists($logoEscuela)) {
            $this->smarty->assign('logoEscuela', $logoEscuela);
        }

        $catalogos = $this->getFromCatalogo($xmlData, $empresaId);
        $this->smarty->assign('catalogos', $catalogos);

        //Uncomment if you want to see a html version
        //$this->smarty->display(DOC_ROOT.'/templates/pdf/basico.tpl');exit;

        $html = $this->smarty->fetch(DOC_ROOT.'/templates/pdf/basico.tpl');
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

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
        $data["EfectoComprobante"] = strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT nombreRegimen FROM tipoRegimen
				WHERE claveRegimen = "'.$xmlData["emisor"]['RegimenFiscal'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["RegimenFiscal"] = strtoupper($this->Util()->DB()->GetSingle());

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
        $data["FormaPago"] = strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT descripcion FROM c_MetodoPago
				WHERE c_metodoPago = "'.$xmlData["cfdi"]['MetodoPago'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["MetodoPago"] = strtoupper($this->Util()->DB()->GetSingle());

        $sql = 'SELECT descripcion FROM c_UsoCfdi
				WHERE c_UsoCfdi = "'.$xmlData["receptor"]['UsoCFDI'].'"';
        $this->Util()->DB()->setQuery($sql);
        $data["UsoCFDI"] = strtoupper($this->Util()->DB()->GetSingle());

        return $data;
    }
}
?>
