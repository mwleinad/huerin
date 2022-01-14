<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST['type']){
    case 'generate_layout':
        $book =  new PHPExcel();
        $cat=  new Catalogue();
        $tipo = $_POST['tipo'];
        $string = file_get_contents(DOC_ROOT."/properties/config_layout_".$tipo.".json");
        $headers = json_decode($string, true);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $catalogue = $book->createSheet(1);
        $catalogue->setTitle("Datos");
        $sheet->setTitle('Layout');
        $list_departamentos = $tipo === 'add_contract' ?  $departamentos->GetListDepartamentos()  : [];
        $lastCol = 0;
        $margin_left_comment = 10;
        $current_col_catalogue = 0;
        $other_ranges = [];
        foreach($headers as $head) {
            $col_string = PHPExcel_Cell::stringFromColumnIndex($lastCol);
            $sheet->getStyleByColumnAndRow($lastCol,1)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($lastCol, 1, $head['name']);
            if($head['comment']!== "") {
                $sheet->getCommentByColumnAndRow($lastCol, 1)
                    ->setVisible(true)
                    ->setMarginTop('100pt')
                    ->setHeight('100pt')
                    ->setMarginLeft($margin_left_comment . "pt")
                    ->getText()->createText($head['comment']);
            }

            if($head['generate_range']) {
               switch($head['field_excel']) {
                   case 'facturador': $items_range  = $rfc->listEmisores(); break;
                   case 'noFactura13':
                   case 'qualification':
                   case 'type': $items_range = $cat->EnumerateFromArrayLineal($head['accepted_values']); break;
                   default: $items_range = $cat->EnumerateCatalogue($head['reference_table']); break;
               }
               $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, ucfirst($head['name_range']));
               $current_row_catalogue = 2;
               $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
               foreach($items_range as $reg) {
                    $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $reg[$head['field_comparison_foreign']]);
                    $current_row_catalogue++;
                }
               if($head['field_excel'] === 'facturador' && count($items_range))
                   $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, 'Efectivo');

                $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
                $book->addNamedRange(
                    new PHPExcel_NamedRange(
                        $head['name_range'],
                        $catalogue,
                        "$current_init_range:$current_end_range"
                    )
                );
                $cad['columna'] =  $lastCol;
                $cad['name_range']  = $head['name_range'];
                array_push($other_ranges, $cad);
                $current_col_catalogue += count($items_range) > 0 ? 1 : 0;
            }
            $margin_left_comment +=110;
            $lastCol++;
        }
        $data_range_resp = [];
        foreach ($list_departamentos as $dep) {
            $sheet->getStyleByColumnAndRow($lastCol, 1)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($lastCol, 1, 'Resp.' . $dep['departamento']);
            $sheet->getCommentByColumnAndRow($lastCol, 1)
                ->setVisible(true)
                ->setMarginTop('100pt')
                ->setHeight('100pt')
                ->setMarginLeft($margin_left_comment . "pt")
                ->getText()->createText("Seleccionar el responsable de la lista que se muestra en las filas.");

            $personal->setDepartamentoId($dep['departamentoId']);
            $responsables = $personal->getListPersonalByDepartamento();

            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "RESPONSABLES DEP." . strtoupper($dep['departamento']));
            foreach ($responsables as $item) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $item['name']);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue--;
            $name_range = "dep_" . $dep['departamentoId'];
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    $name_range,
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            $end_col['col_string'] = PHPExcel_Cell::stringFromColumnIndex($lastCol);
            $end_col['name_range'] = $name_range;
            array_push($data_range_resp, $end_col);

            $margin_left_comment += 110;
            $current_col_catalogue++;
            $lastCol++;
        }

        foreach ($data_range_resp as $data_resp) {
            $init = $data_resp['col_string'] . "2";
            $end = $data_resp['col_string'] . "2";
            $current_name_range = $data_resp['name_range'];
            $objList = $sheet->getCell($init)->getDataValidation();
            $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('El responsable no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione un responsable de la lista');
            $objList->setPrompt('Seleccione un responsable de la lista.');
            $objList->setFormula1("=$current_name_range"); //note this!
            $sheet->setDataValidation("$init:$end", $objList);
            unset($objList);
        }

        foreach ($other_ranges as $other_range) {
            if($other_range['columna'] === "")
                continue;

            $init = PHPExcel_Cell::stringFromColumnIndex($other_range['columna']) . "2";
            $end = PHPExcel_Cell::stringFromColumnIndex($other_range['columna']) . "2";
            $current_name_range = $other_range['name_range'];
            $objList = $sheet->getCell($init)->getDataValidation();
            $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('El valor seleccionado no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione un valor de la lista.');
            $objList->setPrompt('Seleccione un valor de la lista.');
            $objList->setFormula1("=$current_name_range"); //note this!
            $sheet->setDataValidation("$init:$end", $objList);
            unset($objList);
        }
        $sheet->getCommentByColumnAndRow(0, 2)
            ->setVisible(true)
            ->setMarginTop('300pt')
            ->setHeight('350pt')
            ->setWidth('300pt')
            ->setMarginLeft('0pt')
            ->getText()->createText("Reglas a tener en cuenta para el correcto llenado del archivo:\n
            - Tome como ejemplo la fila 2 para iniciar el llenado de las filas, ya que se encuentra lista para el uso de la informacion desplegable.\n
            - Por cada fila que necesite agregar copie la anterior y pegue en la siguiente para tener disponible la configuracion de la fila inicial.\n
            - No se permiten filas vacias.\n
            - Una vez finalizado el llenado de informacion, vaya a archivo > Guardar como > Elegir directorio donde alojara el archivo > Seleccione el tipo   CSV (delimitado por comas)(*.csv) > Guardar\n
            - Se recomienda mantener abierto el archivo, para futuras correcciones en caso de haber cometido algun error en el llenado.\n\n
            Nota: Puede ocultar los comentarios de la siguiente manera: En la parte superior  de la ventana de excel, ubiquese en la pestaña Revisar , vaya a la seccion comentarios y de click
            en la opcion Mostrar todos los comentarios. 
            ");
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
    case 'layout-update-encargado':
        $book =  new PHPExcel();
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
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('LayoutServicios');
        $sheet->setCellValueByColumnAndRow(0,1,'id_contrato');
        $sheet->setCellValueByColumnAndRow(1,1,'nombre_empresa');
        $sheet->setCellValueByColumnAndRow(2,1,'id_servicio');
        $sheet->setCellValueByColumnAndRow(3,1,'nombre_servicio');
        $sheet->setCellValueByColumnAndRow(4,1,'costo');
        $sheet->setCellValueByColumnAndRow(5,1,'inicio_operacion');
        $sheet->setCellValueByColumnAndRow(6,1,'inicio_facturacion');
        $sheet->setCellValueByColumnAndRow(7,1,'fecha_ultimo_workflow');
        $sheet->setCellValueByColumnAndRow(8,1,'departamento');
        $sheet->setCellValueByColumnAndRow(9,1,'periodicidad');
        $sheet->setCellValueByColumnAndRow(10,1,'status');

        $servicios =  $servicio->EnumerateServiceForInstances();
        $row=2;
        foreach($servicios as $key=>$value){
            $sheet->setCellValueByColumnAndRow(0,$row,$value['contractId']);
            $sheet->setCellValueByColumnAndRow(1,$row,$value['razonSocialName']);
            $sheet->setCellValueByColumnAndRow(2,$row,$value['servicioId']);
            $sheet->setCellValueByColumnAndRow(3,$row,$value['nombreServicio']);
            $sheet->setCellValueByColumnAndRow(4,$row,$value['costo']);
            $sheet->setCellValueByColumnAndRow(5,$row,$value['inicioOperaciones']!='0000-00-00'
                                                                     && !is_null($value['inicioOperaciones'])
                                                                     && (int)date('Y',strtotime($value['inicioOperaciones'])) > 1989 ? date('d/m/Y',strtotime($value['inicioOperaciones'])): '');
            $sheet->setCellValueByColumnAndRow(6,$row,$value['inicioFactura']!='0000-00-00'
                                                                     && !is_null($value['inicioFactura'])
                                                                     && (int)date('Y',strtotime($value['inicioFactura'])) > 1989 ? date('d/m/Y',strtotime($value['inicioFactura'])):'');
            $sheet->setCellValueByColumnAndRow(7,$row,$value['status']=='bajaParcial'
                                                                     && !is_null($value['lastDateWorkflow'])
                                                                     && (int)date('Y',strtotime($value['lastDateWorkflow'])) > 1989 ? date('d/m/Y',strtotime($value['lastDateWorkflow'])):'');
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
