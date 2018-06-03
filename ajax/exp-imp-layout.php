<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST['type']){
    case 'imp-razon':
        include(DOC_ROOT.'/libs/excel/PHPExcel.php');

        $book =  new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('layoutRazon');
        $sheet->setCellValueByColumnAndRow(0,1,'NOMBRE CLIENTE');
        $sheet->setCellValueByColumnAndRow(1,1,'NOMBRE RAZON SOCIAL');
        $sheet->setCellValueByColumnAndRow(2,1,'TIPO DE PERSONA');
        $sheet->setCellValueByColumnAndRow(3,1,'FACTURADOR');
        $sheet->setCellValueByColumnAndRow(4,1,'RFC');
        $sheet->setCellValueByColumnAndRow(5,1,'NOMBRE COMERCIAL');
        $sheet->setCellValueByColumnAndRow(6,1,'CALLE(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(7,1,'No. EXT(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(8,1,'No. INT(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(9,1,'COLONIA(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(10,1,'MUNICIPIO(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(11,1,'ESTADO(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(12,1,'PAIS(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(13,1,'C.P(DIR. FISCAL)');
        $sheet->setCellValueByColumnAndRow(14,1,'METODO DE PAGO');
        $sheet->setCellValueByColumnAndRow(15,1,'No. CUENTA');
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));

        $writer= PHPExcel_IOFactory::createWriter($book, 'CSV');
        foreach ($book->getAllSheets() as $sheet1) {
            // Iterating through all the columns //
            // The after Z column problem is solved by using numeric columns; thanks to the columnIndexFromString method
            for ($col = 0; $col <= PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }


        $nameFile= $_POST['type'].".csv";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
        break;
}