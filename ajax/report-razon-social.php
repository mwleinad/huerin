<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

session_start();
include(DOC_ROOT . '/libs/excel/PHPExcel.php');
switch ($_POST['type']) {
    case 'generate_report_razon_social':
        $file  = DOC_ROOT . "/properties/config_layout_".$_POST['type_report'].".json";
        $string = file_get_contents($file);
        $headers = json_decode($string, true);
        $formas_pago = $catalogo->formasDePago();
        $encargados = $personal->GetIdResponsablesSubordinados($_POST);
        $_POST["encargados"] = $encargados;
        $_POST['selectedResp'] = $_POST['responsableCuenta'] > 0 ? true : false;
        $group_by =  $_POST['type_report'] === 'update_customer' ? 'customerId' : 'contractId';
        $items = $customer->SuggestCustomerRazon($_POST, $group_by);

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $catalogue = $book->createSheet(1);
        $catalogue->setTitle("Datos");
        $sheet->setTitle('Contratos');
        $list_departamentos = $departamentos->GetListDepartamentos();
        $colRegimen = "";
        $colMetodoPago = "";
        $colSociedad = "";
        $colFacturador = "";
        $colTipoPersona = "";
        $colNoFactura = "";
        $colAcName ="";
        $lastCol = 0;
        $margin_left_comment = 100;
        foreach ($headers as $keyHead => $head) {
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

            switch($head['field_excel']){
               case "nombreRegimen":
                 $colRegimen = $lastCol;
               break;
               case "metodoDePago":
                 $colMetodoPago = $lastCol;
               break;
               case "nombreSociedad":
                 $colSociedad = $lastCol;
               break;
               case "facturador":
                $colFacturador = $lastCol;
               break;
               case "type":
                $colTipoPersona = $lastCol;
               break;
               case "noFactura13":
                $colNoFactura = $lastCol;
               break;
               case "ac_name":
                $colAcName = $lastCol;
               break;
               case "qualification":
                $colQualification = $lastCol;
               break;
            }
            $margin_left_comment +=150;
            $lastCol++;
        }
        $data_range_resp = [];
        $current_col_catalogue = 0;
        if($_POST['type_report'] !== "update_customer") {
            foreach ($list_departamentos as $dep) {
                $sheet->getStyleByColumnAndRow($lastCol, 1)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($lastCol, 1, 'Resp.' . ucfirst(mb_strtolower($dep['departamento'])));

                if ($_POST['type_report'] !== "complete_report_cc") {
                    $sheet->getCommentByColumnAndRow($lastCol, 1)
                        ->setVisible(true)
                        ->setMarginTop('100pt')
                        ->setHeight('100pt')
                        ->setMarginLeft($margin_left_comment . "pt")
                        ->getText()->createText("Seleccionar el responsable de la lista que se muestra en las filas.");
                }

                $personal->setDepartamentoId($dep['departamentoId']);
                $responsables = $personal->getListPersonalByDepartamento();

                $current_row_catalogue = 2;
                $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "RESP DEP." . mb_strtoupper($dep['departamento']));
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

                //add last columns to $headers
                $new_head['name'] = "Resp. " . ucfirst($dep['departamento']);
                $new_head['required'] = false;
                $new_head['comment'] = "Seleccionar el responsable de la lista que se muestra en las filas.";
                $keyField = "name" . ucfirst(mb_strtolower(str_replace(" ", "", $dep['departamento'])));
                $new_head['field_excel'] = $keyField;
                $new_head['fillable'] = false;
                $new_head['constraint'] = true;
                $new_head['reference_table'] = "personal";
                $new_head['field_comparison_foreign'] = "name";
                $new_head['field_return_foreign'] = "personalId";
                $new_head['foreign_key'] = "personalId";
                array_push($headers, $new_head);

                $end_col['col_string'] = PHPExcel_Cell::stringFromColumnIndex($lastCol);
                $end_col['name_range'] = $name_range;
                array_push($data_range_resp, $end_col);

                $margin_left_comment += 150;
                $current_col_catalogue++;
                $lastCol++;
            }
        }
        // decisiones
        $decisiones = ['Si', 'No'];
        $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "DECISIONES");
        $current_row_catalogue = 2;
        $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
        foreach($decisiones as $decision) {
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $decision);
            $current_row_catalogue++;
        }
        $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
        $book->addNamedRange(
            new PHPExcel_NamedRange(
                "decisiones",
                $catalogue,
                "$current_init_range:$current_end_range"
            )
        );
        //end decisiones
        $currentRow = 2;
        foreach ($items as $keyItem => $item) {
            foreach ($headers as $keyHead => $header) {
                if($header['validate_date_format'] === true) {
                    $sheet->setCellValueByColumnAndRow($keyHead, $currentRow, $item[$header['field_excel']]);
                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                } else {
                    if ($_POST['type_report'] === 'complete_report_cc' && $header['field_excel'] === 'active') {
                        $val_cell =  $item[$header['field_excel']] == '1' ? 'Si' : 'No';
                    } else {
                        $val_cell =  $item[$header['field_excel']];
                    }

                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->setValueExplicit($val_cell, PHPExcel_Cell_DataType::TYPE_STRING );
                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->getStyle()->getNumberFormat()->setFormatCode('@');
                }
            }
            $currentRow++;
        }
        if($_POST['type_report'] !== 'update_customer') {
            //range regimenes
            $current_col_catalogue++;
            $regimenes = $regimen->EnumerateAll();
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "REGIMENES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($regimenes as $reg) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $reg['nombreRegimen']);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "regimenes",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end range regimenes
            //range metodo pago
            $current_col_catalogue++;
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "FORMAS DE PAGO");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($formas_pago as $fp) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $fp['descripcion']);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "formas_pago",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end metodos pago
            //range sociedades
            $current_col_catalogue++;
            $sociedades = $sociedad->EnumerateAll();
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "SOCIEDADES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($sociedades as $soc) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $soc['nombreSociedad']);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "sociedades",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end metodos pago
            //range facturadores
            $current_col_catalogue++;
            $facturadores = $rfc->listEmisores(false);
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "FACTURADORES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($facturadores as $fact) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $fact['claveFacturador']);
                $current_row_catalogue++;
            }
            //add adicional facturador
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, 'Efectivo');
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "facturadores",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end metodos pago
            // tipos persona
            $current_col_catalogue++;
            $tipos_persona = ['Persona Fisica', 'Persona Moral'];
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "TIPOS PERSONA");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($tipos_persona as $tp) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $tp);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "tipos_persona",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            // actividades economicas
            $current_col_catalogue++;
            $cat =new Catalogue();
            $actividades = $cat->ListActividadesComerciales(0, true);
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "ACTIVIDADES COMERCIALES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($actividades as $act) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, trim($act['name']));
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "actividades_comerciales",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            // calificaciones
            $calificaciones = ['AAA', 'AA', 'A'];
            $current_col_catalogue++;
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "CALIFICACIONES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($calificaciones as $calificacion) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $calificacion);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "calificaciones",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end calificaciones
            foreach ($data_range_resp as $data_resp) {
                $init = $data_resp['col_string'] . "2";
                $end = $data_resp['col_string'] . $currentRow;
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
            if($colRegimen!="") {
                $init = PHPExcel_Cell::stringFromColumnIndex($colRegimen) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colRegimen) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Regimen seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione un regimen de la lista');
                $objList->setPrompt('Seleccione un regimen de la lista.');
                $objList->setFormula1("=regimenes"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
            if($colMetodoPago!=""){
                $init = PHPExcel_Cell::stringFromColumnIndex($colMetodoPago) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colMetodoPago) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Forma de pago seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione una forma de pago de la lista');
                $string_prop = $headers[$colMetodoPago]['comment'];
                $objList->setPrompt("Seleccione un elemento de la lista ");
                $objList->setFormula1("=formas_pago"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
            if($colSociedad!=""){
                $init = PHPExcel_Cell::stringFromColumnIndex($colSociedad) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colSociedad) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Tipo de sociedad seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione una sociedad de la lista');
                $objList->setPrompt('Seleccione una sociedad de la lista.');
                $objList->setFormula1("=sociedades"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
            if($colFacturador!=""){
                $init = PHPExcel_Cell::stringFromColumnIndex($colFacturador) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colFacturador) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Facturador seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione un facturador de la lista');
                $objList->setPrompt('Seleccione un facturador de la lista.');
                $objList->setFormula1("=facturadores"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
            if($colTipoPersona!=""){
                $init = PHPExcel_Cell::stringFromColumnIndex($colTipoPersona) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colTipoPersona) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Tipo de persona seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione un tipo de persona de la lista');
                $objList->setPrompt('Seleccione un tipo de persona de la lista.');
                $objList->setFormula1("=tipos_persona"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
            if($colAcName!=""){
                $init = PHPExcel_Cell::stringFromColumnIndex($colAcName) . "2";
                $end = PHPExcel_Cell::stringFromColumnIndex($colAcName) . $currentRow;
                $objList = $sheet->getCell($init)->getDataValidation();
                $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
                $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
                $objList->setAllowBlank(false);
                $objList->setShowInputMessage(true);
                $objList->setShowErrorMessage(true);
                $objList->setShowDropDown(true);
                $objList->setErrorTitle('Error!!');
                $objList->setError('Tipo de actividad seleccionado no se encuentra en la lista.');
                $objList->setPromptTitle('Seleccione un tipo de actividad de la lista');
                $objList->setPrompt('Seleccione un tipo de actividad de la lista.');
                $objList->setFormula1("=actividades_comerciales"); //note this!
                $sheet->setDataValidation("$init:$end", $objList);
                unset($objList);
            }
        }

        if($colNoFactura!=""){
            $init = PHPExcel_Cell::stringFromColumnIndex($colNoFactura) . "2";
            $end = PHPExcel_Cell::stringFromColumnIndex($colNoFactura) . $currentRow;
            $objList = $sheet->getCell($init)->getDataValidation();
            $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('Valor seleccionado no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione un valor de la lista');
            $objList->setPrompt('Seleccione un valor de la lista.');
            $objList->setFormula1("=decisiones"); //note this!
            $sheet->setDataValidation("$init:$end", $objList);
            unset($objList);
        }
        if($colQualification!=""){
            $init = PHPExcel_Cell::stringFromColumnIndex($colQualification) . "2";
            $end = PHPExcel_Cell::stringFromColumnIndex($colQualification) . $currentRow;
            $objList = $sheet->getCell($init)->getDataValidation();
            $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('Valor seleccionado no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione un valor de la lista');
            $objList->setPrompt('Seleccione un valor de la lista.');
            $objList->setFormula1("=calificaciones"); //note this!
            $sheet->setDataValidation("$init:$end", $objList);
            unset($objList);
        }
        if( $_POST['type_report'] === 'update_customer' ||  $_POST['type_report'] === 'update_contract') {
            $sheet->getCommentByColumnAndRow(0, 2)
                ->setVisible(true)
                ->setMarginTop('300pt')
                ->setHeight('350pt')
                ->setWidth('300pt')
                ->setMarginLeft('0pt')
                ->getText()->createText("Reglas a tener en cuenta para el correcto llenado del archivo:\n
            - Utilice las listas desplegadas en las columnas donde esten presentes.\n
            - Una vez actualizado la informacion, vaya a archivo > Guardar como > Elegir directorio donde alojara el archivo > Seleccione el tipo   CSV (delimitado por comas)(*.csv) > Guardar\n
            - Se recomienda mantener abierto el archivo, para futuras correcciones en caso de haber cometido algun error en el llenado.\n\n
            Nota: Puede ocultar los comentarios de la siguiente manera: En la parte superior  de la ventana de excel, ubiquese en la pestaña Revisar , vaya a la seccion comentarios y de click
            en la opcion Mostrar todos los comentarios. 
            ");
        }

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "report_razon_social_".$_POST['tipos']."_" . $_SESSION['User']['userId'] . ".xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
    break;
}
