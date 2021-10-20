<?php

class Bono extends Personal
{

    private $nameReport;

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData()
    {
        $filtro['responsable'] = $_POST['responsableCuenta'];
        $filtro['departamento_id'] = $_POST['departamentoId'];
        $filtro['periodo'] = $_POST['periodo'];
        $filtro['year'] = $_POST['year'];
        $filtro['deep'] = 1;
        $data['subordinados'] = [];
        $data['gerente'] = [];

        $months = $this->Util()->generateMonthUntil($_POST['period'], false);
        $name_view = "instancia_" . $_POST['year'] . "_" . implode('_', $months);
        $custom_fields = ['contract_id', 'name', 'customer', 'servicio_id', 'name_service', 'departamento_id',
            'status_service', 'is_primary', 'last_date_workflow', 'instancias'];
        $add_fields_no_group = ['tipo_servicio_id', 'instancia_id', 'status', 'class', 'costo', 'fecha', 'comprobante_id'];

        $select_general  ="select c.contractId, c.name, c.customer_name, b.servicioId, b.nombreServicio, b.departamentoId, b.status,
                            b.is_primary, b.lastDateWorkflow ";
        $select_nogroup = ", b.tipoServicioId, a.instanciaServicioId as instancia_id, a.status, a.class, a.costoWorkflow as costo, a.date as fecha, a.comprobanteId as comprobante_id ";
        $select_group  = ", concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status, 'class',a.class, 
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

        $this->Util()->createOrReplaceView($name_view, $select_general.$select_group.$base_sql.$group_by.$order_by, $custom_fields);
        array_pop($custom_fields);
        $this->Util()->createOrReplaceView('nogroup_'.$name_view, $select_general.$select_nogroup.$base_sql.$order_by, array_merge($custom_fields, $add_fields_no_group));

        // crear vista no agrupada

        $this->setPersonalId($_POST['responsableCuenta']);
        $info = $this->InfoWhitRol();
        $subordinados = $this->getSubordinadosByLevel(4);
        foreach ($subordinados as $key => $sub) {
            $subordinados[$key]['propios'] = $this->getRowsBySheet($sub['personalId'], $name_view);
            $this->setPersonalId($sub['personalId']);
            $childs = $this->GetCascadeSubordinates();
            foreach ($childs as $kc => $child) {
                $childs[$kc]['propios'] = $this->getRowsBySheet($child['personalId'], $name_view);
            }
            $subordinados[$key]['childs'] = $childs;
        }

        $data['subordinados'] = $subordinados;
        $info['propios'] = $this->getRowsBySheet($_POST['responsableCuenta'], $name_view);
        $data['gerente'] = $info;

        return $data;
    }

    public function getRowsBySheet($id, $view)
    {
        global $workflow;
        $ftr_departamento = $_POST['departamentoId'] ? " and a.departamento_id in(" . $_POST['departamentoId'] . ") " : "";

        $sql = "select a.* from " . $view . " a 
                inner join contractPermiso b on a.contract_id=b.contractId
                where b.personalId in (" . $id . ") " . $ftr_departamento . " order by a.name asc, a.name_service asc ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        // E precargar array validando pasos

        foreach ($res as $key => $row_serv) {
            $valid_instancias = [];
            $instancias = json_decode($row_serv['instancias'], true);
            $valid_instancias = $this->processInstancias($row_serv, $instancias, $view);
            // $pasos = $workflow->validateStepTaskByWorkflow($value); realizarlo por procedimiento;
            // y aca validar si tiene pasos para el año se toma en cuenta.
            /*if ($row_serv['status_service'] === 'bajaParcial') {
                foreach ($instancias as $ki => $inst) {
                    if ($this->Util()->getFirstDate($inst['fecha']) <= $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                        array_push($valid_instancias, $inst);
                    }
                }
            } else $valid_instancias = $instancias;*/

            $res[$key]['instancias_array'] = $valid_instancias;
            if (count($res[$key]['instancias_array']) <= 0)
                unset($res[$key]);
        }
        return $res;
    }

