<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
include(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST['type']){
    case 'layout-razon':
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
        $sheet->setCellValueByColumnAndRow(6,1,'CALLE');
        $sheet->setCellValueByColumnAndRow(7,1,'No. EXT');
        $sheet->setCellValueByColumnAndRow(8,1,'No. INT');
        $sheet->setCellValueByColumnAndRow(9,1,'COLONIA');
        $sheet->setCellValueByColumnAndRow(10,1,'MUNICIPIO');
        $sheet->setCellValueByColumnAndRow(11,1,'ESTADO');
        $sheet->setCellValueByColumnAndRow(12,1,'PAIS');
        $sheet->setCellValueByColumnAndRow(13,1,'C.P');
        $sheet->setCellValueByColumnAndRow(14,1,'METODO DE PAGO');
        $sheet->setCellValueByColumnAndRow(15,1,'No. CUENTA');
        $sheet->setCellValueByColumnAndRow(16,1,'CLAVE SIPARE');
        $sheet->setCellValueByColumnAndRow(17,1,'DIRECCION COMERCIAL');
        $sheet->setCellValueByColumnAndRow(18,1,'CONTACTO ADMINISTRATIVO');
        $sheet->setCellValueByColumnAndRow(19,1,'EMAIL ADMINISTRATIVO');
        $sheet->setCellValueByColumnAndRow(20,1,'TEL. ADMINISTRATIVO');
        $sheet->setCellValueByColumnAndRow(21,1,'CONTACTO CONTABILIDAD');
        $sheet->setCellValueByColumnAndRow(22,1,'EMAIL CONTABILIDAD');
        $sheet->setCellValueByColumnAndRow(23,1,'TEL. CONTABILIDAD');
        $sheet->setCellValueByColumnAndRow(24,1,'CONTACTO DIRECTIVO');
        $sheet->setCellValueByColumnAndRow(25,1,'EMAIL DIRECTIVO');
        $sheet->setCellValueByColumnAndRow(26,1,'TEL. DIRECTIVO');
        $sheet->setCellValueByColumnAndRow(27,1,'CELULAR DIRECTIVO');
        $sheet->setCellValueByColumnAndRow(28,1,'CLAVE FIEL');
        $sheet->setCellValueByColumnAndRow(29,1,'CLAVE CIEC');
        $sheet->setCellValueByColumnAndRow(30,1,'CLAVE IDSE');
        $sheet->setCellValueByColumnAndRow(31,1,'CLAVE ISN');
        $sheet->setCellValueByColumnAndRow(32,1,'RESP. CONTABILIDAD');
        $sheet->setCellValueByColumnAndRow(33,1,'RESP. NOMINA');
        $sheet->setCellValueByColumnAndRow(34,1,'RESP. ADMINISTRACION');
        $sheet->setCellValueByColumnAndRow(35,1,'RESP. JURIDICO ');
        $sheet->setCellValueByColumnAndRow(36,1,'RESP. IMSS');
        $sheet->setCellValueByColumnAndRow(37,1,'RESP. MENSAJERIA');
        $sheet->setCellValueByColumnAndRow(38,1,'RESP. AUDITORIA');
        $sheet->setCellValueByColumnAndRow(39,1,'TIPO DE REGIMEN');
        $sheet->setCellValueByColumnAndRow(40,1,'TIPO DE SOCIEDAD');



        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));

        $writer= PHPExcel_IOFactory::createWriter($book, 'CSV');
        foreach ($book->getAllSheets() as $sheet1) {
            // Iterating through all the columns //
            // The after Z column problem is solved by using numeric columns; thanks to the columnIndexFromString method
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }


        $nameFile= $_POST['type'].".csv";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
        break;
    case 'layout-customer':
        $book =  new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('layoutRazon');
        $sheet->setCellValueByColumnAndRow(0,1,'NOMBRE');
        $sheet->setCellValueByColumnAndRow(1,1,'TELEFONO');
        $sheet->setCellValueByColumnAndRow(2,1,'EMAIL');
        $sheet->setCellValueByColumnAndRow(3,1,'PASSWORD');
        $sheet->setCellValueByColumnAndRow(4,1,'OBSERVACIONES');
        $sheet->setCellValueByColumnAndRow(5,1,'FECHA ALTA(DIA/MES/AÑO)');
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'CSV');
        foreach ($book->getAllSheets() as $sheet1) {
            // Iterating through all the columns //
            // The after Z column problem is solved by using numeric columns; thanks to the columnIndexFromString method
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= $_POST['type'].".csv";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
    break;
    case 'layout-update-encargado':
        $book =  new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('layoutRazon');
        $sheet->setCellValueByColumnAndRow(0,1,'No.CLIENTE');
        $sheet->setCellValueByColumnAndRow(1,1,'No.CONTRATO');
        $sheet->setCellValueByColumnAndRow(2,1,'CLIENTE');
        $sheet->setCellValueByColumnAndRow(3,1,'RAZON');
        $sheet->setCellValueByColumnAndRow(4,1,'RES. CONTABILIDAD');
        $sheet->setCellValueByColumnAndRow(5,1,'RES. NOMINA');
        $sheet->setCellValueByColumnAndRow(6,1,'RES. ADMIN');
        $sheet->setCellValueByColumnAndRow(7,1,'RES. JURIDICO');
        $sheet->setCellValueByColumnAndRow(8,1,'RES. IMSS');
        $sheet->setCellValueByColumnAndRow(9,1,'RES. AUDITORIA');
        $sheet->setCellValueByColumnAndRow(10,1,'RES. DH');

        $result = $customer->GetListRazones('','',0,'Activos',false);
        $row=2;
        foreach($result as $key=>$value){
            $sheet->setCellValueByColumnAndRow(0,$row,$value['customerId']);
            $sheet->setCellValueByColumnAndRow(1,$row,$value['contractId']);
            $sheet->setCellValueByColumnAndRow(2,$row,$value['nameContact']);
            $sheet->setCellValueByColumnAndRow(3,$row,$value['name']);
            $sheet->setCellValueByColumnAndRow(4,$row,$value['nameContabilidad']);
            $sheet->setCellValueByColumnAndRow(5,$row,$value['nameNominas']);
            $sheet->setCellValueByColumnAndRow(6,$row,$value['nameAdministracion']);
            $sheet->setCellValueByColumnAndRow(7,$row,$value['nameJuridico']);
            $sheet->setCellValueByColumnAndRow(8,$row,$value['nameImss']);
            $sheet->setCellValueByColumnAndRow(9,$row,$value['nameAuditoria']);
            $sheet->setCellValueByColumnAndRow(10,$row,$value['nameDesarrollohumano']);

            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'CSV');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "layaout_update_encargados.csv";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;

    break;
    case 'layout-update-servicios':
        $book =  new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('LayoutServicios');
        $sheet->setCellValueByColumnAndRow(0,1,'ID CONTRATO');
        $sheet->setCellValueByColumnAndRow(1,1,'RAZON SOCIAL');
        $sheet->setCellValueByColumnAndRow(2,1,'ID SERVICIO');
        $sheet->setCellValueByColumnAndRow(3,1,'NOMBRE SERVICIO');
        $sheet->setCellValueByColumnAndRow(4,1,'COSTO');
        $sheet->setCellValueByColumnAndRow(5,1,'INICIO OPERACION');
        $sheet->setCellValueByColumnAndRow(6,1,'INICIO FACTURACION');
        $sheet->setCellValueByColumnAndRow(7,1,'FECHA ULTIMO WORKFLOW');
        $sheet->setCellValueByColumnAndRow(8,1,'DEPARTAMENTO');
        $sheet->setCellValueByColumnAndRow(9,1,'PERIODICIDAD');
        $sheet->setCellValueByColumnAndRow(10,1,'STATUS(activo,baja,bajaParcial,readonly)');

        $servicios =  $servicio->EnumerateServiceForInstances();
        $row=2;
        foreach($servicios as $key=>$value){
            $sheet->setCellValueByColumnAndRow(0,$row,$value['contractId']);
            $sheet->setCellValueByColumnAndRow(1,$row,$value['razonSocialName']);
            $sheet->setCellValueByColumnAndRow(2,$row,$value['servicioId']);
            $sheet->setCellValueByColumnAndRow(3,$row,$value['nombreServicio']);
            $sheet->setCellValueByColumnAndRow(4,$row,$value['costo']);
            $sheet->setCellValueByColumnAndRow(5,$row,$value['inicioOperaciones']!='0000-00-00'?date('d/m/Y',strtotime($value['inicioOperaciones'])):$value['inicioOperaciones']);
            $sheet->setCellValueByColumnAndRow(6,$row,$value['inicioFactura']!='0000-00-00'?date('d/m/Y',strtotime($value['inicioFactura'])):'0000-00-00');
            $sheet->setCellValueByColumnAndRow(7,$row,$value['status']=='bajaParcial'&&$value['status']!='0000-00-00'?date('d/m/Y',strtotime($value['lastDateWorkflow'])):'');
            $sheet->setCellValueByColumnAndRow(8,$row,$value['departamento']);
            $sheet->setCellValueByColumnAndRow(9,$row,$value['periodicidad']);
            $sheet->setCellValueByColumnAndRow(10,$row,$value['status']);
            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'CSV');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "layout_update_servicios.csv";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;

    break;
}