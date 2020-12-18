<?php

class QrService extends Producto{
    private $domPdf;
    private $smarty;

/*    public function __construct()
    {
        //$this->domPdf = new Dompdf();
        $this->smarty = new Smarty;
        $this->cfdiUtil = new CfdiUtil();
        $this->comprobantePago = new ComprobantePago();
    }*/

    function generate($xmlData)
    {
        $total = $this->Util()->RoundNumber($xmlData['cfdi']["Total"], 6);
        $total = $this->Util()->PadStringLeft(number_format($total, 6, ".", ""), 17, "0");
        $cadenaCodigoBarras = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx".
            "?re=".$xmlData['emisor']['Rfc'].
            "&rr=".$xmlData['receptor']['Rfc'].
            "&tt=".$total.
            "&fe=".substr($xmlData['timbreFiscal']['SelloCFD'], -8).
            "&id=".$xmlData['timbreFiscal']['UUID'];

        $rfcActivo = $this->getRfcActive();

        $root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/qr/";
        $web_root = WEB_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/qr/";
        $rootFacturas = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/facturas/";

        if(!is_dir($rootFacturas)){
            mkdir($rootFacturas, 0777);
        }

        if(!is_dir($root)) {
            mkdir($root, 0777);
        }

        $fileName = $_SESSION['empresaId']."_".$xmlData['cfdi']["Serie"]."_".$xmlData['cfdi']["Folio"];

        QRcode::png($cadenaCodigoBarras, $root.$fileName.".png", 'L', 4, 2);

        //This requires the full path to the url, can't use http:// or it won't show
        return $web_root.$fileName.".png";
    }
}
?>
