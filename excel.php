<?php

//	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}

	include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
	include_once(DOC_ROOT.'/libs/excel/PHPExcel/Calculation.php');
	include_once(DOC_ROOT.'/libs/excel/PHPExcel/Cell.php');

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Avantika DS")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Exportacion en Excel")
							 ->setSubject("Exportacion en Excel");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", "test");

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Reporte de');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;