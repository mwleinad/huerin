<?php

class BonoCobranza extends Personal
{

    private $nameReport;

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData()
    {
        $filtro['responsable'] = $_POST['responsableCuenta'];
        $filtro['periodo'] = $_POST['periodo'];
        $filtro['year'] = $_POST['year'];

        $months = $this->Util()->generateMonthUntil($_POST['period'], false);
        $mFinal = end($months);
        $mInicial = reset($months);
        $this->setPersonalId($_POST['responsableCuenta']);
        $info = $this->InfoWhitRol();
        $subordinados = $this->getSubordinadosByLevel(4);
        foreach ($subordinados as $key => $sub) {
            $subordinados[$key]['propios'] = $this->getRowsBySheet($sub['personalId'], $mInicial, $mFinal, $_POST['year']);
            $this->setPersonalId($sub['personalId']);
            $childs = $this->GetCascadeSubordinates();
            foreach ($childs as $kc => $child) {
                $childs[$kc]['propios'] = $this->getRowsBySheet($child['personalId'], $mInicial, $mFinal, $_POST['year']);
            }
            $subordinados[$key]['childs'] = $childs;
        }
        $data['subordinados'] = $subordinados;
        $info['propios'] =  $this->getRowsBySheet($_POST['responsableCuenta'], $mInicial, $mFinal, $_POST['year']);;
        $data['gerente'] = $info;

        return $data;
    }

    //obtener las filas por responsable
    public function getRowsBySheet($id, $mesInicial, $mesFinal, $year)
    {
        $sql = "call sp_emp_comp_por_mes($id,$mesInicial, $mesFinal, $year)";
        $this->Util()->DB()->setQuery($sql);
        $result =  $this->Util()->DB()->GetResult();
        foreach($result as $key => $value) {
            $facturas =  json_decode($value['factura'], true);
            foreach ($facturas as $factura) {
                $result[$key]['factura_array'][] = $factura;
            }
        }
        return $result;
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
        $col_title = ['Cliente', 'Razon social', 'Responsable cxc'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title, $col_month_title);

        $gran_consolidado_gerente   = [];
        foreach ($supervisores as $supervisor) {
            $consolidado_final = [];
            $total_por_supervisor = [];
            if((int)$supervisor['departamentoId'] !== 21)
                continue;

            if ($hoja != 0)
                $sheet  = $book->createSheet($hoja);
            $name_title     =  substr($supervisor["name"], 0, 6);
            $name_title     =  $this->Util()->cleanString($name_title);
            $name_title     = str_replace(" ", "", $name_title);
            $title_sheet    = strtoupper($name_title);
            $sheet->setTitle($title_sheet);
            $col = 0;
            $row = 1;
            foreach ($col_title_mix as $title_header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $title_header)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
                $col++;
            }
            $sheet->setCellValueByColumnAndRow($col, $row, 'Total facturado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, 'Total cobrado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, 'Diferencia')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);

