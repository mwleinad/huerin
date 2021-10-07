<?php
class Consolidado extends Personal {

    private $nameReport;

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData()
    {
        $strFilter =  '';
        $months = $this->Util()->generateMonthByPeriod($_POST['period'], false);
        $name_view = "instancia_" . $_POST['year'] . "_" . implode('_', $months);
        $custom_fiels = ['contract_id', 'name', 'customer', 'servicio_id', 'name_service', 'departamento_id',
            'status_service', 'is_primary', 'last_date_workflow', 'instancias'];
        $sql = "select c.contractId, c.name,c.customer_name, b.servicioId, b.nombreServicio,b.departamentoId,b.status,b.is_primary, b.lastDateWorkflow,
                      concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status, 'class',a.class, 
                      'costo', a.costoWorkflow,  'fecha', a.date, 'comprobante_id', a.comprobanteId, 'mes', month(a.date))),  ']') as instancias
               from instanciaServicio a
               inner join (select servicio.servicioId,servicio.contractId, servicio.tipoServicioId, servicio.status, 
                           tipoServicio.nombreServicio, tipoServicio.periodicidad, tipoServicio.departamentoId,
                           tipoServicio.is_primary, servicio.lastDateWorkflow from servicio 
                           inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId
                           where tipoServicio.status='1') b on a.servicioId=b.servicioId
               inner join (select contract.contractId, contract.name, customer.nameContact as customer_name
                           from contract inner join customer on contract.customerId = customer.customerId) c
                           on b.contractId=c.contractId
               where year(a.date)=" . $_POST['year'] . " and month(a.date) in (" . implode(',', $months) . ") 
               group by a.servicioId  order by a.date asc";
        $this->Util()->createOrReplaceView($name_view, $sql, $custom_fiels);

        if($_POST["responsableCuenta"])
            $strFilter .= " and a.personalId = '".$_POST['responsableCuenta']."' ";

        $sql = "select a.*, b.nivel,c.departamento, b.name as name_rol from personal a
                inner join roles b on a.roleId = b.rolId
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();

        $cleaned_gerentes = [];
        foreach($gerentes as $gerente) {
            $this->setPersonalId($gerente['personalId']);
            $subordinados = $this->SubordinadosDetailsAddPass();
            $item_gerente = $gerente;
            $cleaned_subordinados = [];
            foreach ($subordinados as $key => $sub) {
                unset($sub['children']);
                $sub['propios'] = $this->getRowsPropios($sub['personalId'], $name_view);
                array_push($cleaned_subordinados, $sub);
            }
            if(count($cleaned_subordinados)) {
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
            $valid_instancias = [];
            $instancias = json_decode($row_serv['instancias'], true);
            if ($row_serv['status_service'] === 'bajaParcial') {
                foreach ($instancias as $ki => $inst) {
                    if ($this->Util()->getFirstDate($inst['fecha']) <= $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                        array_push($valid_instancias, $inst);
                    }
                }
            } else $valid_instancias = $instancias;

            $res[$key]['instancias_array'] = $valid_instancias;
            if (count($res[$key]['instancias_array']) <= 0)
                unset($res[$key]);
        }
        return $res;
    }

    public function calcularEstadoResultado() {
        global $comprobante;

        $gerentes =  $this->generateData();
        $devengados = [];
        $months = $this->Util()->generateMonthByPeriod($_POST['period'], false);
        $gerentes_filtered =[];
        foreach($gerentes as $gerente) {
            $cad_gerente = $gerente;
            $totales_sub = [];
            foreach($gerente['subordinados_cascada'] as $data) {
                $cad = $data;
                $propios_meses = [];
                foreach($data['propios'] as $propio) {
                    foreach ($months as $month) {
                        $key = array_search($month, array_column($propio['instancias_array'], 'mes'));
                        $month_row = $key === false ? [] : $propio['instancias_array'][$key];
                        $propios_meses[$month]['trabajado'] += in_array($month_row['class'], ['Completo', 'CompletoTardio'])
                            ? $month_row['costo']
                            : 0;
                        $propios_meses[$month]['devengado'] += $month_row['costo'];
                        $saldo = $month_row['comprobante_id'] ? $comprobante->GetInfoComprobante($month_row['comprobante_id'])['saldo'] : 0;
                        $propios_meses[$month]['cobrado'] += $saldo <=0 && $month_row['comprobante_id']
                            ? $month_row['costo'] : 0;
                    }
                }
                $cad['meses_totales'] = $propios_meses;
                unset($cad['propios']);
                array_push($totales_sub, $cad);
            }
            $cad_gerente['subordinados_filtered'] = $totales_sub;
            if(count($totales_sub))
                array_push($gerentes_filtered, $cad_gerente);
        }
        return $gerentes_filtered;
    }

    public function generateReport () {
        global $global_config_style_cell;
        $gerentes_filtered = $this->calcularEstadoResultado();

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $months = $this->Util()->generateMonthByPeriod($_POST['period'], false);
        $col_title = ['Area', 'Encargado'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title, $col_month_title);

        foreach($gerentes_filtered as $gerente) {
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $title_sheet = trim(strtoupper(substr($gerente["name"], 0, 6)));
            $sheet->setTitle($title_sheet);
            $col = 0;
            $row = 1;
            foreach ($col_title_mix as $title_header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $title_header)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                if($col > 1) {
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col, $row, '%')
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                }
                $col++;
            }

            $row++;
            $this->drawSection($sheet, $row, $months, 'DEVENGADOS', $gerente['subordinados_filtered'], 'devengado');
            $this->drawSection($sheet, $row, $months, 'TRABAJADOS', $gerente['subordinados_filtered'], 'trabajado');
            $this->drawSection($sheet, $row, $months, 'COBRADOS', $gerente['subordinados_filtered'], 'cobrado');
            $this->drawSectionNomina($sheet, $row, $months,  $gerente['subordinados_filtered']);
            $this->drawSectionUtilidad($sheet, $row, $months,  $gerente['subordinados_filtered']);

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

    function drawSection (&$sheet, &$row, $months, $title_section, $data, $key) {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, $title_section)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section  = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, $mes_total[$key])
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row,"=IFERROR(".$current_col_cordinate."/".$current_col_total_section.",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL '.$title_section))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row +=3;
    }

    function drawSectionNomina(&$sheet, &$row, $months, $data) {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'NOMINA')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section  = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row,(double)$item['sueldo'] * 1.4)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row,"=IFERROR(".$current_col_cordinate."/".$current_col_total_section.",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL NOMINAS'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row +=3;
    }
    function drawSectionUtilidad(&$sheet, &$row, $months, $data) {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'UTILIDAD')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section  = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, (double)$mes_total['trabajado'] - (double)$item['sueldo'] * 1.4)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row,"=IFERROR(".$current_col_cordinate."/".$current_col_total_section.",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL UTILIDAD'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        foreach ($months as $month) {
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,"=SUM(".PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section.":".
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $row +=3;
    }
}