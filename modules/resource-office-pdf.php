<?php
//$empresa->AuthUser();

include_once(DOC_ROOT."/services/pdfs/ResourceOffice.php");

$pdfService = new ResourceOffice();
$pdfService->setId($_GET["id"]);
$pdfService->generate( "pdf_resource_office",$_GET['type']);