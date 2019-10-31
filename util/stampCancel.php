<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT."/libraries.php");

$fileName ="21_C_2375.pdf";
$path = DOC_ROOT ."/empresas/21/certificados/30/facturas/pdf/".$fileName;
//chmod($path, 0777);
$pdf = new FPDI();

$pagecount = $pdf->setSourceFile($path);
$tplidx = $pdf->importPage(1, '/MediaBox');

$pdf->addPage();
$pdf->useTemplate($tplidx, 0, 0, 210);

$pdf->AddFont('verdana', '', 'verdana.php');
$pdf->SetFont('verdana', '', 72);

$pdf->SetY(100);
$pdf->SetX(10);
$pdf->SetTextColor(200, 0, 0);
$pdf->Cell(20, 10, "CANCELADO", 0, 0, 'L');
$pdf->Output($path, 'F');