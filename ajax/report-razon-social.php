<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries.php');
$catalogoGeneral = $catalogue;

session_start();
include_once(DOC_ROOT . '/libs/excel/PHPExcel.php');
switch ($_POST['type']) {
    case 'generate_report_razon_social':
        $file  = DOC_ROOT . "/properties/config_layout_".$_POST['type_report'].".json";
        $string = file_get_contents($file);
        $headers = json_decode($string, true);
        $formas_pago = $catalogo->formasDePago();
        $encargados = $personal->GetIdResponsablesSubordinados($_POST);
        $responsablesGerenciales = $personal->getPersonasParaDepartamentoGerencial();
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
               case "clasificacionCliente":
                    $colClasificacionCliente = $lastCol;
               break;
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
               case "tipoClasificacion":
                $colClasificacion = $lastCol;
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

                $tipoGerencia = current(array_filter(DEPARTAMENTOS_TIPO_GERENCIA,
                    function ($item) use ($dep) {
                        return $item['principal'] == $dep['departamento'];
                    }
                ));

                if (isset($tipoGerencia['secundario']) && $tipoGerencia['secundario']!== '') {
                    $responsables = $responsablesGerenciales[$tipoGerencia['secundario']] ?? [];
                } else {
                    $personal->setDepartamentoId($dep['departamentoId']);
                    $responsables = $personal->getListPersonalByDepartamento();
                }


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
                if($header['field_excel'] === 'nombreSociedad' && $item['type'] =='Persona Fisica')
                    continue;

                if($header['validate_date_format'] === true) {
                    $sheet->setCellValueByColumnAndRow($keyHead, $currentRow, $item[$header['field_excel']]);
                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->getStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                } else {
                    if ($_POST['type_report'] === 'complete_report_cc' && $header['field_excel'] === 'active') {
                        $val_cell =  $item[$header['field_excel']] == '1' ? 'Si' : 'No';
                    } else {
                        $val_cell =  $item[$header['field_excel']];
                    }

                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->setValueExplicit($val_cell);
                    $sheet->getCellByColumnAndRow($keyHead, $currentRow)->getStyle()->getNumberFormat()->setFormatCode('@');
                }
            }
            $currentRow++;
        }

        //range clasificacion clientes
        $current_col_catalogue++;
        $clasificacionesCliente = $catalogoGeneral->EnumerateCatalogue('tipo_clasificacion_cliente');

        $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "CLASIFICACIÓNES CLIENTE");
        $current_row_catalogue = 2;
        $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
        foreach($clasificacionesCliente as $clasificacionCliente) {
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $clasificacionCliente['nombre']);
            $current_row_catalogue++;
        }
        $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
        $book->addNamedRange(
            new PHPExcel_NamedRange(
                "clasificaciones_cliente",
                $catalogue,
                "$current_init_range:$current_end_range"
            )
        );



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
            // CLASIFICACIONES

            $current_col_catalogue++;
            $clasificaciones = $catalogoGeneral->EnumerateCatalogue('tipo_clasificacion');
            $catalogue->setCellValueByColumnAndRow($current_col_catalogue, 1, "CLASIFICACIONES");
            $current_row_catalogue = 2;
            $current_init_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            foreach($clasificaciones as $clasificacion) {
                $catalogue->setCellValueByColumnAndRow($current_col_catalogue, $current_row_catalogue, $clasificacion['nombre']);
                $current_row_catalogue++;
            }
            $current_end_range = PHPExcel_Cell::stringFromColumnIndex($current_col_catalogue) . $current_row_catalogue;
            $book->addNamedRange(
                new PHPExcel_NamedRange(
                    "clasificaciones",
                    $catalogue,
                    "$current_init_range:$current_end_range"
                )
            );
            //end CLASIFICACIONES
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

        if($colClasificacionCliente!="") {
            $init = PHPExcel_Cell::stringFromColumnIndex($colClasificacionCliente) . "2";
            $end = PHPExcel_Cell::stringFromColumnIndex($colClasificacionCliente) . $currentRow;
            $objList = $sheet->getCell($init)->getDataValidation();
            $objList->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objList->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objList->setAllowBlank(false);
            $objList->setShowInputMessage(true);
            $objList->setShowErrorMessage(true);
            $objList->setShowDropDown(true);
            $objList->setErrorTitle('Error!!');
            $objList->setError('Clasificación cliente seleccionado no se encuentra en la lista.');
            $objList->setPromptTitle('Seleccione una clasificación cliente de la lista');
            $objList->setPrompt('Seleccione una clasificación cliente de la lista.');
            $objList->setFormula1("=clasificaciones_cliente"); //note this!
            $sheet->setDataValidation("$init:$end", $objList);
            unset($objList);
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

        if($colClasificacion!=""){
            $init = PHPExcel_Cell::stringFromColumnIndex($colClasificacion) . "2";
            $end = PHPExcel_Cell::stringFromColumnIndex($colClasificacion) . $currentRow;
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
            $objList->setFormula1("=clasificaciones"); //note this!
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
    case 'generar_layout_cliente_empresa':
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('clientes y empresas');


        $row=1;
        $headers = [
            'Cliente',
            'Telefono cliente',
            'Correo cliente',
            'Fecha de alta cliente',
            'Razon social',
            'Tipo persona',
            'RFC',
            'Facturador',
            'Regimen',
            'Sociedad',
            'Actividad economica',
            'Uso CFDI',
            'Forma de pago',
            'Direccion comercial',
            'Calle',
            'No. Ext',
            'No. Int',
            'Colonia',
            'Codigo postal',
            'Municipio',
            'Estado',
            'Pais',
            'Representante legal',
            'Nombre contacto administrativo',
            'Email contacto administrativo',
            'Tel. contacto administrativo',
            'Nombre contacto contabilidad',
            'Email contacto contabilidad',
            'Tel. contacto contabilidad',
            'Nombre contacto directivo',
            'Email contacto directivo',
            'Tel. contacto directivo',
            'Tel. celular directivo',
            'Clave CIEC',
            'Clave FIEL',
            'Clave IDSE',
            'Clave ISN',
            'Clasificacion'
        ];

        $col = 0;
        foreach($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header);
            $col++;
        }

        $row++;

        $sql = "SELECT REPLACE
        (
            REPLACE (
                REPLACE (
                    REPLACE (
                        REPLACE (
                            REPLACE ( REPLACE ( TRIM( REGEXP_REPLACE ( customer.nameContact, '\\\s{2,}', ' ' )), 'LE?N', 'LEÓN' ), 'NU?EZ', 'NUÑEZ' ),
                            'PATI?O',
                            'PATIÑO' 
                        ),
                        'VILLASE?OR',
                        'VILLASEÑOR' 
                    ),
                    'MAR?A',
                    'MARÍA' 
                ),
                'CA?IZO',
                'CAÑIZO' 
            ),
            'MU?OZ',
            'MUÑOZ' 
        ) cliente,
        TRIM(REGEXP_REPLACE ( customer.phone, '\\\s{2,}', ' ' )) cliente_telefono,
        TRIM(REGEXP_REPLACE( customer.email, '\\\s{2,}', ' ')) cliente_email,
        customer.fechaAlta fecha_registro,
        REPLACE(TRIM(REGEXP_REPLACE ( contract.NAME, '\\\s{2,}', '' )), '&amp;', '&' ) empresa,
        TRIM( contract.type ) tipo_persona,
        TRIM( contract.rfc ) rfc,
        IF( customer.noFactura13 = 'No', 'Si', 'No' ) factura_13,
        (SELECT razonSocial FROM rfc WHERE claveFacturador = contract.facturador LIMIT 1) AS facturador,
        (SELECT nombreRegimen FROM regimen WHERE regimenId = contract.regimenId LIMIT 1) AS regimen,
        (SELECT nombreSociedad FROM sociedad WHERE sociedadId = contract.sociedadId LIMIT 1) AS sociedad,
        (SELECT name FROM actividad_comercial WHERE id = contract.actividadComercialId LIMIT 1) AS actividad_economica,
        contract.claveUsoCfdi uso_cfdi,
        (SELECT descripcion FROM c_FormaPago WHERE c_FormaPago=contract.metodoDePago LIMIT 1) AS forma_pago,
        contract.direccionComercial direccion_comercial,
        contract.address calle,
        contract.noExtAddress numero_exterior,
        contract.noIntAddress numero_interior,
        contract.coloniaAddress colonia,
        contract.municipioAddress municipio,
        contract.estadoAddress estado,
        contract.paisAddress pais,
        contract.cpAddress codigo_postal,
        contract.nameRepresentanteLegal representante_legal,
        contract.nameContactoAdministrativo nombre_contacto_administrativo,
        REPLACE ( contract.emailContactoAdministrativo, '?rsteinerh@yahoo.com', 'rsteinerh@yahoo.com' ) email_contacto_administrativo,
        contract.telefonoContactoAdministrativo telefono_contacto_administrativo,
        contract.nameContactoContabilidad nombre_contacto_contabilidad,
        REPLACE ( REPLACE ( contract.emailContactoContabilidad, 'recepci?n', 'recepcion' ), '?rsteinerh@yahoo.com', 'rsteinerh@yahoo.com' ) email_contacto_contabilidad,
        contract.telefonoContactoContabilidad telefono_contacto_contabilidad,
        contract.nameContactoDirectivo nombre_contacto_directivo,
        REPLACE ( REPLACE ( contract.emailContactoDirectivo, 'recepci?n', 'recepcion' ), '?rsteinerh@yahoo.com', 'rsteinerh@yahoo.com' ) email_contacto_directivo,
        contract.telefonoContactoDirectivo telefono_contacto_directivo,
        contract.telefonoCelularDirectivo telefono_celular_directivo,
        contract.claveCiec AS clave_ciec,
        contract.claveFiel AS clave_fiel,
        contract.claveIdse AS clave_idse,
        contract.claveIsn AS clave_isn,
        contract.activo estatus_empresa,
        IF(contract.useAlternativeRzForInvoice = 1, 'Si', 'No' ) usa_dato_fiscal_alterno,
        IF(contract.useAlternativeRzForInvoice = 1, contract.alternativeRzId, NULL ) empresa_alterna,
        IF(contract.useAlternativeRzForInvoice = 1 && contract.alternativeRzId = 0, contract.alternativeType, NULL ) tipo_persona_alterna,
        IF(contract.useAlternativeRzForInvoice = 1 && contract.alternativeRzId = 0, contract.alternativeRfc, NULL ) rfc_alterna,
        IF(contract.useAlternativeRzForInvoice = 1 && contract.alternativeRzId = 0, contract.alternativeRz, NULL ) razon_social_alterna,
        IF(contract.useAlternativeRzForInvoice = 1 && contract.alternativeRzId = 0, contract.alternativeCp, NULL ) cp_alterna,
        IF(contract.useAlternativeRzForInvoice = 1 && contract.alternativeRzId = 0, contract.alternativeRegimen, NULL ) regimen_alterna,
        IF(contract.useAlternativeRzForInvoice = 1, contract.createSeparateInvoice, 0 ) generar_factura_independiente,
        (SELECT nombre FROM tipo_clasificacion WHERE id = contract.idTipoClasificacion LIMIT 1) AS clasificacion,
        (SELECT count(servicio.servicioId) 
            FROM servicio 
            INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
            WHERE servicio.contractId =contract.contractId
            AND tipoServicio.periodicidad = 'Eventual'
            AND servicio.inicioOperaciones >= '2025-04-01'
            AND tipoServicio.status='1'
            AND servicio.status = 'activo'
            ) servicios_eventuales_vigentes,
        (SELECT count(servicio.servicioId) 
                    FROM servicio 
                    INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
                    WHERE servicio.contractId =contract.contractId
                    AND tipoServicio.periodicidad != 'Eventual'
                    AND tipoServicio.status='1'
					AND servicio.status = 'activo'
                    ) servicios_vigentes
        FROM
	        contract
	    INNER JOIN customer ON contract.customerId = customer.customerId 
        WHERE
	    contract.activo = 'Si' 
	    AND customer.active = '1' 
        HAVING (servicios_eventuales_vigentes + servicios_vigentes) > 0
        ORDER BY
	    customer.nameContact ASC,
	    contract.name ASC";

        $db->setQuery($sql);
        $results = $db->GetResult();

        foreach($results as $result) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['cliente']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['cliente_telefono']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['cliente_email']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['fecha_registro']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['empresa']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['tipo_persona']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['rfc']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['facturador']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['regimen']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['sociedad']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['actividad_economica']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['uso_cfdi']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['forma_pago']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['direccion_comercial']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['calle']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['numero_exterior']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['numero_interior']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['colonia']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['codigo_postal']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['municipio']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['estado']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['pais']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['representante_legal']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['nombre_contacto_administrativo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['email_contacto_administrativo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['telefono_contacto_administrativo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['nombre_contacto_contabilidad']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['email_contacto_contabilidad']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['telefono_contacto_contabilidad']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['nombre_contacto_directivo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['email_contacto_directivo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['telefono_contacto_directivo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['telefono_celular_directivo']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['clave_ciec']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['clave_fiel']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['clave_idse']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['clave_isn']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['clasificacion']);
            $row++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "layout_clientes_empresas_para_v2.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
        break;
    case 'generar_reporte_encargado_comunicacion_cliente':

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('clientes y empresas');

        $row=1;

        $col = 0;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Cliente');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Razón social');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. asociado');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable contabilidad');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Contabilidad e Impuestos');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable nóminas');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Nóminas y Seguridad Social');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable auditoria');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Auditoria');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable legal');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Legal');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable fiscal');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Fiscal');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gerencia responsable gestoria');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Gestoria');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Cuentas por cobrar');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Resp. Atención al cliente');
        $col++;
        $row++;


        $sql = "SELECT REPLACE( 
            REPLACE (
                REPLACE (
                    REPLACE (
                        REPLACE (
                            REPLACE ( REPLACE ( TRIM( REGEXP_REPLACE ( customer.nameContact, '\\\s{2,}', ' ' )), 'LE?N', 'LEÓN' ), 'NU?EZ', 'NUÑEZ' ),
                            'PATI?O',
                            'PATIÑO' 
                        ),
                        'VILLASE?OR',
                        'VILLASEÑOR' 
                    ),
                    'MAR?A',
                    'MARÍA' 
                ),
                'CA?IZO',
                'CAÑIZO' 
            ),
            'MU?OZ',
            'MUÑOZ' 
        ) cliente,
        REPLACE(
            TRIM(REGEXP_REPLACE ( contract.name, '\\\s{2,}', '' )), '&amp;', '&' ) empresa,
        (select CONCAT(
               '[',
                GROUP_CONCAT(
                    CONCAT(
                        '{\"departamentoId',
                        '\":\"',
                        contractPermiso.departamentoId,
                        '\",\"',
                        'personalId',
                        '\":\"',
                        contractPermiso.personalId,
                        '\",\"',
                        'departamento',
                        '\":\"',
                        departamentos.departamento,
                        '\",\"',
                        'name',
                        '\":\"',
                        personal.name,
                        '\"}'
                    )
                ),
              ']'      
           ) FROM contractPermiso
               INNER JOIN personal on personal.personalId=contractPermiso.personalId
               INNER JOIN departamentos ON departamentos.departamentoId=contractPermiso.departamentoId
               WHERE contractPermiso.contractId = contract.contractId
               GROUP BY contractPermiso.contractId
        ) as encargados,
        (SELECT count(servicio.servicioId) 
            FROM servicio 
            INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
            WHERE servicio.contractId =contract.contractId
            AND tipoServicio.periodicidad = 'Eventual'
            AND servicio.inicioOperaciones >= '2025-04-01'
            AND tipoServicio.status='1'
            AND servicio.status = 'activo'
        ) servicios_eventuales_vigentes,
        (SELECT count(servicio.servicioId) 
                    FROM servicio 
                    INNER JOIN tipoServicio ON servicio.tipoServicioId=tipoServicio.tipoServicioId 
                    WHERE servicio.contractId =contract.contractId
                    AND tipoServicio.periodicidad != 'Eventual'
                    AND tipoServicio.status='1'
					AND servicio.status = 'activo'
        ) servicios_vigentes
        FROM
	        contract
	    INNER JOIN customer ON contract.customerId = customer.customerId 
      WHERE
	    contract.activo = 'Si' 
	    AND customer.active = '1' 
        HAVING (servicios_eventuales_vigentes + servicios_vigentes) > 0
        ORDER BY
	    customer.nameContact ASC,
	    contract.name ASC";

        $db->setQuery($sql);
        $results = $db->GetResult();

        $listaPersonal = $personal->getListaPersonalPuestoAsc();

        foreach($results as $result) {

            $encargados = json_decode($result['encargados'] ??  '[]',1);

            $resAsociado = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'ASOCIADO'));
            $resGerenciaContabilidad = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE CONTABILIDAD'));
            $resContabilidad = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'CONTABILIDAD E IMPUESTOS'));
            $resGerenciaNominas = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE NOMINAS'));
            $resNominas = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) ==='NOMINAS Y SEGURIDAD SOCIAL'));
            $resGerenciaAuditoria = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE AUDITORIA'));
            $resAuditoria = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'AUDITORIA'));
            $resGerenciaLegal = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE LEGAL'));
            $resLegal = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'LEGAL'));
            $resGerenciaFiscal = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE FISCAL'));
            $resFiscal = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'FISCAL'));
            $resGerenciaGestoria = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GERENCIA RESPONSABLE GESTORIA'));
            $resGestoria = current(array_filter($encargados, fn($encargado) => mb_strtoupper($encargado['departamento']) === 'GESTORIA'));
            $resCuentasPorCobrar = current(array_filter($encargados, fn($encargado) =>mb_strtoupper($encargado['departamento']) == 'CUENTAS POR COBRAR'));
            $resAtc = current(array_filter($encargados, fn($encargado) => (mb_strtoupper($encargado['departamento']) == 'ATENCION AL CLIENTE' || mb_strtoupper($encargado['departamento']) == 'ATENCIÓN AL CLIENTE')));



            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['cliente']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $result['empresa']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resAsociado['name'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resGerenciaContabilidad['name'] ?? '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resContabilidad['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resGerenciaNominas['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resNominas['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resGerenciaAuditoria['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resAuditoria['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resGerenciaLegal['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resLegal['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resGerenciaFiscal['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resFiscal['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resGerenciaGestoria['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resGestoria['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resCuentasPorCobrar['name']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,$resAtc['name']);

            $row++;
        }

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet1->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "responsables_de_comunicacion_con_clientes.xlsx";
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
        echo WEB_ROOT . "/download.php?file=" . WEB_ROOT . "/sendFiles/" . $nameFile;
        break;
}
