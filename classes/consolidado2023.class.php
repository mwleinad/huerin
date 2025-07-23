<?php

class Consolidado2023 extends Personal
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
            'status_service', 'is_primary', 'last_date_workflow', 'fio', 'fif','instancias'];
        $add_fields_no_group = ['tipo_servicio_id', 'instancia_id', 'status', 'class', 'costo', 'fecha', 'comprobante_id'];

        $select_general = "select c.contractId, c.name, c.customer_name, b.servicioId, b.nombreServicio, b.departamentoId, b.status,
                            b.is_primary, b.lastDateWorkflow,b.fio, b.fif  ";
        $select_nogroup = ", b.tipoServicioId, a.instanciaServicioId as instancia_id, a.status, a.class, a.costoWorkflow as costo, a.date as fecha, a.comprobanteId as comprobante_id ";
        $select_group = ", concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status, 'class',a.class, 
                      'costo', a.costoWorkflow,  'fecha', a.date, 'tipo_servicio_id', b.tipoServicioId, 'unique_invoice',b.uniqueInvoice,'comprobante_id', a.comprobanteId, 'mes', month(a.date))),  ']') as instancias ";
        $group_by = " group by a.servicioId ";
        $order_by = "order by a.date asc ";

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
                           where year(a.date)=" . $_POST['year'] . " and month(a.date) in (" . implode(',', $months) . ")";
        $this->Util()->createOrReplaceView($name_view, $select_general . $select_group . $base_sql . $group_by . $order_by, $custom_fields);
        array_pop($custom_fields);
        $this->Util()->createOrReplaceView('nogroup_' . $name_view, $select_general . $select_nogroup . $base_sql . $order_by, array_merge($custom_fields, $add_fields_no_group));


        if ($_POST["responsableCuenta"])
            $strFilter .= " and a.personalId = '" . $_POST['responsableCuenta'] . "' ";
        else {
            $areasAdmin = strlen(AREAS_EDO_RESULTADO) > 0  && AREAS_EDO_RESULTADO != "*" ? explode(',', AREAS_EDO_RESULTADO) : [];
            $strFilter .=  count($areasAdmin) > 0 ? " AND c.departamento NOT IN(".implode(',', $areasAdmin).")" : "";
        }

        $sql = "select a.*, b.nivel,c.departamento, b.name as name_rol from personal a
                inner join roles b on a.roleId = b.rolId
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 3 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();

        $cleaned_gerentes = [];
        foreach ($gerentes as $gerente) {
            $filtroDeps = [$gerente['departamentoId']];
            
            $this->setPersonalId($gerente['personalId']);
            $subordinados = $this->getSubordinadosNoDirectoByLevel([4,5]); //

            if(count($subordinados) <= 0)
                continue;

            $conRegistros  = 0;
            $item_gerente  = $gerente;
            $cleaned_subordinados = [];
            $gerente['propios'] = $this->getRowsPropios($gerente['personalId'], $name_view, $filtroDeps);
            if(count($gerente['propios']) > 0)
                $conRegistros +=1;

            array_push($cleaned_subordinados, $gerente);

            foreach ($subordinados as $key => $sub) {

                if ($sub['nivel'] == 4) { // Subgerente
                    $item_subgerente = $sub;
                    // Obtener supervisores bajo este subgerente
                    $this->setPersonalId($sub['personalId']);
                    $supervisores = $this->getSubordinadosNoDirectoByLevel(5);

                    $item_subgerente['propios'] = $this->getRowsPropios($sub['personalId'], $name_view, $filtroDeps);
                    $item_subgerente['tipoPersonal'] = 'Subgerente';
                    $cleaned_subordinados[] = $item_subgerente;

                    if(count($item_subgerente['propios']) > 0)
                            $conRegistros +=1;

            
                    foreach ($supervisores as $supervisor) {
                        $item_supervisor = $supervisor;
                        
                        $this->setPersonalId($supervisor['personalId']);
                        $childs = $this->GetCascadeSubordinates();

                        $childsId = array_map(function($s) { return $s['personalId']; }, $childs);
                        $item_supervisor['sueldo'] += array_sum(is_array($childs) ? array_column($childs, 'sueldo') : []);
                        $item_supervisor['propios'] = $this->getRowsPropios($childsId, $name_view, $filtroDeps);
                        $item_supervisor['tipoPersonal'] = 'Supervisor';
                        if(count($item_supervisor['propios']) > 0)
                            $conRegistros +=1;
                        $cleaned_subordinados[] = $item_supervisor;
                        
                    }
                } elseif ($sub['nivel'] == 5) { // Supervisor directo del gerente
                    $item_supervisor = $sub;

                    $this->setPersonalId($sub['personalId']);
                    $childs = $this->GetCascadeSubordinates();

                    $childsId = array_map(function($s) { return $s['personalId']; }, $childs);
                    $item_supervisor['sueldo'] += array_sum(is_array($childs) ? array_column($childs, 'sueldo') : []);
                    $item_supervisor['propios'] = $this->getRowsPropios($childsId, $name_view, $filtroDeps);
                    $item_supervisor['tipoPersonal'] = 'Supervisor';
                    if(count($item_supervisor['propios']) > 0)
                        $conRegistros +=1;

                    $cleaned_subordinados[] = $item_supervisor;
                }
            
            }
            $item_gerente['subordinados_cascada'] = $cleaned_subordinados;

            if ($conRegistros > 0)
                array_push($cleaned_gerentes, $item_gerente);

        }

        return $cleaned_gerentes;
    }

    public function getRowsPropios($id, $view, $ftrDeps = [])
    {
        $depPresente =  $_POST['departamentoId'] ?? 0;

        if($depPresente > 0) {
            $ftrDeps = [$depPresente];
            if($depPresente == 1)
                array_push($ftrDeps, 31);
        }
        if(count($ftrDeps) > 0)
            $ftr_departamento = " and a.departamento_id in(" . implode(',', $ftrDeps) . ")";


        $id =  is_array($id) ?  implode(',', $id) : $id;

        $tienePermiso     = ", (SELECT COUNT(*) total FROM contractPermiso sd 
                            INNER JOIN departamentos dep ON sd.departamentoId = dep.departamentoId
                            WHERE sd.personalId IN(". $id .")
                            AND dep.esGerencial = 0
                            AND   sd.contractId = a.contract_id) as tienePermiso ";

        $queryPermiso       = ",(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('departamento_id', contractPermiso.departamentoId, 'departamento',
                               departamentos.departamento, 'personal_id', contractPermiso.personalId, 'nombre', personal.name)), ']') 
                               FROM contractPermiso
                               INNER JOIN personal ON contractPermiso.personalId = personal.personalId  
                               INNER JOIN departamentos ON contractPermiso.departamentoId = departamentos.departamentoId
                               WHERE contractPermiso.contractId = a.contract_id 
                               GROUP BY contractPermiso.contractId) permiso_detallado ";

        $sql = "select a.* ".$queryPermiso.$tienePermiso." from " . $view . " a 
                HAVING tienePermiso > 0 " . $ftr_departamento . " 
                order by a.name asc, a.name_service asc ";
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
        $firstInicioFactura = $this->Util()->getFirstDate($row_serv['fif']);
        foreach ($instancias as $inst) {
            $firstDateWorkflow = $this->Util()->getFirstDate($inst['fecha']);
            $cad = $inst;
            // las instancias deben ser apartir de su fecha de inicio de operaciones en adelante si
            // es menor no debe tomarse encuenta

            if ($firstDateWorkflow < $this->Util()->getFirstDate($row_serv['fio']))
                continue;

            //los rif el dia que se abren deven valer doble
            if (in_array((int)$inst['tipo_servicio_id'], [RIF, RIFAUDITADO]))
                $cad['costo'] = $cad['costo'] * 2;

            if ($row_serv['status_service'] === 'bajaParcial' && $this->Util()->getFirstDate($inst['fecha']) > $this->Util()->getFirstDate($row_serv['last_date_workflow'])) {
                $cad['class'] = 'Parcial';
                $cad['costo'] = 0;
            }
            // si no es primario es secundario por default.
            $cad['costo'] = (int)$row_serv['is_primary'] ==0 ? 0 : $cad['costo'];
            // setear a 0 costo de tipos de servicio que facturan de unica ocasion, en sus demas workflows.
            $cad['costo'] = ((int)$inst['unique_invoice'] == 1 && $firstInicioFactura != $firstDateWorkflow)
                ? 0
                : $cad['costo'];

            if ($row_serv['is_primary']) {
                $month = (int)date('m', strtotime($inst['fecha']));
                $year = (int)date('Y', strtotime($inst['fecha']));
                $cad['secondary_pending'] = $this->verifySecondary($row_serv['contract_id'], $inst['tipo_servicio_id'], $month, $year, $view);
            }
            $cad2['finstancia'] = $cad['fecha'];
            $cad2['tipoServicioId'] = $cad['tipo_servicio_id'];
            $pasos = $workflow->validateStepTaskByWorkflow($cad2);
            if (!count($pasos))
                $cad['costo'] = 0;

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
                        $propios_meses[$month]['trabajado'] += (in_array($month_row['class'], ['Completo', 'CompletoTardio']) && (int)$month_row['secondary_pending'] === 0 && $propio['is_primary'] == 1)
                            ? $month_row['costo']
                            : 0;
                        $propios_meses[$month]['devengado'] += $propio['is_primary'] == 1 ? $month_row['costo'] : 0;
                        $saldo = $month_row['comprobante_id'] ? $comprobante->GetInfoComprobante($month_row['comprobante_id'])['saldo'] : 0;
                        $propios_meses[$month]['cobrado'] += ($saldo <= 0 && $month_row['comprobante_id'] && $propio['is_primary'] == 1)
                            ? $month_row['costo'] : 0;
                    }
                }
                $cad['meses_totales'] = $propios_meses;
                unset($cad['propios']);
                array_push($totales_sub, $cad);
            }
            $cad_gerente['subordinados_filtered'] = $totales_sub;

            /*$totalesNomina = [];
            foreach ($gerente['subordinados_nomina'] as $data2) {
                $cad2 = $data2;
                $propios_meses2 = [];
                foreach ($data2['propios'] as $propio2) {
                    foreach ($months as $month) {
                        $key = array_search($month, array_column($propio2['instancias_array'], 'mes'));
                        $month_row = $key === false ? [] : $propio2['instancias_array'][$key];
                        $month_complete = $month >= 10 ? $month : "0" . $month;
                        if (($propio2['status_service'] === 'bajaParcial' &&
                                $_POST['year'] . "-" . $month_complete . "-01" > $this->Util()->getFirstDate($propio2['last_date_workflow'])) && empty($month_row)) {
                            $month_row['class'] = "Parcial";
                            $month_row['costo'] = '';
                        }
                        $propios_meses2[$month]['trabajado'] += in_array($month_row['class'], ['Completo', 'CompletoTardio']) && (int)$month_row['secondary_pending'] === 0
                            ? $month_row['costo']
                            : 0;
                        $propios_meses2[$month]['devengado'] += $month_row['costo'];
                        $saldo = $month_row['comprobante_id'] ? $comprobante->GetInfoComprobante($month_row['comprobante_id'])['saldo'] : 0;
                        $propios_meses2[$month]['cobrado'] += $saldo <= 0 && $month_row['comprobante_id']
                            ? $month_row['costo'] : 0;
                    }
                }
                $cad2['meses_totales'] = $propios_meses2;
                unset($cad2['propios']);
                array_push($totalesNomina, $cad2);
            }
            $cad_gerente['subordinados_nomina'] = $totalesNomina;*/

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

        // obtener gastos administrativos.

        $totalConsolidado =  [];
        foreach ($gerentes_filtered as $hj => $gerente) {
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $title_sheet = ($hj + 1)."_". str_replace(' ','', trim(strtoupper(substr($gerente["name"], 0, 6))));
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
           // $data_total['trabajado'] = $this->drawSection($sheet, $row, $months, 'TRABAJADOS', $gerente['subordinados_filtered'], 'trabajado');
            //$data_total['cobrado'] = $this->drawSection($sheet, $row, $months, 'COBRADOS', $gerente['subordinados_filtered'], 'cobrado');
            $data_total['nomina'] = $this->drawSectionNomina($sheet, $row, $months, $gerente['subordinados_filtered']);
            $data_total['nomina_adicional'] = $this->drawSectionNomina($sheet, $row, $months, $gerente['subordinados_filtered'], PORCENTAJE_AUMENTO);
            $data_total['utilidad'] = $this->drawSectionUtilidad($sheet, $row, $months, $gerente['subordinados_filtered']);
            $data_total['nomina_admin'] = $this->drawNominaAdmnistrativa($sheet, $row, $months, $gerente['subordinados_filtered']);
            $data_total['nomina_admin_adicional'] = $this->drawNominaAdmnistrativa($sheet, $row, $months, $gerente['subordinados_filtered'],PORCENTAJE_AUMENTO);
            $data_total['gasto_administrativo'] = $this->drawGastoAdministrativo($sheet, $row, $months, $gerente['subordinados_filtered']);
            $data_total['utilidad_neta'] = $this->drawSectionUtilidadNeta($sheet, $row, $months,
            $gerente['subordinados_filtered'], $data_total['utilidad'], $data_total['nomina_admin_adicional'], $data_total['gasto_administrativo']);

            $gerente['totales'] = $data_total;
            $gerente['sheet'] = $title_sheet;
            array_push($totalConsolidado, $gerente);
            //$this->drawGranTotal($sheet, $row, $months, $data_total);
            $hoja++;
        }
        $sheetLast = $book->createSheet($hoja);
        $sheetLast->setTitle('CONSOLIDADO');
        $col = 0;
        $row = 1;
        $col_title_mix = array_merge(['Area', 'Nombre'], $col_month_title);
        foreach ($col_title_mix as $title_header) {
            $sheetLast->setCellValueByColumnAndRow($col, $row, $title_header)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            if ($col > 1) {
                $col++;
                $sheetLast->setCellValueByColumnAndRow($col, $row, '%')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
            }
            $col++;
        }
        $sheetLast->setCellValueByColumnAndRow($col, $row, 'TOTAL')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $col++;
        $sheetLast->setCellValueByColumnAndRow($col, $row, '%')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'DEVENGADO', $totalConsolidado, 'devengado');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'NOMINA OPERATIVA', $totalConsolidado, 'nomina');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'NOMINA OPERATIVA (ADICIONAL '.PORCENTAJE_AUMENTO.'%)', $totalConsolidado, 'nomina_adicional');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'UTILIDAD BRUTA', $totalConsolidado, 'utilidad');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'NOMINA ADMINISTRATIVA', $totalConsolidado, 'nomina_admin');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'NOMINA ADMINISTRATIVA (ADICIONAL '.PORCENTAJE_AUMENTO.'%)', $totalConsolidado, 'nomina_admin_adicional');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'GASTOS ADMINISTRATIVOS', $totalConsolidado, 'gasto_administrativo');
        $this->drawSectionConsolidado($sheetLast, $row, $months, 'UTILIDAD NETA', $totalConsolidado, 'utilidad_neta');
        $hoja++;

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        /*foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }*/
        $nameFile = "EDO_RES_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }
    function drawSectionConsolidado(&$sheet, &$row, $months, $title_section, $data, $key) {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, $title_section)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        foreach($data as $data) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $data['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $data['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sumHor = [];
            for($num=1; $num<=count($months); $num++) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                array_push($sumHor, $current_col_cordinate);
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, "=".$data['sheet']."!".$data['totales'][$key]['totales'][$num])
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(".implode(',', $sumHor).")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $row++;
        }
        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL ' . $title_section))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num =1; $num<= count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
            PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
        $row +=3;
    }
    function drawSection(&$sheet, &$row, $months, $title_section, $data, $key)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, $title_section)
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);
        $coordenadas = [];
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

        for($num =1; $num<= count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }

        $coordenadas['row_final'] = $row;
        $row += 3;

        return $coordenadas;
    }

    function drawSectionNomina(&$sheet, &$row, $months, $data, $adicional = 0)
    {
        $titulo =  $adicional > 0 ?  " (ADICIONAL ".$adicional." %)" : '';

        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'NOMINA OPERATIVA'.strtoupper($titulo))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = $row + count($data);

        $coordenadas = [];
        foreach ($data as $item) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $numeroColumnas = count($item['meses_totales']) > 0 ? count($item['meses_totales']) : count($months);
            for($num =1; $num<= $numeroColumnas; $num++) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $coordenadas['responsables'][$item['personalId']][$num] = $current_col_cordinate;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col, $row, (double)$item['sueldo'] * (1 + ((double)$adicional/100)))
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL NOMINA OPERATIVA'.strtoupper($titulo)))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num =1; $num<= count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $coordenadas['row_final'] = $row;
        $row += 3;
        return $coordenadas;
    }

    function drawSectionUtilidad(&$sheet, &$row, $months, $data)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'UTILIDAD BRUTA')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        $row_end_section = count($data) >  1 ? ($row + count($data) - 1) : ($row + count($data));

        $coordenadas = [];
        foreach ($data as $key => $item) {
            if($key === 0 && count($data) > 1)
                continue;

            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            foreach ($item['meses_totales'] as $key2 => $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $coordenadas['responsables'][$item['personalId']][$key2] = $current_col_cordinate;


                $nominaGerente =  intval($data[0]['sueldo']) > 0 ? ($data[0]['sueldo'] * (1 + (PORCENTAJE_AUMENTO/100))): 0;
                $cantidad =  count($data) > 1 ? (count($data) - 1 ) : count($data);
                $totalGerente = count($data) > 1 ? ($nominaGerente/$cantidad) : 0;
                $utilidad =  (double)$mes_total['devengado'] - ((double)$item['sueldo'] * (1 + (PORCENTAJE_AUMENTO/100))) - $totalGerente;
                $sheet->setCellValueByColumnAndRow($col, $row, $utilidad)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL UTILIDAD BRUTA'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num=1; $num<=count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $coordenadas['row_final'] = $row;
        $row += 3;

        return $coordenadas;
    }

    function drawSectionUtilidadNeta(&$sheet, &$row, $months, $data, $coorUtil, $coorNomAdminAdic, $coorGastoAdmin)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'UTILIDAD NETA')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        //$row_end_section = $row + count($data) -1;
        $row_end_section = count($data) >  1 ? ($row + count($data) - 1) : ($row + count($data));
        $coordenadas = [];
        foreach ($data as $key => $item) {
            if($key === 0 && count($data) > 1)
                continue;

            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;

            foreach ($item['meses_totales'] as $key2 => $mes_total) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;

                $coordenadaNominaAdmAdic =  $coorNomAdminAdic['responsables'][$item['personalId']][$key2];
                $coordenadaGastoAdmin=  $coorGastoAdmin['responsables'][$item['personalId']][$key2];
                $utilidadNeta = "=".$coorUtil['responsables'][$item['personalId']][$key2]."-SUM(".$coordenadaNominaAdmAdic.", ".$coordenadaGastoAdmin.")";
                $sheet->setCellValueByColumnAndRow($col, $row, $utilidadNeta)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL UTILIDAD NETA'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num=1; $num<=count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $coordenadas['row_final'] = $row;
        $row += 3;

        return $coordenadas;
    }

    function drawNominaAdmnistrativa(&$sheet, &$row, $months, $data, $adicional=0)
    {
        $titulo =  $adicional > 0 ?  " (ADICIONAL ".$adicional." %)" : '';

        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'NOMINA ADMINISTRATIVA'.strtoupper($titulo))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        //$row_end_section = $row + (count($data) -1);
        $row_end_section = count($data) >  1 ? ($row + count($data) - 1) : ($row + count($data));
        $filtroAreas = strlen(AREAS_EDO_RESULTADO) > 0  && AREAS_EDO_RESULTADO != "*" ? explode(',', AREAS_EDO_RESULTADO) : [];
        $matrizNomina =  $this->matrizNominaAdministrativa($filtroAreas);
        $ponderacionNomina= is_array($matrizNomina) ?  array_column($matrizNomina, 'ponderacion'):  [];
        $totalPonderacion = array_sum($ponderacionNomina);
        $totalPonderacion = intval($adicional) > 0 ? ($totalPonderacion * (1 + ($adicional/100))) : $totalPonderacion;

        $coordenadas = [];

        foreach ($data as $key => $item) {
            if($key == 0 && count($data) > 1)
                continue;
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $numeroColumnas = count($item['meses_totales']) > 0 ? count($item['meses_totales']) : count($months);
            for($num=1; $num <= $numeroColumnas; $num++) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $coordenadas['responsables'][$item['personalId']][$num] = $current_col_cordinate;
                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col,$row, $totalPonderacion)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL NOMINA ADMINISTRATIVA'.strtoupper($titulo)))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num=1; $num<=count($months); $num++)  {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section -1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $coordenadas['row_final'] = $row;
        $row += 3;
        return $coordenadas;
    }

    function drawGastoAdministrativo(&$sheet, &$row, $months, $data)
    {
        global $global_config_style_cell;
        $sheet->setCellValueByColumnAndRow(1, $row, 'GASTOS ADMINISTRATIVOS')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);
        $row++;

        $row_initial_section = $row;
        //$row_end_section = $row + count($data) -1;
        $row_end_section = count($data) >  1 ? ($row + count($data) - 1) : ($row + count($data));
        $totalGastoAdministrativo =  $_POST['gasto_administrativo'] ?? GASTO_ADMINISTRATIVO;
        $numeroGrupo =  $this->numeroGrupoOperativo();
        $montoGastoAdmministrativo =  intval($totalGastoAdministrativo) > 0  && $numeroGrupo > 0 ? ($totalGastoAdministrativo/$numeroGrupo) : 0;

        $coordenadadas = [];
        foreach ($data as $key => $item) {
            if($key === 0 && count($data) > 1)
                continue;
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['tipoPersonal'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $item['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $col++;
            for($num=1; $num<=count($item['meses_totales']); $num++) {
                $current_col_cordinate = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $coordenadas['responsables'][$item['personalId']][$num] = $current_col_cordinate;

                $current_col_total_section = PHPExcel_Cell::stringFromColumnIndex($col) . $row_end_section;
                $sheet->setCellValueByColumnAndRow($col,$row, $montoGastoAdmministrativo)
                    ->getStyle($current_col_cordinate)->applyFromArray($global_config_style_cell['style_currency']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(" . $current_col_cordinate . "/" . $current_col_total_section . ",0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                $col++;
            }
            $row++;
        }

        $col = 2;
        $sheet->setCellValueByColumnAndRow(1, $row, strtoupper('TOTAL GASTOS ADMINISTRATIVOS'))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->applyFromArray($global_config_style_cell['style_header']);

        for($num=1; $num<=count($months); $num++) {
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
            $coordenadas['totales'][$num] = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, "=SUM(" . PHPExcel_Cell::stringFromColumnIndex($col) . $row_initial_section . ":" .
                PHPExcel_Cell::stringFromColumnIndex($col) . ($row_end_section - 1) . ")")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
            $col++;
        }
        $coordenadadas['row_final'] = $row;
        $row += 3;
        return $coordenadas;
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

    function matrizNominaAdministrativa($only = [], $except = []) {
      $ftr = "";
      $ftr .=  count($only) > 0 ? " AND b.departamento IN(".implode(',', $only).")" : "";
      $ftr .=  count($except) > 0 ? " AND b.departamento NOT IN(".implode(',', $except).")" : "";

      $sql = "SELECT b.departamento nombre,SUM(sueldo) total 
              FROM personal a
              INNER JOIN departamentos  b ON a.departamentoId=b.departamentoId
              WHERE b.estatus = 1 ".$ftr."
              GROUP BY a.departamentoId ";
      $this->Util()->DB()->setQuery($sql);
      $results = $this->Util()->DB()->GetResult();
      $numeroGrupo =  $this->numeroGrupoOperativo();
      foreach($results as $key => $value) {
          $results[$key]['ponderacion'] = intval($value['total']) > 0 && $numeroGrupo > 0 ? ($value['total']/$numeroGrupo) : 0;
      }
      return $results;
    }

    function numeroGrupoAdministrativo() {
        $areasAdmin = strlen(AREAS_EDO_RESULTADO) > 0  && AREAS_EDO_RESULTADO != "*" ? explode(',', AREAS_EDO_RESULTADO) : [];
        $ftr = "";
        $ftr .=  count($areasAdmin) > 0 ? " AND b.departamento IN(".implode(',', $areasAdmin).")" : "";

        $sql = "SELECT count(*) total FROM personal a
                INNER JOIN departamentos b ON a.departamentoId = b.departamentoId
                INNER JOIN roles c ON a.roleId = c.rolId
                WHERE (a.name not like '%Curso Plataforma%' && a.name not like '%Capacitación%') ".$ftr;
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetSingle();

    }

    function numeroGrupoOperativo() {

        $areasAdmin = strlen(AREAS_EDO_RESULTADO) > 0  && AREAS_EDO_RESULTADO != "*" ? explode(',', AREAS_EDO_RESULTADO) : [];
        $filtro =  count($areasAdmin) > 0 ? " AND b.departamento NOT IN(".implode(',', $areasAdmin).")" : "";
        $sql = "SELECT count(*) total FROM personal a
                INNER JOIN departamentos b ON a.departamentoId = b.departamentoId
                INNER JOIN roles c ON a.roleId = c.rolId
                WHERE c.nivel=4 AND (a.name not like '%Curso Plataforma%' && a.name not like '%Capacitación%') ".$filtro;
        $this->Util()->DB()->setQuery($sql);
        return $this->Util()->DB()->GetSingle();

    }
}
