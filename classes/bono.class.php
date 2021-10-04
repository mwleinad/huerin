<?php

class Bono extends Personal {

    private $nameReport;
    public function getNameReport(){
        return $this->nameReport;
    }

    public function generateData () {
        $filtro['responsable'] = $_POST['responsableCuenta'];
        $filtro['departamento_id'] =  $_POST['departamentoId'];
        $filtro['periodo'] = $_POST['periodo'];
        $filtro['year'] = $_POST['year'];
        $filtro['deep'] = 1;

       $months = $this->Util()->generateMonthByPeriod($_POST['period'], false);
       $name_view = "instancia_".$_POST['year']."_".implode('_', $months);
       $custom_fiels = ['contract_id','name', 'servicio_id', 'name_service', 'departamento_id','status_service','is_primary', 'instancias'];
       $sql = "select c.contractId, c.name, b.servicioId, b.nombreServicio,b.departamentoId,b.status,b.is_primary,
               concat('[', group_concat(JSON_OBJECT('servicio_id',a.servicioId,'instancia_id',a.instanciaServicioId,'status',a.status, 'class',a.class, 
               'costo', a.costoWorkflow,  'fecha', a.date, 'comprobante_id', a.comprobanteId, 'mes', month(a.date))),  ']') as instancias
               from instanciaServicio a
               inner join (select servicio.servicioId,servicio.contractId, servicio.tipoServicioId, servicio.status, 
                        tipoServicio.nombreServicio, tipoServicio.periodicidad, tipoServicio.departamentoId,
                        tipoServicio.is_primary from servicio 
                        inner join tipoServicio on servicio.tipoServicioId=tipoServicio.tipoServicioId
                        where tipoServicio.status='1') b on a.servicioId=b.servicioId
               inner join contract c on b.contractId=c.contractId
               where year(a.date)=".$_POST['year']." and month(a.date) in (".implode(',', $months).") group by a.servicioId  order by a.date asc";
       $this->Util()->createOrReplaceView($name_view, $sql, $custom_fiels);

       $this->setPersonalId($_POST['responsableCuenta']);
       $subordinados = $this->getSubordinadosByLevel(4);
       foreach($subordinados as $key => $sub) {
            $subordinados[$key]['propios'] = $this->getRowsBySheet($sub['personalId'], $name_view);
            $this->setPersonalId($sub['personalId']);
            $childs = $this->GetCascadeSubordinates();
            foreach($childs as $kc => $child) {
                $childs[$kc]['propios'] = $this->getRowsBySheet($child['personalId'], $name_view);
            }
           $subordinados[$key]['childs'] = $childs;
       }
       return $subordinados;
    }

    public function getRowsBySheet($id, $view) {
        $ftr_departamento = $_POST['departamentoId'] ?  " and a.departamento_id in(".$_POST['departamentoId'].") " : "";

        $sql ="select a.* from ".$view." a 
                  inner join contractPermiso b on a.contract_id=b.contractId
                  where b.personalId in (". $id.") ".$ftr_departamento." order by a.name asc, a.name_service asc ";
        $this->Util()->DB()->setQuery($sql);
        $res = $this->Util()->DB()->GetResult();
        foreach($res as $key => $rowserv) {
            $instancias = json_decode($rowserv['instancias'], true);
            $res[$key]['instancias_array'] = $instancias;
        }
        return $res;
    }

    public function generateReport() {

        $supervisores = $this->generateData();
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $months = $this->Util()->generateMonthByPeriod($_POST['period'], false);
        $col_title = ['Cliente', 'C. Asignado', 'Razon Social', 'Servicio'];
        $col_month_title = $this->Util()->listMonthHeaderForReport($_POST['period']);
        $col_title_mix = array_merge($col_title, $col_month_title);

        foreach ($supervisores as $supervisor) {
            $consolidado_final = [];
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);

            $sheet->setTitle(strtoupper(substr($supervisor["name"], 0, 6)));
            $col = 0;
            $row = 1;
            foreach($col_title_mix as $title_header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $title_header);
                $col++;
            }
            $row++;
            $totales = $this->drawRowsPropios($sheet, $months, $supervisor, $row);
            $cad['data'] = $supervisor;
            $cad['totales']  = $totales;
            array_push($consolidado_final, $cad);
            $this->drawRowTotal($sheet, $totales,$row);
            foreach($supervisor['childs'] as $child) {
                $totales_child = $this->drawRowsPropios($sheet, $months, $child, $row);
                $this->drawRowTotal($sheet, $totales_child,$row);
                $cad2['data'] = $child;
                $cad2['totales']  = $totales_child;
                array_push($consolidado_final, $cad2);
            }