            $row++;
            $row_init_col_total = $row;
            $totales        = $this->drawRowsPropios($sheet, $months, $supervisor, $row);
            $cad['data']    = $supervisor;
            $cad['totales'] = $totales;
            array_push($consolidado_final, $cad);
            $this->drawRowTotal($sheet, $totales, $row, $months, $row_init_col_total, $total_por_supervisor);
            foreach ($supervisor['childs'] as $child) {
                $row_init_col_total = $row;
                $totales_child = $this->drawRowsPropios($sheet, $months, $child, $row);
                $this->drawRowTotal($sheet, $totales_child, $row, $months, $row_init_col_total, $total_por_supervisor);
                $cad2['data']       = $child;
                $cad2['totales']    = $totales_child;
                array_push($consolidado_final, $cad2);
            }
            $this->drawRowTotalConsolidadoPorSupervisor($sheet, $total_por_supervisor, $row);
            $total_consolidado_grupo = $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row);

            if(!is_array($gran_consolidado_gerente[$title_sheet]))
                $gran_consolidado_gerente[$title_sheet] = [];


            $cad_gran_consolidado['info_grupo']                 = $supervisor;
            $cad_gran_consolidado['total_consolidado_grupo']    = $total_consolidado_grupo;

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
        $nameFile = "BONOSCXC_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }

    function backgroundCell($row)
    {
        $color = '';
        if(empty($row))
            return  'FFFFFF';

        if((double)$row['saldo'] > 0.1 && (double)$row["abono"] > 0)
            $color  = "FFCC00";
        elseif((double)$row['saldo'] > 0.1 && (double)$row["abono"] <= 0)
            $color  = "FF0000";
        elseif((double)$row['saldo'] <= 0.1)
            $color  = "009900";

        return $color;
    }

    function drawRowTotal(&$sheet, $totales, &$row,$months, $row_init_total, &$total_por_supervisor = [])
    {
        global $global_config_style_cell;
        $style_currency = $global_config_style_cell['style_currency_total_por_responsable'];
        $style_text = array_merge($style_currency, $global_config_style_cell['style_simple_text']);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, 'No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);
        $coordenada_num_empresa = PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado;
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, count($totales['total_contract']))
            ->getStyle($coordenada_num_empresa)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, 'Total cobrado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_devengado, 'Total facturado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengado)->applyFromArray($style_text);
        $col = 3;
        foreach ($totales['totales_mes'] as $ktotal => $total) {

            $coor_total_cobrado =  PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $formula = count($total['coordenada_cobrado'])
                ? '=+'.implode('+', $total['coordenada_cobrado'])
                : '';
            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                ->getStyle($coor_total_cobrado)->applyFromArray($style_currency);

            $coor_total_facturado =  PHPExcel_Cell::stringFromColumnIndex($col) . ($row + 1);
            $formula = count($total['coordenada_facturado'])
                ? '=+'.implode('+', $total['coordenada_facturado'])
                : '';
            $sheet->setCellValueByColumnAndRow($col, $row + 1, $formula)
                ->getStyle($coor_total_facturado)->applyFromArray($style_currency);
            $col++;

            if(!is_array($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['cobrado']))
                $total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['cobrado'] = [];
            if(!is_array($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['facturado']))
                $total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['facturado'] = [];

            array_push($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['cobrado'], $coor_total_cobrado);
            array_push($total_por_supervisor['total_concentrado_vertical_meses'][$ktotal]['facturado'], $coor_total_facturado);
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

            $sheet->setCellValueByColumnAndRow($col, $row, $propio['customer_name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $data['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $col++;
            $sum_col_facturado = 0;
            $sum_col_cobrado = 0;
            foreach ($months as $month) {
                $key = array_search($month, array_column($propio['factura_array'], 'mes'));
                $month_row = $key === false ? [] : $propio['factura_array'][$key];
                $style_general['fill']['color']['rgb'] = $this->backgroundCell($month_row);
                $current_coordinate_month = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, empty($month_row) ?  0 : $month_row['total'])
                    ->getStyle($current_coordinate_month)->applyFromArray($style_general);
                $col++;

                // inicializar coordenadas trabajados
                if(!is_array($return['totales_mes'][$month]['coordenada_cobrado'])) {
                    $return['totales_mes'][$month]['coordenada_cobrado'] = [];
                    $return['totales_mes'][$month]['cantidad_workflow_trabajado'] = 0;
                    $return['totales_mes'][$month]['cantidad_workflow_devengado'] = 0;
                }

                // inicializar array coordenadas devengados
                if(!is_array($return['totales_mes'][$month]['coordenada_facturado']))
                    $return['totales_mes'][$month]['coordenada_facturado'] = [];

                if(($month_row['saldo']) <= 0) {
                    $sum_col_cobrado +=$month_row['total'];
                    $return['totales_mes'][$month]['total_cobrado'] += $month_row['total'];
                    array_push($return['totales_mes'][$month]['coordenada_cobrado'], $current_coordinate_month);
                }

                $sum_col_facturado +=$month_row['total'];

                $return['totales_mes'][$month]['total_facturado'] += $month_row['total'];
                array_push($return['totales_mes'][$month]['coordenada_facturado'], $current_coordinate_month);
            }
            $col_total_dev = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, $sum_col_facturado)
                ->getStyle($col_total_dev)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $col_total_trab = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, $sum_col_cobrado)
                ->getStyle($col_total_trab)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $col_total_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, '='.$col_total_trab."-".$col_total_dev)
                ->getStyle($col_total_diferencia)->applyFromArray($global_config_style_cell['style_currency']);
            $row++;
        }
        return $return;
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
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, 'Gran No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);

        $formula = count($totales['sum_vertical_total_horizontal_num_empresa'])
            ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_num_empresa'])
            : '';
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, $formula)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado)->applyFromArray($style_currency);
        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, 'Gran total cobrado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_devengado, 'Gran total facturado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengado)->applyFromArray($style_text);
        $col = 3;

        foreach ($totales['total_concentrado_vertical_meses'] as $total) {
            $coor_total_trabajado =  PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $formula = count($total['cobrado']) ? '=+'.implode('+', $total['cobrado']) : '';
            $sheet->setCellValueByColumnAndRow($col, $row,  $formula)
                ->getStyle($coor_total_trabajado)->applyFromArray($style_currency);
            $coor_total_devengado =  PHPExcel_Cell::stringFromColumnIndex($col) . ($row + 1);
            $formula = count($total['facturado']) ? '=+'.implode('+', $total['facturado']) : '';
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

    function drawsTotalesFinal(&$book, $sheet, $data, $months, &$row)
    {
        global $global_config_style_cell, $global_bonos;

        $total_consolidado_grupo['row_devengado'] = [];
        $total_consolidado_grupo['row_trabajado'] = [];
        $total_consolidado_grupo['row_gasto'] = [];
        $total_consolidado_grupo['row_porcent_bono'] = [];
        $total_consolidado_grupo['row_bono'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_trabajado'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_devengando'] = [];

        foreach ($data as $total) {
            $row_nombre = $row;
            $sheet->setCellValueByColumnAndRow(2, $row, 'Nombre')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);
            $sheet->setCellValueByColumnAndRow(3, $row, $total['data']['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_devengado = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_devengado, 'Facturado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_trabajado = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_trabajado, 'Cobrado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_gasto = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_gasto, 'Gastos')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_utilidad = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_utilidad, 'Utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_bono = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_bono, 'Bono entregado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_porcentefectividad = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_porcentefectividad, 'Porcentaje de efectividad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_porcentutilidad = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_porcentutilidad, 'Porcentaje de utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_porcentcrecimiento = $row;
            $sheet->setCellValueByColumnAndRow(2, $row_porcentcrecimiento, 'Porcentaje de crecimiento')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);
            $col = 3;
            $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;;
            foreach ($total['totales']['totales_mes'] as $key_month => $total_mes) {

                $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
                $formula = count($total_mes['coordenada_facturado']) ? '=+'.implode('+', $total_mes['coordenada_facturado']) : '';
                $sheet->setCellValueByColumnAndRow($col, $row_devengado, $formula)
                    ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_devengado'][$key_month])) $total_consolidado_grupo['row_devengado'][$key_month]= [];
                array_push($total_consolidado_grupo['row_devengado'][$key_month], $cordinate_devengado);

                $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
                $formula = count($total_mes['coordenada_cobrado']) ? '=+'.implode('+', $total_mes['coordenada_cobrado']) : '';
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

                $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_bono, '')
                    ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);
                if(!is_array($total_consolidado_grupo['row_bono'][$key_month])) $total_consolidado_grupo['row_bono'][$key_month]= [];
                array_push($total_consolidado_grupo['row_bono'][$key_month], $cordinate_bono);

                $formula_efectividad = '=IFERROR((+'.$cordinate_trabajado.'/'.$cordinate_devengado.'),0)';
                $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                    ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_devengado.',0)')
                    ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;
                $cordenada_devengado_anterior = PHPExcel_Cell::stringFromColumnIndex($col - 1).$row_devengado;
                $valor = (int)$key_month === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_devengado_anterior.')-1,0)';
                $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                    ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

                //$total_consolidado_grupo['gran_cantidad_workflow_devengado'][$key_month] ['total'] += $total_mes['cantidad_workflow_devengado'];
                //$total_consolidado_grupo['gran_cantidad_workflow_trabajado'][$key_month] ['total'] += $total_mes['cantidad_workflow_trabajado'];
                $col++;

            }

            $merges = PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months)+2) . $row_nombre;
            $book->getActiveSheet()->mergeCells($merges);
            $row += 2;
        }
        return $total_consolidado_grupo;
    }

    function drawTotalesConsolidadoGrupo(&$book, $sheet, $data, $months, &$row, $info_grupo, $prefix_sheet = '', &$gran_total_gerente = []) {
        global $global_config_style_cell;

        $col =  3;
        $row_nombre = ++$row;
        $row_devengando = ++$row;
        $row_trabajado = ++$row;
        $row_gasto = ++$row;
        $row_utilidad = ++$row;
        $row_bono = ++$row;
        $row_porcentefectividad = ++$row;
        $row_porcentutilidad = ++$row;
        $row_porcentcrecimiento = ++$row;

        $sheet->setCellValueByColumnAndRow(2, $row_nombre, 'Nombre')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_nombre, "GRUPO SUPERVISOR ". strtoupper($info_grupo['name']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_devengando, 'Facturado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengando)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, 'Cobrado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
        $sheet->setCellValueByColumnAndRow(2, $row_gasto, 'Gastos')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_utilidad, 'Utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_bono, 'Bono entregado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentefectividad, 'Porcentaje efectividad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentutilidad, 'Porcentaje utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentcrecimiento, 'Porcentaje crecimiento')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);

        $prefix_sheet = $prefix_sheet==='' ? '' : $prefix_sheet."!";
        $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        foreach($data['row_devengado'] as $key_mes => $total_mes) {

            $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
            $sheet->setCellValueByColumnAndRow($col, $row_devengando, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_devengado'][$key_mes]))
                ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_devengado'][$key_mes])) $gran_total_gerente['row_devengado'][$key_mes]= [];
            array_push($gran_total_gerente['row_devengado'][$key_mes], $cordinate_devengado);

            $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
            $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_trabajado'][$key_mes]))
                ->getStyle($cordinate_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_trabajado'][$key_mes])) $gran_total_gerente['row_trabajado'][$key_mes]= [];
            array_push($gran_total_gerente['row_trabajado'][$key_mes], $cordinate_trabajado);

            $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
            $sheet->setCellValueByColumnAndRow($col, $row_gasto, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_gasto'][$key_mes]))
                ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_gasto'][$key_mes])) $gran_total_gerente['row_gasto'][$key_mes]= [];
            array_push($gran_total_gerente['row_gasto'][$key_mes], $cordinate_gasto);

            $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

            $formula_bono = '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono'][$key_mes]);
            $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_bono, $formula_bono)
                ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_bono'][$key_mes])) $gran_total_gerente['row_bono'][$key_mes]= [];
            array_push($gran_total_gerente['row_bono'][$key_mes], $cordinate_bono);


            $formula_efectividad = '=IFERROR((+'.$cordinate_trabajado.'/'.$cordinate_devengado.'),0)';
            $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_devengado.',0)')
                ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordenada_devengado_anterior = PHPExcel_Cell::stringFromColumnIndex($col - 1).$row_devengando;
            $valor = (int)$key_mes === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_devengado_anterior.')-1,0)';
            $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

            //gran total efectividad
            $gran_total_gerente['cantidad_workflow_trabajado'][$key_mes]['total'] +=$data['gran_cantidad_workflow_trabajado'][$key_mes]['total'];
            $gran_total_gerente['cantidad_workflow_devengado'][$key_mes]['total'] +=$data['gran_cantidad_workflow_devengado'][$key_mes]['total'];
            $col++;
        }
        $merges = PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months) + 2) . $row_nombre;
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
        $sheet->setCellValueByColumnAndRow($col, $row, 'Total facturado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, 'Total cobrado')
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

        $gran_total_consolidado_gerente = [];
        foreach ($gran_consolidado_gerente as $key => $value) {
            $this->drawTotalesConsolidadoGrupo($book, $sheet, $value['total_consolidado_grupo'], $months, $row, $value['info_grupo'], $key, $gran_total_consolidado_gerente);
            $row += 1;
        }
        $row += 1;
        $gran_total_only_gerente = $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row);
        $this->drawGranTotalGerente($book, $sheet, $gran_total_consolidado_gerente, $gran_total_only_gerente, $months, $row, $data);
    }

    function drawGranTotalGerente (&$book, $sheet, $data, $data_gerente, $months, &$row, $info_grupo) {
        global $global_config_style_cell;
        $col =  3;
        $row_nombre = ++$row;
        $row_devengando = ++$row;
        $row_trabajado = ++$row;
        $row_gasto = ++$row;
        $row_utilidad = ++$row;
        $row_bono = ++$row;
        $row_porcentefectividad = ++$row;
        $row_porcentutilidad = ++$row;
        $row_porcentcrecimiento = ++$row;


        $sheet->setCellValueByColumnAndRow(2, $row_nombre, 'Nombre')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(3, $row_nombre, "GRAN TOTAL GERENTE ". strtoupper($info_grupo['name']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_devengando, 'Facturado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_devengando)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, 'Cobrado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
        $sheet->setCellValueByColumnAndRow(2, $row_gasto, 'Gastos')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_utilidad, 'Utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_bono, 'Bono entregado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentefectividad, 'Porcentaje efectividad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentutilidad, 'Porcentaje utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow(2, $row_porcentcrecimiento, 'Porcentaje crecimiento')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);

        $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        foreach($data['row_devengado'] as $key_mes => $total_mes) {

            $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
            $data['row_devengado'][$key_mes] = !is_array($data['row_devengado'][$key_mes]) ? [] : $data['row_devengado'][$key_mes];
            $data_gerente['row_devengado'][$key_mes] = !is_array($data_gerente['row_devengado'][$key_mes]) ? [] : $data_gerente['row_devengado'][$key_mes];
            $celdas_devengado = array_merge_recursive($data['row_devengado'][$key_mes], $data_gerente['row_devengado'][$key_mes]);
            $formula = count($celdas_devengado) ? '=+'.implode('+', $celdas_devengado) : '';
            $sheet->setCellValueByColumnAndRow($col, $row_devengando, $formula)
                ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);

            $data['row_trabajado'][$key_mes] = !is_array($data['row_trabajado'][$key_mes]) ? [] : $data['row_trabajado'][$key_mes];
            $data_gerente['row_trabajado'][$key_mes] = !is_array($data_gerente['row_trabajado'][$key_mes]) ? [] : $data_gerente['row_trabajado'][$key_mes];
            $celdas_trabajado = array_merge_recursive($data['row_trabajado'][$key_mes], $data_gerente['row_trabajado'][$key_mes]);
            $formula = count($celdas_trabajado) ? '=+'.implode('+', $celdas_trabajado) : '';
            $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
            $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $formula)
                ->getStyle($cordinate_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

            $data['row_gasto'][$key_mes] = !is_array($data['row_gasto'][$key_mes]) ? [] : $data['row_gasto'][$key_mes];
            $data_gerente['row_gasto'][$key_mes] = !is_array($data_gerente['row_gasto'][$key_mes]) ? [] : $data_gerente['row_gasto'][$key_mes];
            $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
            $celdas_gasto = array_merge_recursive($data['row_gasto'][$key_mes], $data_gerente['row_gasto'][$key_mes]);
            $formula = count($celdas_gasto) ? '=+'.implode('+', $celdas_gasto) : '';
            $sheet->setCellValueByColumnAndRow($col, $row_gasto, $formula)
                ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

            $celdas_bono = array_merge_recursive($data['row_bono'][$key_mes], $data_gerente['row_bono'][$key_mes]);
            $formula = count($celdas_bono) ? '=+'.implode('+', $celdas_bono) : '';
            $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_bono,$formula)
                ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);


            $cantidad_workflow_devengado = $data['cantidad_workflow_devengado'][$key_mes]['total'] + $data_gerente['gran_cantidad_workflow_devengado'][$key_mes]['total'];
            $cantidad_workflow_trabajado = $data['cantidad_workflow_trabajado'][$key_mes]['total'] + $data_gerente['gran_cantidad_workflow_trabajado'][$key_mes]['total'];

            $formula_efectividad = '=IFERROR((+'.$cordinate_trabajado.'/'.$cordinate_devengado.'),0)';
            $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_devengado.',0)')
                ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordenada_devengado_anterior = PHPExcel_Cell::stringFromColumnIndex($col - 1).$row_devengando;
            $valor = (int)$key_mes === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_devengado_anterior.')-1,0)';
            $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

            $col++;
        }
        $merges = PHPExcel_Cell::stringFromColumnIndex(3) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months) + 2) . $row_nombre;
        $book->getActiveSheet()->mergeCells($merges);
    }

}
