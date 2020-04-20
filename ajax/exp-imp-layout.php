<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
include(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST['type']){
    case 'layout-razon':
        $book =  new PHPExcel();
        $string = file_get_contents(DOC_ROOT."/properties/config_customer_contract.json");
        $headers = json_decode($string, true);
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $catalogue = $book->createSheet(1);
        $catalogue->setTitle("Responsables");
        $sheet->setTitle('layout');
        $listDepartamentos = $departamentos->GetListDepartamentos();
        $lastCol = 0;
        foreach($headers as $head){
            $sheet->setCellValueByColumnAndRow($lastCol,1, $head['name'])
                ->getCommentByColumnAndRow($lastCol,1)->getText()->createText($head['comment']);
            $lastCol++;

        }
        $current_col_catalogue = 0;
        foreach ($listDepartamentos as $dep) {
            $sheet->setCellValueByColumnAndRow($lastCol,1,'RESP.'.strtoupper($dep['departamento']));
            $personal->setDepartamentoId($dep['departamentoId']);
            $responsables = $personal->getListPersonalByDepartamento();

            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue).$current_row_catalogue;
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "RESPONSABLES DEP." . strtoupper($dep['departamento']));
            foreach ( $responsables as $item) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $item['name']);
                $current_row_catalogue++;
            }
            $current_end_range =  PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue).$current_row_catalogue--;
            $name_range = "dep_".$dep['departamentoId'];
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    $name_range,
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );

            $objList = $sheet->getCell(PHPExcel_Cell::stringFromColumnIndex($lastCol)."2")->getDataValidation();
            $objList->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
            $objList->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('El responsable no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione un responsable de la lista');
            $objList->setPrompt('Seleccione un responsable de la lista.');
            $objList->setFormula1("=$name_range"); //note this!

            $current_col_catalogue++;
            $lastCol++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= $_POST['type'].".xlsx";
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