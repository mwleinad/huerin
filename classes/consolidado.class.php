<?php

class Consolidado extends Personal
{

    private $nameReport;

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData()
    {
        $strFilter = '';
        $months = $this->Util()->generateMonthUntil($_POST['period'], false);

        $name_view = "instancia_" . $_POST['year'] . "_" . implode('_', $months);
        $custom_fields = ['contract_id', 'name', 'customer', 'servicio_id', 'name_service', 'departamento_id',
            'status_service', 'is_primary', 'last_date_workflow', 'instancias'];
        $add_fields_no_group = ['tipo_servicio_id', 'instancia_id', 'status', 'class', 'costo', 'fecha', 'comprobante_id'];

        $select_general = "select c.contractId, c.name, c.customer_name, b.servicioId, b.nombreServicio, b.departamentoId, b.status,
                            b.is_primary, b.lastDateWorkflow ";
        $select_nogroup = ", b.tipoServicioId, a.instanciaServicioId as instancia_id, a.status, a.class, a.costoWorkflow as costo, a.date as fecha, a.comprobanteId as comprobante_id ";
        $select_group = ", concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status, 'class',a.class, 
                      'costo', a.costoWorkflow,  'fecha', a.date, 'tipo_servicio_id', b.tipoServicioId, 'comprobante_id', a.comprobanteId, 'mes', month(a.date))),  ']') as instancias ";
        $group_by = " group by a.servicioId ";
        $order_by = "order by a.date asc ";

        $base_sql = "from instanciaServicio a
               inner join (select servicio.servicioId,servicio.contractId, servicio.tipoServicioId, servicio.status, 
                           tipoServicio.nombreServicio, tipoServicio.periodicidad, tipoServicio.departamentoId,
                           tipoServicio.is_primary, servicio.lastDateWorkflow from servicio 
                           inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId
                           where tipoServicio.status='1') b on a.servicioId=b.servicioId
               inner join (select contract.contractId, contract.name, customer.nameContact as customer_name
                           from contract inner join customer on contract.customerId = customer.customerId) c
                           on b.contractId=c.contractId
               where year(a.date)=" . $_POST['year'] . " and month(a.date) in (" . implode(',', $months) . ")";
        $this->Util()->createOrReplaceView($name_view, $select_general . $select_group . $base_sql . $group_by . $order_by, $custom_fields);
        array_pop($custom_fields);
        $this->Util()->createOrReplaceView('nogroup_' . $name_view, $select_general . $select_nogroup . $base_sql . $order_by, array_merge($custom_fields, $add_fields_no_group));


        if ($_POST["responsableCuenta"])
            $strFilter .= " and a.personalId = '" . $_POST['responsableCuenta'] . "' ";

        $sql = "select a.*, b.nivel,c.departamento, b.name as name_rol from personal a
                inner join roles b on a.roleId = b.rolId
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();

        $cleaned_gerentes = [];
        foreach ($gerentes as $gerente) {
            $this->setPersonalId($gerente['personalId']);
            $subordinados = $this->SubordinadosDetailsAddPass();
            $item_gerente = $gerente;
            $cleaned_subordinados = [];
            foreach ($subordinados as $key => $sub) {
                unset($sub['children']);
                $sub['propios'] = $this->getRowsPropios($sub['personalId'], $name_view);
                array_push($cleaned_subordinados, $sub);
            }
            if (count($cleaned_subordinados)) {
                $item_gerente['subordinados_cascada'] = $cleaned_subordinados;
                array_push($cleaned_gerentes, $item_gerente);
            }
        }

        return $cleaned_gerentes;
    }

    public function getRowsPropios($id, $view)
    {
        $ftr_departamento = $_POST['departamentoId'] ? " and a.departamento_id in(" . $_POST['departamentoId'] . ") " : "";

        $sql = "select a.* from " . $view . " a 
                inner join contractPermiso b on a.contract_id=b.contractId
                where b.personalId in (" . $id . ") " . $ftr_departamento . " order by a.name asc, a.name_service asc ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        foreach ($res as $key => $row_serv) {
            $instancias = json_decode($row_serv['instancias'], true);
            $valid_instancias = $this->processInstancias($row_serv, $instancias, $view);
            $res[$key]['instancias_array'] = $valid_instancias;
            if (count($res[$key]['instancias_array']) <= 0)
                unset($res[$key]);
        }
        return $res;
    }

    function processInstancias($row_serv, $instancias, $view)
    {
        global $workflow;
        $instancias_filtered = [];
        foreach ($instancias as $inst) {
            $cad = $inst;
            //los rif el dia que se abren deven valer doble
            if (in_array((int)$inst['tipo_servicio_id'], [RIF, RIFAUDITADO]))
                $cad['costo'] = $cad['costo'] * 2;

            if ($row_serv['status_service'] === 'bajaParcial' && $this->Util()->getFirstDate($inst['fecha']) > $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                $cad['class'] = 'Parcial';
                $cad['costo'] = 0;
            }
            // si no es primario es secondario por default.
            $cad['costo'] = !$row_serv['is_primary'] ? 0 : $cad['costo'];
            if ($row_serv['is_primary']) {
                $month = (int)date('m', strtotime($inst['fecha']));
                $year = (int)date('Y', strtotime($inst['fecha']));
                $cad['secondary_pending'] = $this->verifySecondary($row_serv['contract_id'], $inst['tipo_servicio_id'], $month, $year, $view);
            }
            $cad2['finstancia'] = $cad['fecha'];
            $cad2['tipoServicioId'] = $cad['tipo_servicio_id'];
            $pasos = $workflow->validateStepTaskByWorkflow($cad2);
            if (!count($pasos))
                continue;
            array_push($instancias_filtered, $cad);
        }
        return $instancias_filtered;
    }

    function verifySecondary($contract_id, $tipo_servicio_id, $month, $year, $view)
    {
        $database_prospect = SQL_DATABASE_PROSPECT;
        $sql = "call sp_verify_secondary($contract_id, $tipo_servicio_id, $month, $year, 'nogroup_$view', '$database_prospect')";
        $this->Util()->DB()->setQuery($sql);
        $complete_secondary = $this->Util()->DB()->GetSingle();
        return $complete_secondary;
    }

    public function calcularEstadoResultado()
    {
        global $comprobante;

        $gerentes = $this->generateData();
        $devengados = [];
        $months = $this->Util()->generateMonthUntil($_POST['period'], false);
        $gerentes_filtered = [];
        foreach ($gerentes as $gerente) {
            $cad_gerente = $gerente;
            $totales_sub = [];
            foreach ($gerente['subordinados_cascada'] as $data) {
                $cad = $data;
                $propios_meses = [];
                foreach ($data['propios'] as $propio) {
                    foreach ($months as $month) {
                        $key = array_search($month, array_column($propio['instancias_array'], 'mes'));
                        $month_row = $key === false ? [] : $propio['instancias_array'][$key];
                        $month_complete = $month >= 10 ? $month : "0" . $month;
                        if (($propio['status_service'] === 'bajaParcial' &&
                                $_POST['year'] . "-" . $month_complete . "-01" > $this->Util()->getFirstDate($propio['last_date_workflow'])) && empty($month_row)) {
                            $month_row['class'] = "Parcial";
                            $month_row['costo'] = '';
                        }
                        $propios_meses[$month]['trabajado'] += in_array($month_row['class'], ['Completo', 'CompletoTardio']) && (int)$month_row['secondary_pending'] === 0
                            ? $month_row['costo']
                            : 0;
                        $propios_meses[$month]['devengado'] += $month_row['costo'];
                        $saldo = $month_row['comprobante_id'] ? $comprobante->GetInfoComprobante($month_row['comprobante_id'])['saldo'] : 0;
                        $propios_meses[$month]['cobrado'] += $saldo <= 0 && $month_row['comprobante_id']
                            ? $month_row['costo'] : 0;
                    }
                }
                $cad['meses_totales'] = $propios_meses;
                unset($cad['propios']);
                array_push($totales_sub, $cad);
            }
            $cad_gerente['subordinados_filtered'] = $totales_sub;
            if (count($totales_sub))
                array_push($gerentes_filtered, $cad_gerente);
        }
        return $gerentes_filtered;
    }

    public function generateReport()
    {
        global $global_config_style_cell;
        $gerentes_filtered = $this->calcularEstadoResultado();

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $months = $this->Util()->generateMonthUntil($_POST['period'], false);
        $col_title = ['Area', 'Encargado'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title, $col_month_title);

        foreach ($gerentes_filtered as $gerente) {
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $title_sheet = trim(strtoupper(substr($gerente["name"], 0, 6)));
            $sheet->setTitle($title_sheet);
            $col = 0;
            $row = 1;
            foreach ($col_title_mix as $title_header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $title_header)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                if ($col > 1) {
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col, $row, '%')
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                }
                $col++;
            }

            $row++;
            $data_total['devengado'] =  $this->drawSection($sheet, $row, $months, 'DEVENGADOS', $gerente['subordinados_filtered'], 'devengado');
            $data_total['trabajado'] = $this->drawSection($sheet, $row, $months, 'TRABAJADOS', $gerente['subordinados_filtered'], 'trabajado');
            $data_total['cobrado'] = $this->drawSection($sheet, $row, $months, 'COBRADOS', $gerente['subordinados_filtered'], 'cobrado');
            $data_total['nomina'] = $this->drawSectionNomina($sheet, $row, $months, $gerente['subordinados_filtered']);
            $data_total['utilidad'] = $this->drawSectionUtilidad($sheet, $row, $months, $gerente['subordinados_filtered']);
            $this->drawGranTotal($sheet, $row, $months, $data_total);

            $hoja++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "EDO_RES_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }

    function drawSection(&$sheet, &$row, $months, $title_section, $data, $key)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, $title_section)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach ($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, $mes_total[$key])
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL ' . $title_section))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row_final = $row;
        $row += 3;

        return $row_final;
    }

    function drawSectionNomina(&$sheet, &$row, $months, $data)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'NOMINA')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach ($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, (double)$item['sueldo'] * 1.4)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL NOMINAS'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row_final = $row;
        $row += 3;
        return $row_final;
    }

    function drawSectionUtilidad(&$sheet, &$row, $months, $data)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'UTILIDAD')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach ($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, (double)$mes_total['trabajado'] - (double)$item['sueldo'] * 1.4)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL UTILIDAD'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row_final = $row;
        $row += 3;

        return $row_final;
    }

    function drawGranTotal(&$sheet, &$row, $months, $row_final = [])
    {

        global $global_config_style_cell;
        $row_devengado = $row;
        foreach ($row_final as $key => $row_total) {
            $col = 2;
            $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('gran total ' . $key))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

            foreach ($months as $month) {
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_total;
                $current_col_total = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
                $sheet->setCellValueByColumnAndRow($col, $row, "=+" . $current_col_total_section . "")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR((+" .$current_col_total."/".$current_col_devengado."),0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

    }
}