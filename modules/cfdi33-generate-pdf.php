<?php
//$empresa->AuthUser();

$info = $empresa->Info();
$smarty->assign("info", $info);

if(!$_GET['identifier']) {
    echo "No hay un identificador de archivo";
    print_r($_GET);
    exit;
}

include_once(DOC_ROOT."/services/PdfService.php");
include_once(DOC_ROOT."/services/QrService.php");
include_once(DOC_ROOT."/services/XmlReaderService.php");

$pdfService = new PdfService;
$compInfo = $comprobante->GetInfoComprobante($_GET['identifier']);
$pdfService->generate($info["empresaId"], $compInfo, $_GET['type']);