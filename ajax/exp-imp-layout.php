<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
/**
 * @param PHPExcel_Worksheet $sheet
 * @param string $colTipoDispositivo
 * @param int $row
 * @param array $tiposSoftware
 * @return void
 * @throws PHPExcel_Exception
 */
function addFormula(PHPExcel_Worksheet $sheet, string $col, int $row, array $tipos)
{
    for($ii=$row;$ii<=1000;$ii++) {
        $objValidation = $sheet->getCell($col . $ii)->getDataValidation();
        $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setPromptTitle('Listado de opciones');
        $objValidation->setPrompt('Por favor seleccione una opcion de la lista.');
        $objValidation->setErrorTitle('Ocurrio un error');
        $objValidation->setError('El valor no se encuentra en la lista');
        $objValidation->setFormula1('"' . implode(',', $tipos) . '"');
    }

}

function formatear(PHPExcel_Worksheet $sheet, string $col, int $row, array $format)
{
    for($ii=$row;$ii<=1000;$ii++) {
        $sheet->getStyle($col.$ii)->applyFromArray($format);
    }

}

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
        if(!is_dir(DOC_ROOT."/sendFiles"))
            mkdir(DOC_ROOT."/sendFiles", 765);
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
    break;
    case 'layout-recotizar-servicios':

        $book =  new PHPExcel();
        $global_config_style_cell['style_porcent']['borders'] = [];
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('LayoutServicios');
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col,1,'id_contrato');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'id_servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'cliente');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Razon social');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Periodicidad');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Inicio operación');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Inicio facturación');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Responsable ATC');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Gerente del servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Supervisor del servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Precio en cartera');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Horas invertidas');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo por hora');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Utilidad actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Utilidad actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Factor');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo nuevo');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo nuevo final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Utilidad final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Utilidad final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Incremento');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Incremento');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS JH');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS JB');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS CH');


        $servicios =  $servicio->EnumerateServiceForRecotizacion();
        $row=2;

        foreach($servicios as $key=>$value) {
            if($value['is_primary'] != 1 || $value['status'] == 'bajaParcial')
                continue;
            // ENCONTRAR LOS EL RESPONSABLE DE ATC
            $current_permisos = json_decode($value['permiso_detallado'], true);
            $current_permisos =  !is_array($current_permisos) ? [] : $current_permisos;

            $permisos_normalizado = [];
            $permisos_normalizado2 = [];
            foreach ($current_permisos as $current_permiso) {
                $permisos_normalizado[strtolower($current_permiso['departamento'])] = $current_permiso;
                $permisos_normalizado2[$current_permiso['departamento_id']] = $current_permiso;
            }


            //ENCONTRAR RESPONSABLES
            $departamentoId = $value["departamentoId"];
            $responsable = isset($permisos_normalizado2[$departamentoId]) > 0 ? $permisos_normalizado2[$departamentoId] : null ;

            $serv = [];
            if($responsable !== null){
                $jefes = array();
                $personal->setPersonalId($responsable['personal_id']);
                $rolRes = $personal->InfoWhitRol();
                $personal->deepJefesByLevel($jefes,true);
                $serv["contador"] = $jefes[6];
                $serv['supervisor'] = $jefes[5];
                $serv['subgerente'] = $jefes[4];
                $serv['gerente'] = $jefes[3];
                $serv['jefeMax'] = $jefes[1];
                $serv[strtolower($rolRes["nameLevel"])] = $jefes['me'];
            }else {
                $serv['auxiliar'] = 'No encontrado';
                $serv['contador'] = 'No encontrado';
                $serv['supervisor'] = 'No encontrado';
                $serv['subgerente'] = 'No encontrado';
                $serv['gerente'] = 'No encontrado';
            }

            $col=0;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['contractId']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['servicioId']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['clienteName']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['razonSocialName']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['nombreServicio']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['periodicidad']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['inicioOperaciones']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['inicioFactura']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row,$permisos_normalizado['atencion al cliente']['nombre']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row, $serv['gerente']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row, $serv['supervisor'] ?? $serv['subgerente'] );
            $col++;

            $coorPrecioCartera = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row,$value['costo']);
            $col++;

            $coorHorasInvertida = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row,'');
            $col++;

            //TODO encontrar sumatoria general del sueldo del gerente.


            $coorCostoPorHora = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "");
            $col++;

            $coorPrecioActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=+({$coorHorasInvertida} * {$coorCostoPorHora})");
            $col++;

            $coorUtilidadActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=({$coorPrecioCartera} - {$coorPrecioActual})");
            $col++;

            $coorPorUtilidadActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, '=IFERROR((+'.$coorUtilidadActual.'/'.$coorPrecioCartera.'),0)')
                ->getStyle($coorPorUtilidadActual)->applyFromArray($global_config_style_cell['style_porcent']);;
            $col++;

            $coorFactor = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "");
            $col++;

            $coorCostoNuevo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=IFERROR((+$coorPrecioCartera*$coorFactor),0)");
            $col++;

            $coorCostoNuevoFinal = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $formula = "=CEILING($coorCostoNuevo,100)";
            $sheet->setCellValueByColumnAndRow($col,$row, $formula);
            $col++;

            $coorUtilidadNuevo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=IFERROR((+$coorCostoNuevoFinal-$coorPrecioActual),0)");
            $col++;

            $coorPorUtilidadNuevo  = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=IFERROR((+$coorCostoNuevoFinal/$coorCostoNuevo),0)")
                ->getStyle($coorPorUtilidadNuevo)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;

            $coorIncremento = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $sheet->setCellValueByColumnAndRow($col,$row, "=IFERROR((+$coorCostoNuevoFinal-$coorPrecioCartera),0)");
            $col++;

            $coorPorIncremento = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col,$row, "=IFERROR((+$coorIncremento/$coorPrecioCartera),0)")
                ->getStyle($coorPorIncremento)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row, "");
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row, "");
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row, "");

            $row++;
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
        $nameFile= "Formato recotizacion.xlsx";
        if(!is_dir(DOC_ROOT."/sendFiles"))
            mkdir(DOC_ROOT."/sendFiles", 765);
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
        break;

    case 'layout-inventario':

        $book =  new PHPExcel();
        $global_config_style_cell['style_porcent']['borders'] = [];
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('Inventario');

        $tiposSoftware    = ['aspel_coi','aspel_noi','aspel_sae','aspel_facture','admin_xml','adobe_photoshop', 'adobe_ilustrator'];
        $tiposRecurso     = ['dispositivo','equipo_computo','software','inmobiliaria'];
        $tiposDispositivo = ['nobreak','hdmi','hubusb','mousepad','ethernet','mouse','teclado','ventilador','monitor','cable_ventilador','convertidor_hdmi','convertidor_vga'];
        $tiposEquipo      = ['escritorio','portatil'];

        $col = 0;
        $sheet->setCellValueByColumnAndRow($col,1,'No inventario');
        $col++;
        $colTipoRecurso = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Tipo recurso');
        $col++;
        $colTipoDispositivo = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Tipo dispositivo');
        $col++;
        $colTipoEquipo = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Tipo equipo');
        $col++;
        $colTipoSoftware = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Tipo software');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Numero serie');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Numero licencia');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Codigo activacion');
        $col++;
        $colFechaAlta = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Fecha alta');
        formatear($sheet, $colFechaAlta, 2, $global_config_style_cell['style_date']);
        $col++;
        $colFechaCompra = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Fecha compra');
        formatear($sheet, $colFechaCompra, 2, $global_config_style_cell['style_date']);
        $col++;
        $colFechaVencimiento = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Fecha vencimiento');
        formatear($sheet, $colFechaVencimiento, 2, $global_config_style_cell['style_date']);
        $col++;
        $colCostoCompra = PHPExcel_Cell::stringFromColumnIndex($col);
        $sheet->setCellValueByColumnAndRow($col,1,'Costo compra');
        formatear($sheet, $colCostoCompra, 2, $global_config_style_cell['style_simple_text']);
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo recuperacion');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Marca');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Modelo');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Procesador');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Memoria ram');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Disco duro');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Observaciones');

        $row = 2;
        addFormula($sheet, $colTipoSoftware, $row, $tiposSoftware);
        addFormula($sheet, $colTipoRecurso, $row, $tiposRecurso);
        addFormula($sheet, $colTipoDispositivo, $row, $tiposDispositivo);
        addFormula($sheet, $colTipoEquipo, $row, $tiposEquipo);

        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }

        $nameFile= "Formato inventario.xlsx";
        if(!is_dir(DOC_ROOT."/sendFiles"))
            mkdir(DOC_ROOT."/sendFiles", 765);
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
        break;

    case 'layout-reporte-recotizar':

        $book =  new PHPExcel();
        $global_config_style_cell['style_porcent']['borders'] = [];
        $styleSimpleText = array_merge($global_config_style_cell['style_simple_text_whit_border'],array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));
        $styleNumberDecimal = array_merge($global_config_style_cell['style_simple_text_whit_border'],array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            ],
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));
        $styleTotalCliente = array_merge($styleSimpleText, array(
                'numberformat' => [
                    'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ],
                'font' => array(
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BFBFBF'))
            )
        );
        $styleTotalClientePorcentaje = array_merge($global_config_style_cell['style_porcent'], array(
                'numberformat' => [
                    'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE,
                ],
                'font' => array(
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'BFBFBF'))
            )
        );

        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('LayoutServicios');
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col,1,'id_contrato');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'id_servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'cliente');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Razon social');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Periodicidad');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Inicio operación');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Inicio facturación');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Responsable ATC');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Gerente del servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Supervisor del servicio');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Precio en cartera');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Horas invertidas');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo por hora');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Utilidad actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Utilidad actual');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Factor');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo nuevo');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Costo nuevo final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Utilidad final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Utilidad final');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'Incremento');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'% Incremento');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS JH');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS JB');
        $col++;
        $sheet->setCellValueByColumnAndRow($col,1,'COMENTARIOS CH');


        $servicios =  $servicio->EnumerateServiceForRecotizacion();


        $clientesId = array_column($servicios, 'customerId');
        $clientesId = array_values(array_unique($clientesId));

        $clientes = [];
        foreach ($clientesId as $clienteId) {
            $currentCliente = current(array_filter($servicios, fn($elemento) => $elemento['customerId'] === $clienteId));
            $cadCliente['customerId'] = $currentCliente['customerId'];
            $cadCliente['nombre'] = $currentCliente['clienteName'];
            $cadCliente['servicios'] =  array_filter($servicios, fn($elemento) => $elemento['customerId'] === $clienteId);
            $clientes[] = $cadCliente;
        }

        $row=2;
        foreach($clientes as $cliente) {
            $rowInicioGrupoCliente = $row;
            $serviciosActivos = array_filter($cliente['servicios'] ?? [], fn($item) => (in_array($item['status'], ['Activo','activo']) && $item['is_primary'] == 1));
            if(count($serviciosActivos) <= 0)
                continue;

            foreach ($cliente['servicios'] as $servicio) {
                if ($servicio['is_primary'] != 1 || $servicio['status'] == 'bajaParcial')
                    continue;

                // ENCONTRAR LOS EL RESPONSABLE DE ATC
                $current_permisos = json_decode($servicio['permiso_detallado'], true);
                $current_permisos = !is_array($current_permisos) ? [] : $current_permisos;

                $permisos_normalizado = [];
                $permisos_normalizado2 = [];
                foreach ($current_permisos as $current_permiso) {
                    $permisos_normalizado[strtolower($current_permiso['departamento'])] = $current_permiso;
                    $permisos_normalizado2[$current_permiso['departamento_id']] = $current_permiso;
                }


                //ENCONTRAR RESPONSABLES
                $departamentoId = $servicio["departamentoId"];
                $responsable = isset($permisos_normalizado2[$departamentoId]) > 0 ? $permisos_normalizado2[$departamentoId] : null;

                $sueldoAcumulado = 0;
                $gerente = null;
                $supervisor =  null;

                if ($responsable) {
                    $superiores = $personal->superiores($responsable['personal_id']);
                    $gerente  = current(array_filter($superiores, fn($item) => $item['puesto'] == 'Gerente'));
                    $supervisor  = current(array_filter($superiores, fn($item) => $item['puesto'] == 'Supervisor'));
                }
                $sueldoAcumulado = 0;
                if(isset($gerente['id'])) {
                    $inferiores        = $personal->inferiores($gerente['id']);
                    $sueldosInferiores = array_column($inferiores, 'sueldo');
                    $sueldoAcumulado   = array_sum($sueldosInferiores) + $gerente['sueldo'];
                }

                $coorRowInicial =
                $col = 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['contractId'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['servicioId'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $cliente['nombre'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['razonSocialName'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['nombreServicio'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['periodicidad'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['inicioOperaciones'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['inicioFactura'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $permisos_normalizado['atencion al cliente']['nombre'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $gerente['nombre'] ?? 'Sin gerente en linea de mando')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $supervisor['nombre']  ?? 'Sin supervisor en linea de mando')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styleSimpleText);
                $col++;

                $coorPrecioCartera = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, $servicio['costo'])
                ->getStyle($coorPrecioCartera)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorHorasInvertida = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, '');
                $col++;

                //TODO salario de los subordinados
                $formula = "=+((((".$sueldoAcumulado."*(1+(".PORCENTAJE_AUMENTO_SALARIO."/100)))/30)/8)/6)";
                $coorCostoPorHora = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                    ->getStyle($coorCostoPorHora)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorPrecioActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=+({$coorHorasInvertida} * {$coorCostoPorHora})")
                    ->getStyle($coorPrecioActual)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorUtilidadActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=({$coorPrecioCartera} - {$coorPrecioActual})")
                    ->getStyle($coorUtilidadActual)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorPorUtilidadActual = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, '=IFERROR((+' . $coorUtilidadActual . '/' . $coorPrecioCartera . '),0)')
                    ->getStyle($coorPorUtilidadActual)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;

                $coorFactor = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, 1.05);
                $col++;

                $coorCostoNuevo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+$coorPrecioCartera*$coorFactor),0)")
                    ->getStyle($coorCostoNuevo)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorCostoNuevoFinal = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $formula = "=CEILING($coorCostoNuevo,100)";
                $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                    ->getStyle($coorCostoNuevoFinal)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorUtilidadNuevo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+$coorCostoNuevoFinal-$coorPrecioActual),0)")
                    ->getStyle($coorUtilidadNuevo)->applyFromArray($styleNumberDecimal);
                $col++;

                $coorPorUtilidadNuevo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+$coorCostoNuevoFinal/$coorUtilidadNuevo),0)")
                    ->getStyle($coorPorUtilidadNuevo)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;

                $coorIncremento = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+$coorCostoNuevoFinal-$coorPrecioCartera),0)")
                    ->getStyle($coorIncremento)->applyFromArray($styleNumberDecimal);;
                $col++;

                $coorPorIncremento = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+$coorIncremento/$coorPrecioCartera),0)")
                    ->getStyle($coorPorIncremento)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;

                $sheet->setCellValueByColumnAndRow($col, $row, "");
                $col++;

                $sheet->setCellValueByColumnAndRow($col, $row, "");
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "");

                $row++;
            }

            $rowFinGrupoCliente = count($cliente['servicios']) > 1 ? ($row - 1) : $row;

            for($ii=0; $ii <= 8; $ii++) {
                $sheet->setCellValueByColumnAndRow($ii, $row,'')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
            }
            $sheet->setCellValueByColumnAndRow(9, $row,$cliente['nombre'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(9) . $row)->applyFromArray($styleTotalCliente);
            $sheet->setCellValueByColumnAndRow(10, $row,'TOTALES')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(10) . $row)->applyFromArray($styleTotalCliente);

            for($ii=11; $ii < 24; $ii++) {
                $coorInicio = PHPExcel_Cell::stringFromColumnIndex($ii) . $rowInicioGrupoCliente;
                $coorFin    = PHPExcel_Cell::stringFromColumnIndex($ii) . $rowFinGrupoCliente;

                $coorTotalPrecioCartera = PHPExcel_Cell::stringFromColumnIndex(11) . $row;
                $coorTotalCostoActual   = PHPExcel_Cell::stringFromColumnIndex(14) . $row;
                $coorTotalUtilidadActual= PHPExcel_Cell::stringFromColumnIndex(15) . $row;
                $coorTotalFactor        = PHPExcel_Cell::stringFromColumnIndex(17) . $row;
                $coorTotalCostoNuevo    = PHPExcel_Cell::stringFromColumnIndex(18) . $row;
                $coorTotalCostoNuevoFinal  = PHPExcel_Cell::stringFromColumnIndex(19) . $row;
                $coorTotalUtilidadFinal    = PHPExcel_Cell::stringFromColumnIndex(20) . $row;
                $coorTotalPorUtilidadFinal = PHPExcel_Cell::stringFromColumnIndex(21) . $row;
                $coorTotalIncremento = PHPExcel_Cell::stringFromColumnIndex(22) . $row;

                switch($ii) {
                    case 16:
                        $formula =  "=IFERROR(".$coorTotalUtilidadActual."/".$coorTotalPrecioCartera.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalClientePorcentaje);
                        break;
                    case 17:
                        $formula =  1.05;
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;
                    /*case 18:
                        $formula = $formula =  "=IFERROR(".$coorTotalPrecioCartera."*".$coorTotalFactor.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;
                    case 19:
                        $formula = "=CEILING($coorTotalCostoNuevo,100)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;
                    case 20:
                        $formula =  "=IFERROR(".$coorTotalCostoNuevoFinal."-".$coorTotalCostoActual.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;*/
                    case 21:
                        $formula =  "=IFERROR(".$coorTotalCostoNuevoFinal."/".$coorTotalUtilidadFinal.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalClientePorcentaje);
                        break;
                   /* case 22:
                        $formula =  "=IFERROR(".$coorTotalCostoNuevoFinal."-".$coorTotalPrecioCartera.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;*/
                    case 23:
                        $formula =  "=IFERROR(".$coorTotalIncremento."/".$coorTotalPrecioCartera.",0)";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalClientePorcentaje);
                        break;
                    default:
                        $formula = "=SUM({$coorInicio}:{$coorFin})";
                        $sheet->setCellValueByColumnAndRow($ii, $row, $formula)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
                        break;
                }


            }
            for($ii=24; $ii <= 27; $ii++) {
                $sheet->setCellValueByColumnAndRow($ii, $row,'')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($ii) . $row)->applyFromArray($styleTotalCliente);
            }
            $row++;
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
        $nameFile= "Reporte_de_recotizacion.xlsx";
        if(!is_dir(DOC_ROOT."/sendFiles"))
            mkdir(DOC_ROOT."/sendFiles", 765);
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$nameFile;
        break;
}
