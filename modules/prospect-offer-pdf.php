<?php
include_once(DOC_ROOT."/services/pdfs/OfferPdf.php");

$user->allowAccess(2);
$user->allowAccess(271);
$pdfService = new OfferPdf();
$pdfService->setId($_GET["id"]);
$pdfService->generate( "pdf_resource_office",'view');