    public function generateReport()
    {
        global $global_config_style_cell;
        $data = $this->generateData();
        $supervisores = $data['subordinados'];
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $months = $this->Util()->generateMonthUntil($_POST['period'], false);
        $col_title = ['Cliente', 'C. Asignado', 'Razon Social', 'Servicio'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title, $col_month_title);

        $gran_consolidado_gerente = [];
        foreach ($supervisores as $supervisor) {
            $consolidado_final = [];
            $total_por_supervisor = [];
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $name_title =  substr($supervisor["name"], 0, 6);
            $name_title =  $this->Util()->cleanString($name_title);
            $name_title = str_replace(" ", "", $name_title);
            $title_sheet = strtoupper($name_title);
            $sheet->setTitle($title_sheet);
            $col = 0;
            $row = 1;
            foreach ($col_title_mix as $title_header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $title_header)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                $col++;
            }
            $sheet->setCellValueByColumnAndRow($col, $row, 'Total devengado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, 'Total trabajado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, 'Diferencia')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);

            $row++;
            $row_init_col_total = $row;
            $totales = $this->drawRowsPropios($sheet, $months, $supervisor, $row);
            $cad['data'] = $supervisor;
            $cad['totales'] = $totales;
            array_push($consolidado_final, $cad);
            $this->drawRowTotal($sheet, $totales, $row, $months, $row_init_col_total, $total_por_supervisor);
            foreach ($supervisor['childs'] as $child) {
                $row_init_col_total = $row;
                $totales_child = $this->drawRowsPropios($sheet, $months, $child, $row);
                $this->drawRowTotal($sheet, $totales_child, $row, $months, $row_init_col_total, $total_por_supervisor);
                $cad2['data'] = $child;
                $cad2['totales'] = $totales_child;
                array_push($consolidado_final, $cad2);
            }

