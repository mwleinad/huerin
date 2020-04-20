<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');

session_start();
include(DOC_ROOT . '/libs/excel/PHPExcel.php');
switch ($_POST['type']) {
    case 'generate_report_razon_social':
        $string = file_get_contents(DOC_ROOT . "/properties/config_customer_contract.json");
        $headers = json_decode($string, true);
        $formas_pago = $catalogo->formasDePago();
        $encargados = $personal->GetIdResponsablesSubordinados($_POST);
        $_POST["encargados"] = $encargados;
        $_POST['selectedResp'] = $_POST['responsableCuenta'] > 0 ? true : false;
        $items = $customer->SuggestCustomerRazon($_POST);

        $book = new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $catalogue = $book->createSheet(1);
        $catalogue->setTitle("Responsables");
        $sheet->setTitle('Contratos');
        $list_departamentos = $departamentos->GetListDepartamentos();
        $colRegimen = "";
        $colMetodoPago = "";
        $colSociedad = "";
        $colFacturador = "";
        $lastCol = 0;
        foreach ($headers as $keyHead => $head) {
            $sheet->getStyleByColumnAndRow($lastCol,1)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($lastCol, 1, $head['name'])
                ->getCommentByColumnAndRow($lastCol, 1)->getText()->createText($head['comment']);
            switch($head['field_excel']){
               case "nombreRegimen":
                 $colRegimen = $lastCol;
               break;
               case "metodoDePago":
                 $colMetodoPago = $lastCol;
                 $comment_text = "Editar comentario para visualizar las opciones\n";
                 foreach($formas_pago as $formpago)
                     $comment_text .=$formpago['c_FormaPago'].".".$formpago['descripcion']."\n";
                 $headers[$keyHead]['comment'] = $comment_text;
                 $sheet->getCommentByColumnAndRow($lastCol, 1)->getText()->createText($comment_text);
               break;
               case "nombreSociedad":
                 $colSociedad = $lastCol;
               break;
               case "facturador":
                $colFacturador = $lastCol;
               break;
            }
            $lastCol++;
        }
        $data_range_resp = [];
        $current_col_catalogue = 0;
        foreach ($list_departamentos as $dep) {
            $sheet->getStyleByColumnAndRow($lastCol,1)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($lastCol, 1, 'Resp.' . ucfirst(strtolower($dep['departamento'])));
            $personal->setDepartamentoId($dep['departamentoId']);
            $responsables = $personal->getListPersonalByDepartamento();

            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "RESP DEP." . strtoupper($dep['departamento']));
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
            $new_head['match_db'] = true;
            $new_head['table'] = "personal";
            $new_head['field_comparison'] = "name";
            $new_head['comment'] = "Seleccione un responsable de la lista";
            $keyField = "name" . ucfirst(strtolower(str_replace(" ", "", $dep['departamento'])));
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

            $current_col_catalogue++;
            $lastCol++;
        }
        //range regimenes
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
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $fp['c_FormaPago']);
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

        $currentRow = 2;
        foreach ($items as $keyItem => $item) {
            foreach ($headers as $keyHead => $header)
                $sheet->setCellValueByColumnAndRow($keyHead, $currentRow, $item[$header['field_excel']]);

            $currentRow++;
        }
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
        if($colRegimen!=""){
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


        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));

        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);

            }
        }
        $nameFile = "report_razon_social_" . $_SESSION['User']['userId'] . ".xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
        break;
}