            $this->drawsTotalesFinal($book, $sheet, $consolidado_final, $months, $row);
            $hoja++;
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
        $nameFile= "BONOS_".$_SESSION["User"]["userId"].".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);

    }

    function backgroundCell ($class) {
        $color = '';
        switch($class) {
            case 'Completo':
            case 'CompletoTardio': $color = '009900'; break;
            case 'Iniciado':
            case 'PorCompletar': $color = 'FFCC00'; break;
            case 'PorIniciar': $color = 'FF0000'; break;
            default: $color = 'FFFFFF'; break;
        }
        return $color;
    }

    function drawRowTotal(&$sheet,$totales, &$row) {
        $style_currency = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD,
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '0E76A8')
            ),
            'font' => array('bold' => true),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $style_text= array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
        );
        $style_text = array_merge($style_currency, $style_text);
        $row_trabajado = $row;
        $row_devengado = $row + 1;
        $sheet->setCellValueByColumnAndRow(0, $row_trabajado, '')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_trabajado, 'No. de empresas')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_trabajado, count($totales['total_contract']))
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(3, $row_trabajado, 'Total trabajado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3).$row_trabajado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(0, $row_devengado,'')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(1, $row_devengado,'')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(2, $row_devengado,'')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$row_devengado)->applyFromArray($style_text);
        $sheet->setCellValueByColumnAndRow(3, $row_devengado, 'Total devengado')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex(3).$row_devengado)->applyFromArray($style_text);
        $col = 4;
        foreach($totales['totales_mes'] as $total) {
            $sheet->setCellValueByColumnAndRow($col, $row, $total['total_trabajado'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_currency);
            $sheet->setCellValueByColumnAndRow($col, $row+1, $total['total_devengado'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).($row+1))->applyFromArray($style_currency);
            $col++;
        }
        $row += 2;
    }

    function drawRowsPropios(&$sheet, $months, $data, &$row) {
        $style_general = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD,
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $style_text = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $return['total_contract'] = [];
        $return['totales_mes'] = [];
        foreach($data['propios'] as $propio) {
            $col=0;
            if(!in_array($propio['contract_id'], $return['total_contract']))
                array_push($return['total_contract'], $propio['contract_id']);

            $sheet->setCellValueByColumnAndRow($col, $row, $propio['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $data['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_text);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $propio['name_service'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_text);
            $col++;
            foreach($months as $month) {
                $key = array_search($month, array_column($propio['instancias_array'], 'mes'));
                $month_row = $key === false ? [] : $propio['instancias_array'][$key];
                $style_general['fill']['color']['rgb'] = $this->backgroundCell($month_row['class']);
                $sheet->setCellValueByColumnAndRow($col, $row, $month_row['costo'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($style_general);
                $col++;

                $return['totales_mes'][$month]['total_trabajado'] += in_array($month_row['class'], ['Completo', 'CompletoTardio'])
                    ? $month_row['costo'] : 0;
                $return['totales_mes'][$month]['total_devengado'] += $month_row['costo'];
            }
            $row++;
        }
        return $return;
    }

    function drawsTotalesFinal (&$book, $sheet, $data, $months, $row) {
        $style_grantotal = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '808080')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $style_currency = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD,
            ],
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $style_porcent = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE,
            ],
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        );
        $stack_bono[1] = 5;
        $stack_bono[2] = 4;
        $stack_bono[3] = 3;
        $stack_bono[4] = 3;
        $stack_bono[5] = 2;
        $stack_bono[6] = 1;

        $row += 4;
        foreach($data as $total) {
            $row_nombre = $row;
            $sheet->setCellValueByColumnAndRow(0, $row, 'Nombre')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row)->applyFromArray($style_grantotal);
            $sheet->setCellValueByColumnAndRow(1, $row, $total['data']['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->applyFromArray($style_grantotal);
            $row++;
            $row_devengado = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_devengado, 'Ingreso devengado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_devengado)->applyFromArray($style_grantotal);
            $row++;
            $row_trabajado = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_trabajado, 'Ingreso trabajado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_trabajado)->applyFromArray($style_grantotal);
            $row++;
            $row_gasto = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_gasto, 'Gastos')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_gasto)->applyFromArray($style_grantotal);
            $row++;
            $row_utilidad = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_utilidad, 'Utilidad')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_utilidad)->applyFromArray($style_grantotal);
            $row++;
            $row_porcentbono = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_porcentbono, '% Bono')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_porcentbono)->applyFromArray($style_grantotal);
            $row++;
            $row_bono = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_bono, 'Bono')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_bono)->applyFromArray($style_grantotal);
            $row++;
            $row_bono_entregado = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_bono_entregado, 'Bono entregado')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$row_bono_entregado)->applyFromArray($style_grantotal);
            $row++;
            $row_porcentefectividad = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_porcentefectividad, 'Porcentaje de efectividad');
            $row++;
            $row_porcentutilidad = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_porcentutilidad, 'Porcentaje de utilidad');
            $row++;
            $row_porcentcrecimiento = $row;
            $sheet->setCellValueByColumnAndRow(0, $row_porcentcrecimiento, 'Porcentaje de crecimiento');
            $col = 1;
            foreach($total['totales']['totales_mes'] as $total_mes) {
                $cordinate_devengado = PHPExcel_Cell::stringFromColumnIndex($col).$row_devengado;
                $sheet->setCellValueByColumnAndRow($col, $row_devengado, $total_mes['total_devengado'])
                    ->getStyle($cordinate_devengado)->applyFromArray($style_currency);
                $cordinate_trabajado= PHPExcel_Cell::stringFromColumnIndex($col).$row_trabajado;
                $sheet->setCellValueByColumnAndRow($col, $row_trabajado, $total_mes['total_trabajado'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row_trabajado)->applyFromArray($style_currency);
                $cordinate_gasto = PHPExcel_Cell::stringFromColumnIndex($col).$row_gasto;
                $sheet->setCellValueByColumnAndRow($col, $row_gasto, (double)$total['data']['sueldo'] * 1.4)
                    ->getStyle($cordinate_gasto)->applyFromArray($style_currency);
                $cordinate_utilidad = PHPExcel_Cell::stringFromColumnIndex($col).$row_utilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_utilidad, '=+'.$cordinate_trabajado."-".$cordinate_gasto)
                    ->getStyle($cordinate_utilidad)->applyFromArray($style_currency);
                $cordinate_porcent_bono = PHPExcel_Cell::stringFromColumnIndex($col).$row_porcentbono;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentbono, $stack_bono[$total['data']['nivel']] / 100)
                    ->getStyle($cordinate_porcent_bono)->applyFromArray($style_porcent);
                $cordinate_bono = PHPExcel_Cell::stringFromColumnIndex($col).$row_bono;
                $sheet->setCellValueByColumnAndRow($col, $row_bono, '=+'.$cordinate_utilidad."*".$cordinate_porcent_bono)
                    ->getStyle($cordinate_bono)->applyFromArray($style_currency);
                $cordinate_bono_entregado = PHPExcel_Cell::stringFromColumnIndex($col).$row_bono_entregado;
                $sheet->setCellValueByColumnAndRow($col, $row_bono_entregado, '')
                    ->getStyle($cordinate_bono_entregado)->applyFromArray($style_currency);
                $cordinate_porcentefectividad = PHPExcel_Cell::stringFromColumnIndex($col).$row_porcentefectividad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentefectividad, '=+'.$cordinate_trabajado."/".$cordinate_devengado)
                    ->getStyle($cordinate_porcentefectividad)->applyFromArray($style_porcent);
                $cordinate_porcentutilidad = PHPExcel_Cell::stringFromColumnIndex($col).$row_porcentutilidad;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentutilidad, '=(+'.$cordinate_utilidad."-".$cordinate_bono.")/".$cordinate_devengado)
                    ->getStyle($cordinate_porcentutilidad)->applyFromArray($style_porcent);
                $cordinate_porcentcrecimiento = PHPExcel_Cell::stringFromColumnIndex($col).$row_porcentcrecimiento;
                $sheet->setCellValueByColumnAndRow($col, $row_porcentcrecimiento, '')
                    ->getStyle($cordinate_porcentcrecimiento)->applyFromArray($style_porcent);
                $col++;
            }

            $merges = PHPExcel_Cell::stringFromColumnIndex(1).$row_nombre.":".PHPExcel_Cell::stringFromColumnIndex(count($months)).$row_nombre;
            $book->getActiveSheet()->mergeCells($merges);
            $row +=3;
        }

    }

}
