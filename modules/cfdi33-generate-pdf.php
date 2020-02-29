<?php
//$empresa->AuthUser();
$info = $empresa->Info();
$smarty->assign("info", $info);

if(!$_GET['identifier']) {
    echo "No hay un identificador de archivo";
    exit;
}
$identifier = $_GET['identifier'];
$compInfo = [];
if(!is_numeric($identifier)){
    $fileName = $_GET['filename'];
    $base_root = str_replace("_","/",$identifier);
    $file = DOC_ROOT."/empresas/".$base_root."/".$fileName.".xml";
    if(is_file($file)){
        $explode_identifier = explode("_",$identifier);
        $compInfo['xml']= $fileName;
        $compInfo['empresaId'] = $explode_identifier[0];
        $compInfo['rfcId'] = $explode_identifier[2];
        $compInfo['comprobanteId'] = 0;
    }
}else{
    $compInfo = $comprobante->GetInfoComprobante($_GET['identifier']);
}
include_once(DOC_ROOT."/services/PdfService.php");
include_once(DOC_ROOT."/services/QrService.php");
include_once(DOC_ROOT."/services/XmlReaderService.php");

$pdfService = new PdfService;
$pdfService->generate($info["empresaId"], $compInfo, $_GET['type']);