            $this->drawRowTotalConsolidadoPorSupervisor($sheet, $total_por_supervisor, $row);
            $total_consolidado_grupo = $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row);

            if(!is_array($gran_consolidado_gerente[$title_sheet]))
                $gran_consolidado_gerente[$title_sheet] = [];


            $cad_gran_consolidado['info_grupo'] = $supervisor;
            $cad_gran_consolidado['total_consolidado_grupo'] = $total_consolidado_grupo;

            $gran_consolidado_gerente[$title_sheet] = $cad_gran_consolidado;

            $this->drawTotalesConsolidadoGrupo($book, $sheet, $total_consolidado_grupo, $months, $row, $supervisor);
            $hoja++;
        }

        $this->drawPropiosGerente($book, $hoja, $data['gerente'], $col_title_mix, $months, $gran_consolidado_gerente);

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "BONOS_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }

    function backgroundCell($class)
    {
        $color = '';
        switch ($class) {
            case 'Completo':
            case 'CompletoTardio':
                $color = '009900';
                break;
            case 'Iniciado':
            case 'PorCompletar':
                $color = 'FFCC00';
                break;
            case 'PorIniciar':
                $color = 'FF0000';
                break;
            case 'Parcial':
                $color = '768389';
                break;
            default:
                $color = 'FFFFFF';
                break;
        }
        return $color;
    }

    function drawRowTotal(&$sheet, $totales, &$row,$months, $row_init_total, &$total_por_supervisor = [])
    {
        global $global_config_style_cell;
        $style_currency = $global_config_style_cell['style_currency_total_por_responsable'];
        $style_text = array_merge($style_currency, $global_config_style_cell['style_simple_text']);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);
        $coordenada_num_empresa = PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado;
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, 'No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, count($totales['total_contract']))
            ->getStyle($coordenada_num_empresa)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(3, $row_trabajado, 'Total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(3, $row_devengado, 'Total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_devengado)->applyFromArray($style_text);
        $col = 4;
        foreach ($totales['totales_mes'] as $ktotal => $total) {

            $coor_total_trabajado =  PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $formula = count($total['coordenada_trabajado'])
                ? '=+'.implode('+', $total['coordenada_trabajado'])
                : '';
            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                ->getStyle($coor_total_trabajado)->applyFromArray($style_currency);

            $coor_total_devengado =  PHPExcel_Cell::stringFromColumnIndex($col) . ($row + 1);
            $formula = count($total['coordenada_devengado'])
                ? '=+'.implode('+', $total['coordenada_devengado'])
                : '';
            $sheet->setCellValueByColumnAndRow($col, $row + 1, $formula)
                ->getStyle($coor_total_devengado)->applyFromArray($style_currency);
            $col++;

            if(!is_array($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['trabajados']))
                $total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['trabajados'] = [];
            if(!is_array($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['devengados']))
                $total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['devengados'] = [];

            array_push($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['trabajados'], $coor_total_trabajado);
            array_push($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['devengados'], $coor_total_devengado);
        }

        if(!count($totales['totales_mes'])) $col +=count($months);

        if(!is_array($total_por_supervisor['sum_vertical_total_horizontal_devengado']))
            $total_por_supervisor['sum_vertical_total_horizontal_devengado'] = [];
        if(!is_array($total_por_supervisor['sum_vertical_total_horizontal_trabajado']))
            $total_por_supervisor['sum_vertical_total_horizontal_trabajado'] = [];
        if(!is_array($total_por_supervisor['sum_vertical_total_horizontal_diferencia']))
            $total_por_supervisor['sum_vertical_total_horizontal_diferencia'] = [];
        if(!is_array($total_por_supervisor['sum_vertical_total_horizontal_num_empresa']))
            $total_por_supervisor['sum_vertical_total_horizontal_num_empresa'] = [];

        array_push($total_por_supervisor['sum_vertical_total_horizontal_num_empresa'], $coordenada_num_empresa);

        $col_init_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_init_total;
        $col_end_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . ($row_trabajado - 1);
        $cord_total_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray(  $style_currency );
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, '=sum('.$col_init_devengado.":".$col_end_devengado.")")
            ->getStyle($cord_total_devengado)->applyFromArray( $style_currency );
        array_push($total_por_supervisor['sum_vertical_total_horizontal_devengado'], $cord_total_devengado);


        $col++;
        $col_init_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_init_total;
        $col_end_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . ($row_trabajado - 1);
        $cord_total_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray(  $style_currency );
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, '=sum('.$col_init_trabajado.":".$col_end_trabajado.")")
            ->getStyle($cord_total_trabajado)->applyFromArray(  $style_currency );
        array_push($total_por_supervisor['sum_vertical_total_horizontal_trabajado'], $cord_total_trabajado);

        $col++;
        $cord_total_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray(  $style_currency );
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, '='.$cord_total_trabajado."-".$cord_total_devengado)
            ->getStyle($cord_total_diferencia)->applyFromArray(  $style_currency );
        array_push($total_por_supervisor['sum_vertical_total_horizontal_diferencia'], $cord_total_diferencia);

        $row += 2;
    }

    function drawRowsPropios(&$sheet, $months, $data, &$row)
    {
        global $global_config_style_cell;
        $style_general = $global_config_style_cell['style_general_col'];
        $style_text = $global_config_style_cell['style_simple_text_whit_border'];
        $return['total_contract'] = [];
        $return['totales_mes'] = [];
        foreach ($data['propios'] as $propio) {
            $col = 0;
            if (!in_array($propio['contract_id'], $return['total_contract']))
                array_push($return['total_contract'], $propio['contract_id']);

            $sheet->setCellValueByColumnAndRow($col, $row, $propio['customer'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $data['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name_service'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $col++;
            $sum_col_devengado = 0;
            $sum_col_trabajado = 0;
            foreach ($months as $month) {
                $key = array_search($month, array_column($propio['instancias_array'], 'mes'));
                $month_row = $key === false ? [] : $propio['instancias_array'][$key];
                $month_complete = $month >= 10 ? $month : "0".$month;
                if(($propio['status_service'] === 'bajaParcial' &&
                    $_POST['year']."-".$month_complete."-01" > $this->Util()->getFirstDate($propio['last_date_workflow'])) && empty($month_row)) {
                    $month_row['class'] = "Parcial";
                    $month_row['costo'] = '';
                }
                $style_general['fill']['color']['rgb'] = $this->backgroundCell($month_row['class']);
                $current_coordinate_month = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, $month_row['costo'])
                    ->getStyle($current_coordinate_month)->applyFromArray($style_general);
                $col++;


                if(in_array($month_row['class'], ['Completo', 'CompletoTardio']))
                   $sum_col_trabajado +=$month_row['costo'];

                   $sum_col_devengado +=$month_row['costo'];

                // inicializar coordenadas trabajados
                if(!is_array($return['totales_mes'][$month]['coordenada_trabajado'])) {
                    $return['totales_mes'][$month]['coordenada_trabajado'] = [];
                    $return['totales_mes'][$month]['cantidad_workflow_trabajado'] = 0;
                    $return['totales_mes'][$month]['cantidad_workflow_devengado'] = 0;
                }

                // inicializar array coordenadas devengados
                if(!is_array($return['totales_mes'][$month]['coordenada_devengado']))
                    $return['totales_mes'][$month]['coordenada_devengado'] = [];

                if(in_array($month_row['class'], ['Completo', 'CompletoTardio']) && (int)$month_row['secondary_pending'] === 0){
                    $return['totales_mes'][$month]['total_trabajado'] += $month_row['costo'];
                    array_push($return['totales_mes'][$month]['coordenada_trabajado'], $current_coordinate_month);
                }

                if(in_array($month_row['class'], ['Completo', 'CompletoTardio']))
                    $return['totales_mes'][$month]['cantidad_workflow_trabajado']++;

                if(!empty($month_row) && $month_row['class'] !== "Parcial")
                    $return['totales_mes'][$month]['cantidad_workflow_devengado']++;

                $return['totales_mes'][$month]['total_devengado'] += $month_row['costo'];
                array_push($return['totales_mes'][$month]['coordenada_devengado'], $current_coordinate_month);
            }
            $col_total_dev = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, $sum_col_devengado)
                ->getStyle($col_total_dev)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $col_total_trab = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, $sum_col_trabajado)
                ->getStyle($col_total_trab)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $col_total_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, '='.$col_total_trab."-".$col_total_dev)
                ->getStyle($col_total_diferencia)->applyFromArray($global_config_style_cell['style_currency']);
            $row++;
        }
        return $return;
    }

    function drawsTotalesFinal(&$book, $sheet, $data, $months, &$row)
    {
        global $global_config_style_cell, $global_bonos;
        $stack_bono[1] = 5;
        $stack_bono[2] = 4;
        $stack_bono[3] = 3;
        $stack_bono[4] = 3;
        $stack_bono[5] = 2;
        $stack_bono[6] = 1;

        $total_consolidado_grupo['row_devengado'] = [];
        $total_consolidado_grupo['row_trabajado'] = [];
        $total_consolidado_grupo['row_gasto'] = [];
        $total_consolidado_grupo['row_porcent_bono'] = [];
        $total_consolidado_grupo['row_bono'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_trabajado'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_devengando'] = [];

        foreach ($data as $total) {
            $row_nombre = $row;
            $sheet->setCellValueByColumnAndRow(3, $row, 'Nombre')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);
            $sheet->setCellValueByColumnAndRow(4, $row, $total['data']['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(4) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_devengado = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_devengado, 'Ingreso devengado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_devengado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_trabajado = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_trabajado, 'Ingreso trabajado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_gasto = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_gasto, 'Gastos')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_utilidad = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_utilidad, 'Utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);
            /*$row++;
            $row_porcent_bono = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_porcent_bono, '% Bono')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcent_bono)->applyFromArray($global_config_style_cell['style_grantotal']);
            */
            $row++;
            $row_bono = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_bono, 'Bono entregado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);
            /*$row++;
            $row_bono_entregado = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_bono_entregado, 'Bono entregado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono_entregado)->applyFromArray($global_config_style_cell['style_grantotal']);*/
            $row++;
            $row_porcentefectividad = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_porcentefectividad, 'Porcentaje de efectividad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_porcentutilidad = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_porcentutilidad, 'Porcentaje de utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_porcentcrecimiento = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_porcentcrecimiento, 'Porcentaje de crecimiento')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);
            $col = 4;
            $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;;
            foreach ($total['totales']['totales_mes'] as $key_month => $total_mes) {

                $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
                $formula = count($total_mes['coordenada_devengado']) ? '=+'.implode('+', $total_mes['coordenada_devengado']) : '';
                $sheet->setCellValueByColumnAndRow($col, $row_devengado, $formula)
                    ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_devengado'][$key_month])) $total_consolidado_grupo['row_devengado'][$key_month]= [];
                array_push($total_consolidado_grupo['row_devengado'][$key_month], $cordinate_devengado);

                $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
                $formula = count($total_mes['coordenada_trabajado']) ? '=+'.implode('+', $total_mes['coordenada_trabajado']) : '';
                $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $formula)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_trabajado'][$key_month])) $total_consolidado_grupo['row_trabajado'][$key_month]= [];
                array_push($total_consolidado_grupo['row_trabajado'][$key_month], $cordinate_trabajado);

                $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
                $sheet->setCellValueByColumnAndRow($col, $row_gasto, (double)$total['data']['sueldo'] * 1.4)
                    ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_gasto'][$key_month])) $total_consolidado_grupo['row_gasto'][$key_month]= [];
                array_push($total_consolidado_grupo['row_gasto'][$key_month], $cordinate_gasto);

                $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                    ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

                /*$cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcent_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_porcent_bono, $stack_bono[$total['data']['nivel']] / 100)
                    ->getStyle($cordinate_porcent_bono)->applyFromArray($global_config_style_cell['style_porcent']);
                if(!is_array($total_consolidado_grupo['row_porcent_bono'][$key_month])) $total_consolidado_grupo['row_porcent_bono'][$key_month]= [];
                array_push($total_consolidado_grupo['row_porcent_bono'][$key_month], $cordinate_porcent_bono);*/

                /*$formula_bono = $total_mes['total_devengando'] - $total_mes['total_trabajado'] <= 0
                                ? '=+'.$global_bonos[$total['data']['nivel']]
                                : 0;*/
                $formula_bono = '=IF(('.$cordinate_devengado.'-'.$cordinate_trabajado.') <= 0,'.$global_bonos[$total['data']['nivel']].',0)';
                $formula_bono = (($total_mes['cantidad_workflow_devengado'] - $total_mes['cantidad_workflow_trabajado']) == 0 && $total_mes['total_devengado'] > 0)
                                ? $formula_bono
                                : 0;

                $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_bono, '')
                    ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_bono'][$key_month])) $total_consolidado_grupo['row_bono'][$key_month]= [];
                array_push($total_consolidado_grupo['row_bono'][$key_month], $cordinate_bono);

                /*$cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
                $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, '')
                    ->getStyle($cordinate_bono_entregado)->applyFromArray($global_config_style_cell['style_currency']);*/

                $formula_efectividad = '=IFERROR((+'.$total_mes['cantidad_workflow_trabajado'].'/'.$total_mes['cantidad_workflow_devengado'].'),0)';
                $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                    ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_devengado.',0)')
                    ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;

                $valor = (int)$key_month === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_base_devengando.')-1,0)';
                $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                    ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

                $total_consolidado_grupo['gran_cantidad_workflow_devengado'][$key_month] ['total'] += $total_mes['cantidad_workflow_devengado'];
                $total_consolidado_grupo['gran_cantidad_workflow_trabajado'][$key_month] ['total'] += $total_mes['cantidad_workflow_trabajado'];

                $col++;

            }

            $merges = PHPExcel_Cell::stringFromColumnIndex(4) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months)+3) . $row_nombre;
            $book->getActiveSheet()->mergeCells($merges);
            $row += 2;
        }
        return $total_consolidado_grupo;
    }

    function drawRowTotalConsolidadoPorSupervisor(&$sheet, $totales, &$row)
    {
        global $global_config_style_cell;
        $row++;
        $style_currency = $global_config_style_cell['style_currency_total_por_responsable'];
        $style_text = array_merge($style_currency, $global_config_style_cell['style_simple_text']);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, 'Gran No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado)->applyFromArray($style_text);

        $formula = count($totales['sum_vertical_total_horizontal_num_empresa'])
                ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_num_empresa'])
                : '';
        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, $formula)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($style_currency);
        $sheet->setCellValueByColumnAndRow(3, $row_trabajado, 'Gran total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(3, $row_devengado, 'Gran total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_devengado)->applyFromArray($style_text);
        $col = 4;

        foreach ($totales['total_concentrado_vertical_meses'] as $total) {
            $coor_total_trabajado =  PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $formula = count($total['trabajados']) ? '=+'.implode('+', $total['trabajados']) : '';
            $sheet->setCellValueByColumnAndRow($col, $row,  $formula)
                ->getStyle($coor_total_trabajado)->applyFromArray($style_currency);
            $coor_total_devengado =  PHPExcel_Cell::stringFromColumnIndex($col) . ($row + 1);
            $formula = count($total['devengados']) ? '=+'.implode('+', $total['devengados']) : '';
            $sheet->setCellValueByColumnAndRow($col, $row + 1, $formula)
                ->getStyle($coor_total_devengado)->applyFromArray($style_currency);
            $col++;
        }

        $coord_sum_vertical_total_hor_dev =  PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($style_currency);
        $formula = count($totales['sum_vertical_total_horizontal_devengado'])
                ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_devengado'])
                : '';
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, $formula)
            ->getStyle($coord_sum_vertical_total_hor_dev)->applyFromArray($style_currency);

        $col++;
        $coord_sum_vertical_total_hor_trab =  PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($style_currency);
        $formula = count($totales['sum_vertical_total_horizontal_trabajado'])
            ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_trabajado'])
            : '';
        $sheet->setCellValueByColumnAndRow($col, $row_devengado,  $formula)
            ->getStyle($coord_sum_vertical_total_hor_trab)->applyFromArray($style_currency);

        $col++;
        $coord_sum_vertical_total_hor_dif =  PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray(  $style_currency );
        $formula = count($totales['sum_vertical_total_horizontal_diferencia'])
            ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_diferencia'])
            : '';
        $sheet->setCellValueByColumnAndRow($col, $row_devengado,  $formula)
            ->getStyle($coord_sum_vertical_total_hor_dif)->applyFromArray($style_currency);

        $row += 2;
    }

    function drawTotalesConsolidadoGrupo(&$book, $sheet, $data, $months, &$row, $info_grupo, $prefix_sheet = '') {
        global $global_config_style_cell;

        $col =  4;
        $row_nombre = ++$row;
        $row_devengando = ++$row;
        $row_trabajado = ++$row;
        $row_gasto = ++$row;
        $row_utilidad = ++$row;
        /*$row_porcent_bono = ++$row;*/
        $row_bono = ++$row;
        //$row_bono_entregado = ++$row;
        $row_porcentefectividad = ++$row;
        $row_porcentutilidad = ++$row;
        $row_porcentcrecimiento = ++$row;


        $sheet->setCellValueByColumnAndRow(3, $row_nombre, 'Nombre')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(4, $row_nombre, "GRUPO SUPERVISOR ". strtoupper($info_grupo['name']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(4) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_devengando, 'Ingreso devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_devengando)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_trabajado, 'Ingreso trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
        $sheet->setCellValueByColumnAndRow(3, $row_gasto, 'Gastos')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_utilidad, 'Utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        /*$sheet->setCellValueByColumnAndRow(3, $row_porcent_bono, '% Bono')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcent_bono)->applyFromArray($global_config_style_cell['style_grantotal']);*/

        $sheet->setCellValueByColumnAndRow(3, $row_bono, 'Bono entregado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

        /*$sheet->setCellValueByColumnAndRow(3, $row_bono_entregado, 'Bono entregado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono_entregado)->applyFromArray($global_config_style_cell['style_grantotal']);*/

        $sheet->setCellValueByColumnAndRow(3, $row_porcentefectividad, 'Porcentaje efectividad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_porcentutilidad, 'Porcentaje utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_porcentcrecimiento, 'Porcentaje crecimiento')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);

        $prefix_sheet = $prefix_sheet==='' ? '' : $prefix_sheet."!";
        $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        foreach($data['row_devengado'] as $key_mes => $total_mes) {

            $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
            $sheet->setCellValueByColumnAndRow($col, $row_devengando, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_devengado'][$key_mes]))
                ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
            $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_trabajado'][$key_mes]))
                ->getStyle($cordinate_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
            $sheet->setCellValueByColumnAndRow($col, $row_gasto, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_gasto'][$key_mes]))
                ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

            /*$cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcent_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_porcent_bono, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_porcent_bono'][$key_mes]))
                ->getStyle($cordinate_porcent_bono)->applyFromArray($global_config_style_cell['style_porcent']);*/

            //$formula_bono = '=IF(('.$cordinate_devengado.'-'.$cordinate_trabajado.') <= 0,'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono'][$key_mes]).',0)';
            $formula_bono = '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono'][$key_mes]);
            $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_bono, $formula_bono)
                ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);

            /*$cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
            $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, '')
                ->getStyle($cordinate_bono_entregado)->applyFromArray($global_config_style_cell['style_currency']);*/

            $formula_efectividad = '=IFERROR((+'.$data['gran_cantidad_workflow_trabajado'][$key_mes]['total'].'/'.$data['gran_cantidad_workflow_devengado'][$key_mes]['total'].'),0)';
            $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_devengado.',0)')
                ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

            $valor = (int)$key_mes === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_base_devengando.')-1,0)';
            $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

            $col++;
        }
        $merges = PHPExcel_Cell::stringFromColumnIndex(4) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months) + 3) . $row_nombre;
        $book->getActiveSheet()->mergeCells($merges);
    }

    function drawPropiosGerente(&$book, $hoja, $data, $col_title_mix, $months, $gran_consolidado_gerente) {
        global $global_config_style_cell;
        $sheet = $book->createSheet($hoja);
        $name_title =  substr($data["name"], 0, 6);
        $name_title =  $this->Util()->cleanString($name_title);
        $name_title = str_replace(" ", "", $name_title);
        $title_sheet = strtoupper($name_title);

        $sheet->setTitle($title_sheet);
        $col = 0;
        $row = 1;
        foreach ($col_title_mix as $title_header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $title_header)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, $row, 'Total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Diferencia')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);

        $row++;
        $row_init_col_total = $row;
        $consolidado_final = [];
        $totales_gerente = $this->drawRowsPropios($sheet, $months, $data, $row);
        $cad['data'] = $data;
        $cad['totales'] = $totales_gerente;
        array_push($consolidado_final, $cad);
        $total_por_gerente = [];
        $this->drawRowTotal($sheet, $totales_gerente, $row, $months, $row_init_col_total, $total_por_gerente);
        $this->drawRowTotalConsolidadoPorSupervisor($sheet, $total_por_gerente, $row);

        foreach ($gran_consolidado_gerente as $key => $value) {
            $this->drawTotalesConsolidadoGrupo($book, $sheet, $value['total_consolidado_grupo'], $months, $row, $value['info_grupo'], $key);
            $row += 1;
        }
        $row += 1;
        $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row);
    }

    function processInstancias ($row_serv, $instancias, $view) {
        global $workflow;
        $instancias_filtered = [];
        foreach($instancias as $inst) {
            $cad = $inst;
            //los rif el dia que se abren deven valer doble
            if(in_array((int)$inst['tipo_servicio_id'], [RIF, RIFAUDITADO]))
                $cad['costo'] = $cad['costo'] * 2;

            if ($row_serv['status_service'] === 'bajaParcial' && $this->Util()->getFirstDate($inst['fecha']) > $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                $cad['class'] = 'Parcial';
                $cad['costo'] = 0;
            }
            // si no es primario es secondario por default.
            $cad['costo'] = !$row_serv['is_primary'] ? 0 : $cad['costo'];
            if($row_serv['is_primary']) {
                $month = (int) date('m', strtotime($inst['fecha']));
                $year = (int) date('Y', strtotime($inst['fecha']));
                $cad['secondary_pending'] = $this->verifySecondary($row_serv['contract_id'], $inst['tipo_servicio_id'], $month, $year, $view);
            }
            $cad2['finstancia'] = $cad['fecha'];
            $cad2['tipoServicioId'] = $cad['tipo_servicio_id'];
            $pasos = $workflow->validateStepTaskByWorkflow($cad2);
            if(!count($pasos))
                continue;
          array_push($instancias_filtered, $cad);
        }
        return $instancias_filtered;
    }

    function verifySecondary($contract_id, $tipo_servicio_id, $month, $year, $view) {
        $database_prospect =  SQL_DATABASE_PROSPECT;
        $sql = "call sp_verify_secondary($contract_id, $tipo_servicio_id, $month, $year, 'nogroup_$view', '$database_prospect')";
        $this->Util()->DB()->setQuery($sql);
        $complete_secondary = $this->Util()->DB()->GetSingle();
        return $complete_secondary;
    }
}
