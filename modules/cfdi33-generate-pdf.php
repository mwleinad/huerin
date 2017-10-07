<?php
//$empresa->AuthUser();

$info = $empresa->Info();
$smarty->assign("info", $info);

if(!$_GET['filename']) {
    echo "No hay nombre de archivo";
    print_r($_GET);
    exit;
}

include_once(DOC_ROOT."/services/PdfService.php");
include_once(DOC_ROOT."/services/QrService.php");
include_once(DOC_ROOT."/services/XmlReaderService.php");

$pdfService = new PdfService;

$pdfService->generate($info["empresaId"], $_GET['filename'], $_GET['type']);