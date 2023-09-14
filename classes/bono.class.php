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
        $name_view      = "instancia_" . $_POST['year'] . "_" . implode('_', $months);
        $custom_fields  = ['contract_id', 'name', 'customer', 'servicio_id', 'name_service', 'departamento_id',
                           'status_service', 'is_primary', 'last_date_workflow', 'fio', 'fif', 'instancias'];

        $add_fields_no_group    = ['tipo_servicio_id', 'instancia_id', 'status', 'class', 'costo', 'fecha', 'comprobante_id'];

        $select_general     ="select c.contractId, c.name, c.customer_name, b.servicioId, b.nombreServicio, b.departamentoId, b.status,
                              b.is_primary, b.lastDateWorkflow, b.fio, b.fif ";
        $select_nogroup     = ", b.tipoServicioId, a.instanciaServicioId as instancia_id, a.status, a.class, a.costoWorkflow as costo, a.date as fecha, a.comprobanteId as comprobante_id ";
        $select_group       = ",concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status,'class',a.class, 
                                 'costo', a.costoWorkflow,'fecha',a.date,'tipo_servicio_id', b.tipoServicioId,'unique_invoice',b.uniqueInvoice,'periodicidad',b.periodicidad,'comprobante_id',a.comprobanteId,'mes', month(a.date))),']') as instancias ";
        $group_by           = " group by a.servicioId ";
        $order_by           = "order by a.date asc ";

        $base_sql = "from instanciaServicio a
               inner join (select servicio.servicioId,servicio.contractId, servicio.tipoServicioId, servicio.status, 
                           tipoServicio.nombreServicio, tipoServicio.periodicidad, tipoServicio.departamentoId,
                           tipoServicio.is_primary, servicio.lastDateWorkflow,servicio.inicioOperaciones as fio,
                           inicioFactura as fif,tipoServicio.uniqueInvoice from servicio 
                           inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId
                           where tipoServicio.status='1' and (servicio.status = 'bajaParcial' OR servicio.status = 'activo')) b on a.servicioId=b.servicioId
               inner join (select contract.contractId, contract.name, customer.nameContact as customer_name
                           from contract inner join customer on contract.customerId = customer.customerId where customer.active = '1'
                           and contract.activo = 'Si') c
                           on b.contractId=c.contractId
                           where a.status != 'baja' AND year(a.date)=" . $_POST['year'] . " and month(a.date) in (" . implode(',', $months) . ")";

        $this->Util()->createOrReplaceView($name_view, $select_general.$select_group.$base_sql.$group_by.$order_by, $custom_fields);
        array_pop($custom_fields);
        $this->Util()->createOrReplaceView('nogroup_'.$name_view, $select_general.$select_nogroup.$base_sql.$order_by, array_merge($custom_fields, $add_fields_no_group));

        // crear vista no agrupada
        $this->setPersonalId($_POST['responsableCuenta']);
        $info = $this->InfoWhitRol();
        $subordinados = $this->getSubordinadosNoDirectoByLevel([4,5]);
        $subordinados_filtrados = [];
        //Forzar que solo se obtenga  servicios del departamento del gerente.
        $filtro['departamento_id'] = $_POST['departamentoId'] > 0 ? $_POST['departamentoId'] : $info['departamentoId'];
        foreach ($subordinados as $sub) {
            $cad = $sub;

            $this->setPersonalId($sub['jefeInmediato']);
            $cad['jefe'] = $this->InfoWhitRol();

            $propios_sub    = $this->getRowsBySheet($sub, $name_view, $filtro);
            $cad['propios'] = $propios_sub;
            $this->setPersonalId($sub['personalId']);
            $childs             = $this->GetCascadeSubordinates();
            $total_sueldo_sub   = array_sum(array_column($childs, 'sueldo'));
            $cad['sueldo']      = $cad['sueldo'];

            $childs_filtrados   = [];
            foreach ($childs as $child) {

                $this->setPersonalId($child['jefeInmediato']);
                $child['jefe'] = $this->InfoWhitRol();

                $cad_child =  $child;
                $propios_child = $this->getRowsBySheet($child, $name_view, $filtro);
                $cad_child['propios'] = $propios_child;
                //if(count($propios_child) > 0)
                    array_push($childs_filtrados, $cad_child);
            }

            $cad['childs'] = $childs_filtrados;
            if(count($propios_sub) > 0 || count($childs_filtrados) > 0)
                array_push($subordinados_filtrados, $cad);
        }

        $data['subordinados'] = $subordinados_filtrados;
        $info['propios'] = $this->getRowsBySheet($info, $name_view, $filtro);
        $data['gerente'] = $info;

        return $data;
    }

    public function getRowsBySheet($encargado, $view, $ftr = [])
    {
        $ftr_departamento   = $_POST['departamentoId'] ? " and a.departamento_id in(" . $_POST['departamentoId'] . ") " : "";

        $queryPermiso       = " (SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('departamento_id', contractPermiso.departamentoId, 'departamento',
                               departamentos.departamento, 'personal_id', contractPermiso.personalId, 'nombre', personal.name)), ']') 
                               FROM contractPermiso
                               INNER JOIN personal ON contractPermiso.personalId = personal.personalId  
                               INNER JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
                               WHERE contractPermiso.contractId = a.contract_id 
                               GROUP BY contractPermiso.contractId) permiso_detallado ";

        $encargados =  [];
        $this->setPersonalId($encargado['personalId']);
        $encargados =  $this->Subordinados();
        $encargados =  !is_array($encargados) ? [] : array_column($encargados, 'personalId');
        $encargados = [];
        array_push($encargados, $encargado['personalId']);
        $stringEncargados =  "0,".implode(',', $encargados);

        $tienePermiso     = ", (SELECT COUNT(*) total FROM contractPermiso sd 
                            WHERE sd.personalId IN(". $stringEncargados .")
                            AND   sd.contractId = a.contract_id) as tienePermiso ";

        $sql = "SELECT a.*, ". $queryPermiso.$tienePermiso." FROM " . $view ." a 
                HAVING tienePermiso > 0 ".$ftr_departamento."
                ORDER BY a.name ASC, a.name_service ASC ";

        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();

        // variables para evitar procesamiento redundante
        $ids_empresa = [];
        $permisos_empresa = [];
        foreach ($res as $key => $row_serv) {
            $valid_instancias = [];
            // si hay departamento_seleccionado aplicar filtro y fusionar nominas y seguridad social
            $ftrDepartamentoId = (int)$ftr['departamento_id'];
            if($ftrDepartamentoId > 0) {
                $pilaDepartamento = in_array($ftrDepartamentoId, [ID_DEP_NOMINAS, ID_DEP_SS]) ? [ID_DEP_NOMINAS, ID_DEP_SS] : [$ftrDepartamentoId];
                $rowServDepId     = (int)$row_serv['departamento_id'];
                if(!in_array($rowServDepId, $pilaDepartamento)) { // (int)$row_serv['departamento_id'] !== (int) $encargado['departamentoId']
                    unset($res[$key]);
                    continue;
                }
            }
            $permisos_normalizado = [];
            // normalizar todos los encargados presentes por cada registro
            $current_permisos = json_decode($row_serv['permiso_detallado'], true);
            $current_permisos =  !is_array($current_permisos) ? [] : $current_permisos;
            if (!in_array($row_serv['contract_id'], $ids_empresa)) {
                foreach ($current_permisos as $current_permiso)
                    $permisos_normalizado[$current_permiso['departamento_id']] = $current_permiso;

                array_push($ids_empresa, $row_serv['contract_id']);
                $permisos_empresa[$row_serv['contract_id']] = $permisos_normalizado;
            } else $permisos_normalizado = $permisos_empresa[$row_serv['contract_id']];

            $instancias = json_decode($row_serv['instancias'], true);
            $valid_instancias = $this->processInstancias($row_serv, $instancias, $view);

            $res[$key]['permisos_normalizado'] = $permisos_normalizado;
            $res[$key]['instancias_array'] = $valid_instancias;
            if (count($res[$key]['instancias_array']) <= 0)
                unset($res[$key]);
        }
        return $res;
    }

    public function generateReport()
    {
        global $global_config_style_cell, $departamentos;
        $data = $this->generateData();
        $supervisores = $data['subordinados'];
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $months = $this->Util()->generateMonthUntil($_POST['period'], false);

        // obtener la pila de departamentos
        //$pila_departamentos = $departamentos->GetListDepartamentos();
        //$jerarquia_2022 =  JERARQUIA_2022 === '*' ? array_column($pila_departamentos, 'departamentoId') : explode(',', JERARQUIA_2022);
        //$jerarquia_2022 = !is_array($jerarquia_2022) ? [] : $jerarquia_2022;
        $title_jerarquia = [];

       /* foreach ($jerarquia_2022 as $item_jerarquia) {
            $key_departamento =  array_search($item_jerarquia, array_column($pila_departamentos, 'departamentoId'));
            if (!$key_departamento)
                 continue;

            $item_departamento =  $pila_departamentos[$key_departamento];
            array_push($title_jerarquia, $item_departamento);
        }*/
        $col_title_0 = ['Cliente','Razon Social'];
        $col_title_1 = [];
        $col_title_2 = ['Encargado de area', 'Servicio'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title_0, $col_title_1, $col_title_2, $col_month_title);

        $gran_consolidado_gerente = [];
        $gran_consolidado_subgerente = [];
        $subgerentes = [];
        $subgerentesId = [];

        foreach ($supervisores as $ksup => $supervisor) {
            $ftrDepId = $_POST['departamentoId'] > 0 ? $_POST['departamentoId'] : $supervisor['departamentoId'];
            if($supervisor['nivel'] == 4) {

                if(!in_array($supervisor['personalId'],$subgerentesId)) {
                    $supervisor['gasto_adicional'] = !$ftrDepId ? $this->gastoAdicional(4) : 0;
                    array_push($subgerentesId, $supervisor['personalId']);
                    $subgerentes[$supervisor['personalId']]['info'] = $supervisor;
                }
                continue;
            }

            $tieneSubgerente = $supervisor['jefe']['nivel'] == 4;
            $jefe = $supervisor['jefe'];
            if($tieneSubgerente && !in_array($jefe['personalId'],$subgerentesId) ) {

                array_push($subgerentesId, $supervisor['personalId']);
                $subgerentes[$supervisor['personalId']]['info'] = $supervisor;
            }

            $supervisor['gasto_adicional'] = !$ftrDepId ? $this->gastoAdicional() : 0;

            $consolidado_final = [];
            $total_por_supervisor = [];
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);

            $name_title =  substr($supervisor["name"], 0, 6);
            $name_title =  $this->Util()->cleanString($name_title);
            $name_title =  str_replace(" ", "", $name_title);
            $title_sheet = "H_".$ksup."_".strtoupper($name_title);
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
            // Si tiene filas propias se muestra lo cual nunca debe pasar.
            $totales = $this->drawRowsPropios($sheet, $months, $supervisor, $row, $title_jerarquia);
            $cad['data'] = $supervisor;
            $cad['totales'] = $totales;
            array_push($consolidado_final, $cad);
            if(count($totales['total_contract']) > 0)
                $this->drawRowTotal($sheet, $totales, $row, $months, $row_init_col_total, $total_por_supervisor, $title_jerarquia);

            // Crear filas de los subordinados del supervisor
            foreach ($supervisor['childs'] as $child) {

                $row_init_col_total = $row;
                $totales_child = $this->drawRowsPropios($sheet, $months, $child, $row, $title_jerarquia);

                if(count($totales_child['total_contract']) > 0)
                    $this->drawRowTotal($sheet, $totales_child, $row, $months, $row_init_col_total, $total_por_supervisor, $title_jerarquia);
                else {
                    $totales_child =  $this->totalesFicticio($months);
                }

                $cad2['data'] = $child;
                $cad2['totales'] = $totales_child;
                array_push($consolidado_final, $cad2);
            }

            $this->drawRowTotalConsolidadoPorSupervisor($sheet, $total_por_supervisor, $row, $title_jerarquia);
            $total_consolidado_grupo = $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row, $title_jerarquia, $supervisor);

            if(!is_array($gran_consolidado_gerente[$title_sheet]))
                $gran_consolidado_gerente[$title_sheet] = [];

            $consolidado_super = $this->drawTotalesConsolidadoGrupo($book, $sheet, $total_consolidado_grupo, $months, $row, $supervisor, $title_jerarquia);

            if($tieneSubgerente) {
                if(!is_array($subgerentes[$jefe['personalId']]['supervisores'][$title_sheet])) {

                    $subgerentes[$jefe['personalId']]['supervisores'][$title_sheet]['info'] = [];
                }

                if(!is_array($subgerentes[$jefe['personalId']]['supervisores'][$title_sheet])) {

                    $subgerentes[$jefe['personalId']]['supervisores'][$title_sheet]['items'] = [];
                }

                $subgerentes[$jefe['personalId']]['supervisores'][$title_sheet]['info'] = $supervisor;
                $subgerentes[$jefe['personalId']]['supervisores'][$title_sheet]['items'] = $consolidado_super;
            }

            $cad_gran_consolidado['info_grupo'] = $supervisor;
            $cad_gran_consolidado['total_consolidado_grupo'] = $consolidado_super;
            $gran_consolidado_gerente[$title_sheet] = $cad_gran_consolidado;
            $hoja++;
        }

        //Crear hojas de subgerentes
        $prefixChild = 'SUPERVISOR';
        if(count($subgerentes)) {
            $gran_consolidado_gerente = [];
            $prefixChild =  'SUBGERENTE';
            $this->drawSubgerentes($book, $hoja, $subgerentes, $months, $gran_consolidado_gerente);
        }

        $this->drawPropiosGerente($book, $hoja, $data['gerente'], $col_title_mix, $months, $gran_consolidado_gerente, $title_jerarquia, $prefixChild);

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

    function drawRowTotal(&$sheet, $totales, &$row,$months, $row_init_total, &$total_por_supervisor = [], $jerarquia)
    {
        global $global_config_style_cell;
        $style_currency = $global_config_style_cell['style_currency_total_por_responsable'];
        $style_text = array_merge($style_currency, $global_config_style_cell['style_simple_text']);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $coordenada_num_empresa = PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado;
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, 'No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, count($totales['total_contract']))
            ->getStyle($coordenada_num_empresa)->applyFromArray($style_text);
        $total_jerarquia_trabajado = (count($jerarquia) + 3);
        for ($current_col = 2; $current_col <= $total_jerarquia_trabajado  ; $current_col++) {
            $sheet->setCellValueByColumnAndRow($current_col, $row_trabajado, '')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($current_col) . $row_trabajado)->applyFromArray($style_text);
        }
        $col = $total_jerarquia_trabajado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, 'Total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($style_text);

        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);

        $total_jerarquia_dev = (count($jerarquia) + 3);
        for ($current_col_dev = 2; $current_col_dev <= $total_jerarquia_dev  ; $current_col_dev++) {
            $sheet->setCellValueByColumnAndRow($current_col_dev, $row_devengado, '')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($current_col_dev) . $row_devengado)->applyFromArray($style_text);
        }
        $col = $total_jerarquia_dev;
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, 'Total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado)->applyFromArray($style_text);
        $col++;
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

    function drawRowsPropios(&$sheet, $months, $data, &$row, $jerarquias)
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
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            foreach ($jerarquias as $jerarquia) {
                $nombre = isset($propio['permisos_normalizado'][$jerarquia['departamentoId']])
                        ? $propio['permisos_normalizado'][$jerarquia['departamentoId']]['nombre']
                        : '';
                $sheet->setCellValueByColumnAndRow(++$col, $row, $nombre)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            }
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['permisos_normalizado'][$propio['departamento_id']]['nombre'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name_service'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($style_text);
            $col++;
            $sum_col_devengado = 0;
            $sum_col_trabajado = 0;
            foreach ($months as $month) {
                $instanciasLineal = array_column($propio['instancias_array'], 'mes');
                $month_complete = $month >= 10 ? $month : "0".$month;
                $date_instancia = $_POST['year']."-".$month_complete."-01";
                $key        = array_search($month, $instanciasLineal);
                $month_row      = $key === false ? [] : $propio['instancias_array'][$key];
                // la periodicidad debe tomarse en cuenta para verificar el mes anterior ???

                $numeroMesResta = $this->Util()->getNumMesPorPeriodicidad($month_row['periodicidad']);
                $mesAnterior =  $numeroMesResta >= 10 ? $month : "0".$numeroMesResta;

                $keyBefore  = array_search($mesAnterior, $instanciasLineal);

                $month_before   = $keyBefore === false ? [] : $propio['instancias_array'][$keyBefore];

                if(($propio['status_service'] === 'bajaParcial' &&
                    $date_instancia > $this->Util()->getFirstDate($propio['last_date_workflow'])) && empty($month_row)) {
                    $month_row['class'] = "Parcial";
                    $month_row['costo'] = '';
                }
                $style_general['fill']['color']['rgb'] = $this->backgroundCell($month_row['class']);
                $current_coordinate_month = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->setCellValueByColumnAndRow($col, $row, $month_row['costo'])
                    ->getStyle($current_coordinate_month)->applyFromArray($style_general);
                $col++;

                $isCompleteMonthBefore =  true;
                /*if(isset($month_before['class'])) {

                    $isCompleteMonthBefore = !($month > 1) ||
                        (in_array($month_before['class'], ['Completo', 'CompletoTardio'])
                            && (int)$month_before['secondary_pending'] === 0);
                }*/

                if (in_array($month_row['class'], ['Completo', 'CompletoTardio'])
                    && $isCompleteMonthBefore
                    && (int)$month_row['secondary_pending'] === 0) {
                    $sum_col_trabajado += $month_row['costo'];
                }

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

    function drawsTotalesFinal(&$book, $sheet, $data, $months, &$row, $jerarquia, $supervisor)
    {
        global $global_config_style_cell, $global_bonos, $personal;

        $total_consolidado_grupo['row_devengado'] = [];
        $total_consolidado_grupo['row_trabajado'] = [];
        $total_consolidado_grupo['row_gasto'] = [];
        $total_consolidado_grupo['row_porcent_bono'] = [];
        $total_consolidado_grupo['row_bono'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_trabajado'] = [];
        $total_consolidado_grupo['gran_cantidad_workflow_devengando'] = [];
        $col_real = count($jerarquia) + 3;
        $row_hide_inicial = $row;

        $personal->setPersonalId($supervisor['personalId']);
        $subsSupervisor =  $personal->SubordinadosDirectos();
        $inmediatosSupLineal =  array_column($subsSupervisor, 'personalId');

        $coorRecalculables = [];
        $coorRecalculablesTrabajado = [];
        $coorRecalculablesGastos = [];

        $personasLocales = [];

        foreach ($data as $da) {
            array_push($personasLocales, $da['data']['personalId']);

            if($da['data']['personalId']== $supervisor['personalId']) {
                foreach ($da['totales']['totales_mes'] as $km => $tmes) {
                    if(count($tmes['coordenada_devengado']) > 0)
                        $total_consolidado_grupo['row_devengado'][$km] = $tmes['coordenada_devengado'];

                    if(count($tmes['coordenada_trabajado']) > 0)
                        $total_consolidado_grupo['row_trabajado'][$km] = $tmes['coordenada_trabajado'];

                }
            }
        }

        foreach ($data as $total) {
            if($total['data']['personalId']== $supervisor['personalId'])
                continue;

            $esInmediatoSup =  in_array($total['data']['personalId'],  $inmediatosSupLineal);
            $inmediatoSupId =  $total['data']['personalId'];
            $jefeInmediatoId =  $total['data']['jefe']['personalId'];

            $row_nombre = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row, 'Nombre')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);
            $sheet->setCellValueByColumnAndRow($col_real + 1, $row, $total['data']['name']."(".$total['data']['nameLevel'].")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_devengado = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_devengado, 'Ingreso devengado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_devengado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_trabajado = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_trabajado, 'Ingreso trabajado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_gasto = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_gasto, 'Gastos')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_utilidad = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_utilidad, 'Utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_porcent_bono = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_porcent_bono, '% Bono')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_porcent_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_bono = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_bono, 'Bono Mensual')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_bono_entregado = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_bono_entregado, 'Bono Anterior')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono_entregado)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_bono_diferencia = $row;
            $sheet->setCellValueByColumnAndRow(3, $row_bono_diferencia, 'Diferencia bono')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $row_bono_diferencia)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_porcentefectividad = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_porcentefectividad, 'Porcentaje de efectividad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);
            $row++;
            $row_porcentutilidad = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_porcentutilidad, 'Porcentaje de utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

            $row++;
            $row_porcentcrecimiento = $row;
            $sheet->setCellValueByColumnAndRow($col_real, $row_porcentcrecimiento, 'Porcentaje de crecimiento')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);
            $col = $col_real + 1;
            $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;;
            foreach ($total['totales']['totales_mes'] as $key_month => $total_mes) {

                $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado;
                $formula = count($total_mes['coordenada_devengado']) ? '=+'.implode('+', $total_mes['coordenada_devengado']) : '';
                $formula = $esInmediatoSup ? '' : $formula;
                $sheet->setCellValueByColumnAndRow($col, $row_devengado, $formula)
                    ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);

                if($esInmediatoSup) {

                    $cadRecal['row'] = $row_devengado;
                    $cadRecal['col'] = $col;

                    $cadRecal['celdas'] = $total_mes['coordenada_devengado'];
                    $coorRecalculables[$inmediatoSupId][$key_month] = $cadRecal;

                    if(!is_array($total_consolidado_grupo['row_devengado'][$key_month]))
                        $total_consolidado_grupo['row_devengado'][$key_month]= [];

                    array_push($total_consolidado_grupo['row_devengado'][$key_month], $cordinate_devengado);
                }
                else {

                    if(!in_array($jefeInmediatoId, $personasLocales)) {

                        if(!is_array($total_consolidado_grupo['row_devengado'][$key_month])) $total_consolidado_grupo['row_devengado'][$key_month]= [];
                        array_push($total_consolidado_grupo['row_devengado'][$key_month], $cordinate_devengado);
                    } else {
                        if($jefeInmediatoId > 0)
                            array_push($coorRecalculables[$jefeInmediatoId][$key_month]['celdas'], $cordinate_devengado);
                    }
                }

                $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
                $formula = count($total_mes['coordenada_trabajado']) ? '=+'.implode('+', $total_mes['coordenada_trabajado']) : '';
                $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $formula)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

                if($esInmediatoSup) {

                    $cadRecal['row'] = $row_trabajado;
                    $cadRecal['col'] = $col;
                    $cadRecal['celdas'] = $total_mes['coordenada_trabajado'];
                    $coorRecalculablesTrabajado[$inmediatoSupId][$key_month] = $cadRecal;

                    if(!is_array($total_consolidado_grupo['row_trabajado'][$key_month]))
                        $total_consolidado_grupo['row_trabajado'][$key_month]= [];

                    array_push($total_consolidado_grupo['row_trabajado'][$key_month], $cordinate_trabajado);
                }
                else {

                    if(!in_array($jefeInmediatoId, $personasLocales)) {

                        if(!is_array($total_consolidado_grupo['row_trabajado'][$key_month])) $total_consolidado_grupo['row_trabajado'][$key_month]= [];
                        array_push($total_consolidado_grupo['row_trabajado'][$key_month], $cordinate_trabajado);
                    } else {
                        if($jefeInmediatoId > 0)
                            array_push($coorRecalculablesTrabajado[$jefeInmediatoId][$key_month]['celdas'], $cordinate_trabajado);
                    }
                }

                $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
                $sheet->setCellValueByColumnAndRow($col, $row_gasto, (double)$total['data']['sueldo'] * (1.40))
                    ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

                if($esInmediatoSup) {

                    $cadRecal['row'] = $row_gasto;
                    $cadRecal['col'] = $col;
                    $cadRecal['sueldo_propio'] = (double)$total['data']['sueldo'] * (1.40);
                    $cadRecal['celdas'] = $total_mes['coordenada_gasto'] ?? [];
                    $coorRecalculablesGastos[$inmediatoSupId][$key_month] = $cadRecal;

                    if(!is_array($total_consolidado_grupo['row_gasto'][$key_month]))
                        $total_consolidado_grupo['row_gasto'][$key_month]= [];

                    array_push($total_consolidado_grupo['row_gasto'][$key_month], $cordinate_gasto);
                }
                else {

                    if(!in_array($jefeInmediatoId, $personasLocales)) {

                        if(!is_array($total_consolidado_grupo['row_gasto'][$key_month]))
                            $total_consolidado_grupo['row_gasto'][$key_month]= [];

                        array_push($total_consolidado_grupo['row_gasto'][$key_month], $cordinate_gasto);
                    } else {
                        if($jefeInmediatoId > 0)
                            array_push($coorRecalculablesGastos[$jefeInmediatoId][$key_month]['celdas'], $cordinate_gasto);
                    }
                }

                $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                    ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

                $cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcent_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_porcent_bono, $global_bonos[$total['data']['nivel']]['porcentaje']/100)
                    ->getStyle($cordinate_porcent_bono)->applyFromArray($global_config_style_cell['style_porcent']);

                if(!is_array($total_consolidado_grupo['row_porcent_bono'][$key_month]))
                    $total_consolidado_grupo['row_porcent_bono'][$key_month]= [];

                array_push($total_consolidado_grupo['row_porcent_bono'][$key_month], $cordinate_porcent_bono);

                $formula_bono = "=IF(($cordinate_utilidad > 0), $cordinate_utilidad*$cordinate_porcent_bono, 0)";
                $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_bono, $formula_bono)
                    ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);

                if(!is_array($total_consolidado_grupo['row_bono'][$key_month]))
                    $total_consolidado_grupo['row_bono'][$key_month]= [];
                array_push($total_consolidado_grupo['row_bono'][$key_month], $cordinate_bono);

                $cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
                $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, '')
                    ->getStyle($cordinate_bono_entregado)->applyFromArray($global_config_style_cell['style_currency']);

                $cordinate_bono_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_diferencia;
                $sheet->setCellValueByColumnAndRow($col, $row_bono_diferencia, "=$cordinate_bono-$cordinate_bono_entregado")
                    ->getStyle($cordinate_bono_diferencia)->applyFromArray($global_config_style_cell['style_currency']);

                //$formula_efectividad = '=IFERROR((+'.$total_mes['cantidad_workflow_trabajado'].'/'.$total_mes['cantidad_workflow_devengado'].'),0)';
                $formula_efectividad = '=+'.$cordinate_trabajado.'/'.$cordinate_devengado;
                $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                    ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_trabajado.',0)')
                    ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

                $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;

                $cordenada_devengado_anterior = PHPExcel_Cell::stringFromColumnIndex($col - 1).$row_devengado;
                $valor = (int)$key_month === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_devengado_anterior.')-1,0)';
                $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                    ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

                $total_consolidado_grupo['gran_cantidad_workflow_devengado'][$key_month] ['total'] += $total_mes['cantidad_workflow_devengado'];
                $total_consolidado_grupo['gran_cantidad_workflow_trabajado'][$key_month] ['total'] += $total_mes['cantidad_workflow_trabajado'];

                $col++;

            }

            $merges = PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months)+ ($col_real)) . $row_nombre;
            $book->getActiveSheet()->mergeCells($merges);
            $row += 2;
            $row_hide_final = $row;
        }

        foreach ($coorRecalculables as $coorRecalculable) {

            foreach ($coorRecalculable as $recal) {
                $formula    = count($recal['celdas']) > 0 ? '=+'.implode('+', $recal['celdas']) : '';
                $current_cordinate = PHPExcel_Cell::stringFromColumnIndex($recal['col']) . $recal['row'];
                $sheet->setCellValueByColumnAndRow($recal['col'],$recal['row'], $formula)
                    ->getStyle($current_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
            }
        }

        foreach ($coorRecalculablesTrabajado as $coorRecalculableT) {

            foreach ($coorRecalculableT as $recalT) {
                $formula = count($recalT['celdas']) > 0 ? '=+' . implode('+', $recalT['celdas']) : '';
                $current_cordinateT = PHPExcel_Cell::stringFromColumnIndex($recalT['col']) . $recalT['row'];
                $sheet->setCellValueByColumnAndRow($recalT['col'], $recalT['row'], $formula)
                    ->getStyle($current_cordinateT)->applyFromArray($global_config_style_cell['style_currency']);
            }
        }

        foreach ($coorRecalculablesGastos as $coorRecalculableG) {

            foreach ($coorRecalculableG as $recalG) {
                $formula = count($recalG['celdas']) > 0 ? '+' . implode('+', $recalG['celdas']) : '';
                $formula1 = '=+' . $recalG['sueldo_propio'].$formula;
                $current_cordinateG = PHPExcel_Cell::stringFromColumnIndex($recalG['col']) . $recalG['row'];
                $sheet->setCellValueByColumnAndRow($recalG['col'], $recalG['row'], $formula1)
                    ->getStyle($current_cordinateG)->applyFromArray($global_config_style_cell['style_currency']);
            }
        }
        /*for($current_row = $row_hide_inicial; $current_row <= $row_hide_final; $current_row ++)
            $sheet->getRowDimension($current_row)->setVisible(false);
*/
        return $total_consolidado_grupo;
    }

    function drawRowTotalConsolidadoPorSupervisor(&$sheet, $totales, &$row, $jerarquia)
    {
        global $global_config_style_cell;
        $row++;
        $style_currency = $global_config_style_cell['style_currency_total_por_responsable'];
        $style_text = array_merge($style_currency, $global_config_style_cell['style_simple_text']);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $formula = count($totales['sum_vertical_total_horizontal_num_empresa'])
            ? '=+'.implode('+', $totales['sum_vertical_total_horizontal_num_empresa'])
            : '';
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, 'Gran No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, $formula)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_trabajado)->applyFromArray($style_currency);

        $total_jerarquia_trabajado = (count($jerarquia) + 3);
        for ($current_col = 2; $current_col <= $total_jerarquia_trabajado  ; $current_col++) {
            $sheet->setCellValueByColumnAndRow($current_col, $row_trabajado, '')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($current_col) . $row_trabajado)->applyFromArray($style_text);
        }
        $col = $total_jerarquia_trabajado;
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, 'Gran total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado)->applyFromArray($style_text);

        $sheet->setCellValueByColumnAndRow(0, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0) . $row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row_devengado)->applyFromArray($style_text);

        $total_jerarquia_dev = (count($jerarquia) + 3);
        for ($current_col_dev = 2; $current_col_dev <= $total_jerarquia_dev  ; $current_col_dev++) {
            $sheet->setCellValueByColumnAndRow($current_col_dev, $row_devengado, '')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($current_col_dev) . $row_devengado)->applyFromArray($style_text);
        }
        $col = $total_jerarquia_dev;
        $sheet->setCellValueByColumnAndRow($col, $row_devengado, 'Gran total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengado)->applyFromArray($style_text);

        $col++;
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

    function drawTotalesConsolidadoGrupo(&$book, $sheet, $data, $months, &$row, $info_grupo, $jerarquia, $prefix_sheet = '', &$gran_total_gerente = [], $acumular = false, $prefix="SUPERVISOR", $sumarSueldoPropio = true) {
        global $global_config_style_cell, $global_bonos;

        $col_real = $prefix_sheet === '' ? count($jerarquia) + 3 : 0;
        $col = $col_real + 1;
        $row_nombre = ++$row;
        $row_devengando = ++$row;
        $row_trabajado = ++$row;
        $row_gasto = ++$row;
        $row_utilidad = ++$row;
        $row_porcent_bono = ++$row;
        $row_bono_mensual = ++$row;
        $row_bono_entregado = ++$row;
        $row_bono_diferencia = ++$row;
        $row_porcentefectividad = ++$row;
        $row_porcentutilidad = ++$row;
        $row_porcentcrecimiento = ++$row;

        $sheet->setCellValueByColumnAndRow($col_real, $row_nombre, 'Nombre')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real + 1, $row_nombre, "GRUPO $prefix ". strtoupper($info_grupo['name']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_devengando, 'Ingreso devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_devengando)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_trabajado, 'Ingreso trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
        $sheet->setCellValueByColumnAndRow($col_real, $row_gasto, 'Gastos')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_utilidad, 'Utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcent_bono, '% Bono')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcent_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_mensual, 'Bono mensual')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_mensual)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_entregado, 'Bono anterior')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_entregado)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_diferencia, 'Diferencia de bono')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_diferencia)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentefectividad, 'Porcentaje efectividad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentutilidad, 'Porcentaje utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentcrecimiento, 'Porcentaje crecimiento')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);

        $prefix_sheet = $prefix_sheet==='' ? '' : $prefix_sheet."!";
        $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        $total_hor_devengado =  [];
        $total_hor_trabajado =  [];
        $total_hor_gasto =  [];
        $total_hor_bono =  [];
        $total_hor_bono_entregado =  [];

        // CONSOLIDADO
        $total_consolidado_grupo['row_devengado'] = [];
        $total_consolidado_grupo['row_trabajado'] = [];
        $total_consolidado_grupo['row_gasto'] = [];
        $total_consolidado_grupo['row_porcent_bono'] = [];
        $total_consolidado_grupo['row_bono'] = [];
        $total_consolidado_grupo['row_bono_entregado'] = [];
        $total_consolidado_grupo['row_bono_diferencia'] = [];

        foreach($data['row_devengado'] as $key_mes => $total_mes) {

            $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
            $sheet->setCellValueByColumnAndRow($col, $row_devengando, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_devengado'][$key_mes]))
                ->getStyle($cordinate_devengado)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_devengado'][$key_mes])) $gran_total_gerente['row_devengado'][$key_mes]= [];
            array_push($gran_total_gerente['row_devengado'][$key_mes], $cordinate_devengado);
            array_push($total_hor_devengado, $cordinate_devengado);

            if(!is_array($total_consolidado_grupo['row_devengado'][$key_mes]))
                $total_consolidado_grupo['row_devengado'][$key_mes]= [];
            $total_consolidado_grupo['row_devengado'][$key_mes][] = $cordinate_devengado;

            $cordinate_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
            $sheet->setCellValueByColumnAndRow($col, $row_trabajado, '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_trabajado'][$key_mes]))
                ->getStyle($cordinate_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_trabajado'][$key_mes])) $gran_total_gerente['row_trabajado'][$key_mes]= [];
            array_push($gran_total_gerente['row_trabajado'][$key_mes], $cordinate_trabajado);
            array_push($total_hor_trabajado, $cordinate_trabajado);

            if(!is_array($total_consolidado_grupo['row_trabajado'][$key_mes]))
                $total_consolidado_grupo['row_trabajado'][$key_mes]= [];
            $total_consolidado_grupo['row_trabajado'][$key_mes][] = $cordinate_trabajado;

            $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
            $gastosMes = $data['row_gasto'][$key_mes] ?? [];
            $gastoAdicional = $info_grupo['gasto_adicional'] * (1.40);
            $sueldoPropio = $info_grupo['sueldo'] * (1.40);

            if($sumarSueldoPropio)
                array_push($gastosMes, $sueldoPropio);

            $formulaGasto = "";
            if(count($gastosMes) > 0)
                $formulaGasto .= "=+".$prefix_sheet.implode('+'.$prefix_sheet, $gastosMes);
            $sheet->setCellValueByColumnAndRow($col, $row_gasto, $formulaGasto)
                ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_gasto'][$key_mes])) $gran_total_gerente['row_gasto'][$key_mes]= [];
            array_push($gran_total_gerente['row_gasto'][$key_mes], $cordinate_gasto);
            array_push($total_hor_gasto, $cordinate_gasto);

            if(!is_array($total_consolidado_grupo['row_gasto'][$key_mes]))
                $total_consolidado_grupo['row_gasto'][$key_mes]= [];
            $total_consolidado_grupo['row_gasto'][$key_mes][] = $cordinate_gasto;

            $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcent_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_porcent_bono, $global_bonos[$info_grupo["nivel"]]['porcentaje']/100) // '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_porcent_bono'][$key_mes])
                ->getStyle($cordinate_porcent_bono)->applyFromArray($global_config_style_cell['style_porcent']);

            if(!is_array($total_consolidado_grupo['row_porcent_bono'][$key_mes]))
                $total_consolidado_grupo['row_porcent_bono'][$key_mes]= [];
            $total_consolidado_grupo['row_porcent_bono'][$key_mes][] = $cordinate_porcent_bono;

            $formula_bono = $acumular
               ? '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono'][$key_mes])
               : "=IF($cordinate_utilidad > 0,$cordinate_utilidad*$cordinate_porcent_bono, 0)";
            $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_mensual;
            $sheet->setCellValueByColumnAndRow($col, $row_bono_mensual, $formula_bono)
                ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_bono'][$key_mes]))
                $gran_total_gerente['row_bono'][$key_mes]= [];
            array_push($gran_total_gerente['row_bono'][$key_mes], $cordinate_bono);
            array_push($total_hor_bono, $cordinate_bono);

            if(!is_array($total_consolidado_grupo['row_bono'][$key_mes]))
                $total_consolidado_grupo['row_bono'][$key_mes]= [];
            $total_consolidado_grupo['row_bono'][$key_mes][] = $cordinate_bono;

            $cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
            $formula_bono_entregado = $acumular
                ? '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono_entregado'][$key_mes])
                : "";
            $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, $formula_bono_entregado)
                ->getStyle($cordinate_bono_entregado)->applyFromArray($global_config_style_cell['style_currency']);

            if(!is_array($gran_total_gerente['row_bono_entregado'][$key_mes]))
                $gran_total_gerente['row_bono_entregado'][$key_mes]= [];
            array_push($gran_total_gerente['row_bono_entregado'][$key_mes], $cordinate_bono_entregado);
            array_push($total_hor_bono_entregado, $cordinate_bono_entregado);

            $cordinate_bono_diferencia= PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_diferencia;
            $formula_bono_diferencia = $acumular
                ? '=+'.$prefix_sheet.implode('+'.$prefix_sheet, $data['row_bono_diferencia'][$key_mes])
                : "=$cordinate_bono-$cordinate_bono_entregado";
            $sheet->setCellValueByColumnAndRow($col, $row_bono_diferencia, $formula_bono_diferencia)
                ->getStyle($cordinate_bono_diferencia)->applyFromArray($global_config_style_cell['style_currency']);

            //$formula_efectividad = '=IFERROR((+'.$data['gran_cantidad_workflow_trabajado'][$key_mes]['total'].'/'.$data['gran_cantidad_workflow_devengado'][$key_mes]['total'].'),0)';
            $formula_efectividad = '=IFERROR(+'.$cordinate_trabajado.'/'.$cordinate_devengado.",0)";
            $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_trabajado.',0)')
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

            if(!is_array($total_consolidado_grupo['row_bono_entregado'][$key_mes]))
                $total_consolidado_grupo['row_bono_entregado'][$key_mes]= [];
            $total_consolidado_grupo['row_bono_entregado'][$key_mes][] = $cordinate_bono_entregado;


            if(!is_array($total_consolidado_grupo['row_bono_diferencia'][$key_mes]))
                $total_consolidado_grupo['row_bono_diferencia'][$key_mes]= [];
            $total_consolidado_grupo['row_bono_diferencia'][$key_mes][] = $cordinate_bono_entregado;
        }

        $coord_horizontal_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        $formula =  is_array($total_hor_devengado) ? "=".implode('+', $total_hor_devengado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_devengando, $formula)
            ->getStyle($coord_horizontal_devengado)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
        $formula =  is_array($total_hor_trabajado) ? "=".implode('+', $total_hor_trabajado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $formula)
            ->getStyle($coord_horizontal_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
        $formula =  is_array($total_hor_gasto) ? "=".implode('+', $total_hor_gasto) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_gasto, $formula)
            ->getStyle($coord_horizontal_gasto)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
        $formula = '='.$coord_horizontal_trabajado .'-'.$coord_horizontal_gasto;
        $sheet->setCellValueByColumnAndRow($col, $row_utilidad, $formula)
            ->getStyle($coord_horizontal_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_mensual;
        $formula = is_array($total_hor_bono) ? "=".implode('+', $total_hor_bono) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_bono_mensual, $formula)
            ->getStyle($coord_horizontal_bono)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
        $formula = is_array($total_hor_bono_entregado) ? "=".implode('+', $total_hor_bono_entregado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, $formula)
            ->getStyle($coord_horizontal_bono)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_diferencia;
        $formula = "=$coord_horizontal_bono-$coord_horizontal_bono_entregado";
        $sheet->setCellValueByColumnAndRow($col, $row_bono_diferencia, $formula)
            ->getStyle($coord_horizontal_bono_diferencia)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_efectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
        $formula = '=IFERROR(('.$coord_horizontal_trabajado .'/'.$coord_horizontal_devengado.'), 0)';
        $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula)
            ->getStyle($coord_horizontal_efectividad)->applyFromArray($global_config_style_cell['style_porcent']);

        $coord_horizontal_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
        $formula = '=IFERROR(('.$coord_horizontal_utilidad .'-'.$coord_horizontal_bono.')/'.$coord_horizontal_trabajado.', 0)';
        $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, $formula)
            ->getStyle($coord_horizontal_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

        $merges = PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months) + 1 + $col_real) . $row_nombre;
        $book->getActiveSheet()->mergeCells($merges);

        return $total_consolidado_grupo;
    }

    function drawPropiosGerente(&$book, $hoja, $data, $col_title_mix, $months, $gran_consolidado_gerente, $jerarquias, $prefixChild= 'SUPERVISOR') {
        global $global_config_style_cell;
        $sheet = $book->createSheet($hoja);
        $name_title =  substr($data["name"], 0, 6);
        $name_title =  $this->Util()->cleanString($name_title);
        $name_title = "GERENTE_0_".str_replace(" ", "", $name_title);
        $title_sheet = strtoupper($name_title);

        $sheet->setTitle($title_sheet);
        $col = 0;
        $row = 1;
        $consolidado_final = [];
       /* $row_hide_init = $row;
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
        $totales_gerente = $this->drawRowsPropios($sheet, $months, $data, $row, $jerarquias);
        $cad['data'] = $data;
        $cad['totales'] = $totales_gerente;
        array_push($consolidado_final, $cad);
        $total_por_gerente = [];
        $this->drawRowTotal($sheet, $totales_gerente, $row, $months, $row_init_col_total, $total_por_gerente, $jerarquias);
       // $this->drawRowTotalConsolidadoPorSupervisor($sheet, $total_por_gerente, $row, $jerarquias);
        $row_hide_end = $row;
        // ocultar filas, se utiliza para no reprogramar las formulas de totales.
        for($current_row = $row_hide_init; $current_row <= $row_hide_end; $current_row++)
            $sheet->getRowDimension($current_row)->setVisible(false);*/


        $gran_total_consolidado_gerente = [];
        foreach ($gran_consolidado_gerente as $key => $value) {
            $this->drawTotalesConsolidadoGrupo($book, $sheet, $value['total_consolidado_grupo'], $months, $row, $value['info_grupo'], $jerarquias, $key, $gran_total_consolidado_gerente, true, $prefixChild, false);
            $row += 1;
        }
        $row += 1;
        $gran_total_only_gerente = [];
        //$gran_total_only_gerente = $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row, $jerarquias);
        $this->drawGranTotalGerente($book, $sheet, $gran_total_consolidado_gerente, $gran_total_only_gerente, $months, $row, $data);
    }

    function drawSubgerentes(&$book, &$hoja, $subgerentes, $months, &$gran_consolidado_gerente) {

        foreach($subgerentes as $keysub => $subgerente) {
            $sheet = $book->createSheet($hoja);
            $name_title =  substr($subgerente['info']["name"], 0, 6);
            $name_title =  $this->Util()->cleanString($name_title);
            $name_title = "SUBGER_".$keysub."_".str_replace(" ", "", $name_title);
            $title_sheet = strtoupper($name_title);
            $sheet->setTitle($title_sheet);
            $row = 1;
            $gran_total_consolidadado_subgerente = [];
            foreach ($subgerente['supervisores'] as $ksup => $value) {
                $this->drawTotalesConsolidadoGrupo($book, $sheet, $value['items'], $months, $row, $value['info'], [], $ksup, $gran_total_consolidadado_subgerente, true, 'SUPERVISOR', false);
                $row += 1;
            }
            $row += 1;
            $gran_total_only_subgerente = [];
            //TODO retornar coordenadas de totales de subgerente.
            $consolidado = $this->drawGranTotalGerente($book, $sheet, $gran_total_consolidadado_subgerente, $gran_total_only_subgerente, $months, $row, $subgerente['info'], 'SUBGERENTE');

            $cad_gran_consolidado['info_grupo'] = $subgerente['info'];
            $cad_gran_consolidado['total_consolidado_grupo'] = $consolidado;
            $gran_consolidado_gerente[$title_sheet] = $cad_gran_consolidado;
        }
    }

    function drawGranTotalGerente (&$book, $sheet, $data, $data_gerente, $months, &$row, $info_grupo, $preefix = "GERENTE") {
        global $global_config_style_cell, $global_bonos;

        $col_real   =  0;
        $col        =  $col_real + 1;
        $row_nombre     = ++$row;
        $row_devengando = ++$row;
        $row_trabajado  = ++$row;
        $row_gasto      = ++$row;
        $row_utilidad   = ++$row;
        $row_porcent_bono = ++$row;
        $row_bono_mensual = ++$row;
        $row_bono_entregado = ++$row;
        $row_bono_diferencia = ++$row;
        $row_porcentefectividad = ++$row;
        $row_porcentutilidad    = ++$row;
        $row_porcentcrecimiento = ++$row;

        $sheet->setCellValueByColumnAndRow($col_real, $row_nombre, 'Nombre')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real + 1, $row_nombre, "GRAN TOTAL $preefix ". strtoupper($info_grupo['name']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row_nombre)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_devengando, 'Ingreso devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_devengando)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_trabajado, 'Ingreso trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_trabajado)->applyFromArray($global_config_style_cell['style_grantotal']);
        $sheet->setCellValueByColumnAndRow($col_real, $row_gasto, 'Gastos')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_gasto)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_utilidad, 'Utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_utilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcent_bono, '% Bono')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcent_bono)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_mensual, 'Bono mensual')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_mensual)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_entregado, 'Bono anterior')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_entregado)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_bono_diferencia, 'Diferencia bono')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_bono_diferencia)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentefectividad, 'Porcentaje efectividad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentefectividad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentutilidad, 'Porcentaje utilidad')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentutilidad)->applyFromArray($global_config_style_cell['style_grantotal']);

        $sheet->setCellValueByColumnAndRow($col_real, $row_porcentcrecimiento, 'Porcentaje crecimiento')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_real) . $row_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_grantotal']);

        $cordenada_base_devengando = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;

        $total_hor_devengado =  [];
        $total_hor_trabajado =  [];
        $total_hor_gasto =  [];
        $total_hor_bono =  [];
        $total_hor_bono_entregado =  [];

        // CONSOLIDADO
        $total_consolidado_grupo['row_devengado'] = [];
        $total_consolidado_grupo['row_trabajado'] = [];
        $total_consolidado_grupo['row_gasto'] = [];
        $total_consolidado_grupo['row_porcent_bono'] = [];
        $total_consolidado_grupo['row_bono'] = [];
        $total_consolidado_grupo['row_bono_entregado'] = [];
        $total_consolidado_grupo['row_bono_diferencia'] = [];

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
            $celdas_gasto = array_merge_recursive($data['row_gasto'][$key_mes], $data_gerente['row_gasto'][$key_mes]);
            $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
            $formula = count($celdas_gasto) ? '=+'.implode('+', $celdas_gasto) : '';
            $sheet->setCellValueByColumnAndRow($col, $row_gasto, $formula)
                ->getStyle($cordinate_gasto)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+' . $cordinate_trabajado . "-" . $cordinate_gasto)
                ->getStyle($cordinate_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcent_bono;
            $sheet->setCellValueByColumnAndRow($col, $row_porcent_bono, $global_bonos[$info_grupo['nivel']]['porcentaje'] > 0 ? $global_bonos[$info_grupo['nivel']]['porcentaje']/100 : 0)
                ->getStyle($cordinate_porcent_bono)->applyFromArray($global_config_style_cell['style_porcent']);

            //$celdas_bono = array_merge_recursive($data['row_bono'][$key_mes], $data_gerente['row_bono'][$key_mes]);
            //$formula = count($celdas_bono) ? '=+'.implode('+', $celdas_bono) : '';
            $formula = "=IF($cordinate_utilidad >0, $cordinate_utilidad*$cordinate_porcent_bono, 0)";
            $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_mensual;
            $sheet->setCellValueByColumnAndRow($col, $row_bono_mensual,$formula)
                ->getStyle($cordinate_bono)->applyFromArray($global_config_style_cell['style_currency']);

            $cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
            $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado,'')
                ->getStyle($cordinate_bono_entregado)->applyFromArray($global_config_style_cell['style_currency']);

            $formula = "=+$cordinate_bono-$cordinate_bono_entregado";
            $cordinate_diferencia_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_diferencia;
            $sheet->setCellValueByColumnAndRow($col, $row_bono_diferencia,$formula)
                ->getStyle($cordinate_diferencia_bono)->applyFromArray($global_config_style_cell['style_currency']);


            $cantidad_workflow_devengado = $data['cantidad_workflow_devengado'][$key_mes]['total'] + $data_gerente['gran_cantidad_workflow_devengado'][$key_mes]['total'];
            $cantidad_workflow_trabajado = $data['cantidad_workflow_trabajado'][$key_mes]['total'] + $data_gerente['gran_cantidad_workflow_trabajado'][$key_mes]['total'];

            $formula_efectividad = '=+'.$cordinate_trabajado.'/'.$cordinate_devengado;
            $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula_efectividad)
                ->getStyle($cordinate_porcentefectividad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=IFERROR((+' . $cordinate_utilidad . "-" . $cordinate_bono . ")/" . $cordinate_trabajado.',0)')
                ->getStyle($cordinate_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

            $cordenada_devengado_anterior = PHPExcel_Cell::stringFromColumnIndex($col - 1).$row_devengando;
            $valor = (int)$key_mes === 1 ? 1 : '=IFERROR((+'.$cordinate_devengado.'/'.$cordenada_devengado_anterior.')-1,0)';
            $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentcrecimiento;
            $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, $valor)
                ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($global_config_style_cell['style_porcent']);

            array_push($total_hor_devengado, $cordinate_devengado);
            array_push($total_hor_trabajado, $cordinate_trabajado);
            array_push($total_hor_gasto, $cordinate_gasto);
            array_push($total_hor_bono, $cordinate_bono);
            array_push($total_hor_bono_entregado, $cordinate_bono_entregado);

            if(!is_array($total_consolidado_grupo['row_devengado'][$key_mes]))
                $total_consolidado_grupo['row_devengado'][$key_mes]= [];
            $total_consolidado_grupo['row_devengado'][$key_mes][] = $cordinate_devengado;

            if(!is_array($total_consolidado_grupo['row_trabajado'][$key_mes]))
                $total_consolidado_grupo['row_trabajado'][$key_mes]= [];
            $total_consolidado_grupo['row_trabajado'][$key_mes][] = $cordinate_trabajado;

            if(!is_array($total_consolidado_grupo['row_gasto'][$key_mes]))
                $total_consolidado_grupo['row_gasto'][$key_mes]= [];
            $total_consolidado_grupo['row_gasto'][$key_mes][] = $cordinate_gasto;

            if(!is_array($total_consolidado_grupo['row_porcent_bono'][$key_mes]))
                $total_consolidado_grupo['row_porcent_bono'][$key_mes]= [];
            $total_consolidado_grupo['row_porcent_bono'][$key_mes][] = $cordinate_porcent_bono;

            if(!is_array($total_consolidado_grupo['row_bono'][$key_mes]))
                $total_consolidado_grupo['row_bono'][$key_mes]= [];
            $total_consolidado_grupo['row_bono'][$key_mes][] = $cordinate_bono;

            if(!is_array($total_consolidado_grupo['row_bono_entregado'][$key_mes]))
                $total_consolidado_grupo['row_bono_entregado'][$key_mes]= [];
            $total_consolidado_grupo['row_bono_entregado'][$key_mes][] = $cordinate_bono_entregado;

            if(!is_array($total_consolidado_grupo['row_bono_diferencia'][$key_mes]))
                $total_consolidado_grupo['row_bono_diferencia'][$key_mes]= [];
            $total_consolidado_grupo['row_bono_diferencia'][$key_mes][] = $cordinate_bono_entregado;

            $col++;
        }

        $coord_horizontal_devengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_devengando;
        $formula =  is_array($total_hor_devengado) ? "=".implode('+', $total_hor_devengado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_devengando, $formula)
            ->getStyle($coord_horizontal_devengado)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_trabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_trabajado;
        $formula =  is_array($total_hor_trabajado) ? "=".implode('+', $total_hor_trabajado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $formula)
            ->getStyle($coord_horizontal_trabajado)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_gasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row_gasto;
        $formula =  is_array($total_hor_gasto) ? "=".implode('+', $total_hor_gasto) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_gasto, $formula)
            ->getStyle($coord_horizontal_gasto)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_utilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_utilidad;
        $formula = '='.$coord_horizontal_trabajado .'-'.$coord_horizontal_gasto;
        $sheet->setCellValueByColumnAndRow($col, $row_utilidad, $formula)
            ->getStyle($coord_horizontal_utilidad)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_mensual;
        $formula = is_array($total_hor_bono) ? "=".implode('+', $total_hor_bono) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_bono_mensual, $formula)
            ->getStyle($coord_horizontal_bono)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_entregado;
        $formula = is_array($total_hor_bono_entregado) ? "=".implode('+', $total_hor_bono_entregado) : '';
        $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, $formula)
            ->getStyle($coord_horizontal_bono)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_bono_diferencia = PHPExcel_Cell::stringFromColumnIndex($col) . $row_bono_diferencia;
        $formula = "=$coord_horizontal_bono-$coord_horizontal_bono_entregado";
        $sheet->setCellValueByColumnAndRow($col, $row_bono_diferencia, $formula)
            ->getStyle($coord_horizontal_bono_diferencia)->applyFromArray($global_config_style_cell['style_currency']);

        $coord_horizontal_efectividad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentefectividad;
        $formula = '=IFERROR(('.$coord_horizontal_trabajado .'/'.$coord_horizontal_devengado.'), 0)';
        $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, $formula)
            ->getStyle($coord_horizontal_efectividad)->applyFromArray($global_config_style_cell['style_porcent']);

        $coord_horizontal_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row_porcentutilidad;
        $formula = '=IFERROR(('.$coord_horizontal_utilidad .'-'.$coord_horizontal_bono.')/'.$coord_horizontal_trabajado.', 0)';
        $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, $formula)
            ->getStyle($coord_horizontal_porcentutilidad)->applyFromArray($global_config_style_cell['style_porcent']);

        $merges = PHPExcel_Cell::stringFromColumnIndex($col_real + 1) . $row_nombre . ":" . PHPExcel_Cell::stringFromColumnIndex(count($months) + $col_real) . $row_nombre;
        $book->getActiveSheet()->mergeCells($merges);

        return $total_consolidado_grupo;
    }

    function processInstancias ($row_serv, $instancias, $view) {
        global $workflow;
        $instancias_filtered = [];
        $firstInicioFactura = $this->Util()->getFirstDate($row_serv['fif']);
        foreach($instancias as $inst) {
            $firstDateWorkflow = $this->Util()->getFirstDate($inst['fecha']);
            $cad = $inst;
            // las instancias deben ser apartir de su fecha de inicio de operaciones en adelante si
            // es menor no debe tomarse encuenta
            if ($this->Util()->getFirstDate($inst['fecha']) < $this->Util()->getFirstDate($row_serv['fio']))
                continue;


            //los rif el dia que se abren deven valer doble
            if(in_array((int)$inst['tipo_servicio_id'], [RIF, RIFAUDITADO]))
                $cad['costo'] = $cad['costo'] * 2;

            if ($row_serv['status_service'] === 'bajaParcial' && $this->Util()->getFirstDate($inst['fecha']) > $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                $cad['class'] = 'Parcial';
                $cad['costo'] = 0;
            }
            // si no es primario es secondario por default.
            $cad['costo'] = !$row_serv['is_primary'] ? 0 : $cad['costo'];

            // setear a 0 costo de tipos de servicio que facturan de unica ocasion, en sus demas workflows.
            $cad['costo'] = ((int)$inst['unique_invoice'] === 1 && $firstInicioFactura != $firstDateWorkflow)
                ? 0
                : $cad['costo'];

            if($row_serv['is_primary']) {
                $month = (int) date('m', strtotime($inst['fecha']));
                $year = (int) date('Y', strtotime($inst['fecha']));
                $cad['secondary_pending'] = 0;//$this->verifySecondary($row_serv['contract_id'], $inst['tipo_servicio_id'], $month, $year, $view);
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

    function gastoAdicional($nivel = 5) {
        $sql  = "SELECT SUM(sueldo)/count(*) FROM personal a ";
        $sql .= "WHERE roleId IN(SELECT rolId FROM roles WHERE departamentoId =".ID_DEP_NOMINAS." AND nivel IN($nivel))";
        $this->Util()->DB()->setQuery($sql);
        $totalSueldoNomina = $this->Util()->DB()->GetSingle();

        $sql  = "SELECT SUM(sueldo)/count(*) FROM personal a ";
        $sql .= "WHERE roleId IN(SELECT rolId FROM roles WHERE departamentoId =".ID_DEP_SS." AND nivel IN($nivel))";
        $this->Util()->DB()->setQuery($sql);
        $totalSueldoSs = $this->Util()->DB()->GetSingle();

        $sql  = "SELECT SUM(sueldo)/count(*) FROM personal a ";
        $sql .= "WHERE roleId IN(SELECT rolId FROM roles WHERE departamentoId =".ID_DEP_FISCAL." AND nivel IN($nivel))";
        $this->Util()->DB()->setQuery($sql);
        $totalSueldoFiscal = $this->Util()->DB()->GetSingle();
        return $totalSueldoFiscal + $totalSueldoNomina + $totalSueldoSs;
    }

    public function totalesFicticio($months) {

        $return['totales_mes'] = [];
        $return['total_contract'] = 0;

        foreach($months as $month) {
            $return['totales_mes'][$month]['coordenada_trabajado'] = [];
            $return['totales_mes'][$month]['cantidad_workflow_trabajado'] = 0;
            $return['totales_mes'][$month]['cantidad_workflow_devengado'] = 0;
            $return['totales_mes'][$month]['coordenada_devengado'] = [];
            $return['totales_mes'][$month]['total_trabajado'] = 0;
            $return['totales_mes'][$month]['total_devengado'] = 0;
        }

        return $return;
    }
}
