<?php
include_once(DOC_ROOT."/services/pdfs/OfferExport.php");
$user->allowAccess(2);
$user->allowAccess(271);
$pdfService = new OfferExport();
$pdfService->setId($_GET["id"]);
$pdfService->generate();