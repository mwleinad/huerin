<?php

class EdoResultado extends ReporteBonos
{
    private $nameReport;
    public function getNameReport(){
        return $this->nameReport;
    }
    function generateEdoResult($ftr){
        $gerentes = $this->generateArrayEdoResult($ftr);
        $book = new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        // styles
        $styles = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ]
        );
        $stylesPorcent = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '9FE191')
            ),
        );
        $stylesTotalPorcent = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
            ],
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '23A508')
            ),
        );
        $stylesTotal = array(
            'font' => [
                'bold' => true,
            ],
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ]
        );
        $stylesBold = array(
            'font' => [
                'bold' => true,
            ],
        );
        //
        if (!$ftr['responsableCuenta']) {

            $sheet = $book->createSheet($hoja);
            $row = 1;
            $col = 0;
            $sheet->setTitle('consolidado');
            $sheet->setCellValueByColumnAndRow($col, $row, "Area")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Encargado")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $col++;
            foreach($gerentes['headers'] as $head) {
                $sheet->setCellValueByColumnAndRow($col, $row, ucfirst(strtolower($head['name'])))
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow(++$col, $row, '%')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
                $col++;
            }
            $sheet->setCellValueByColumnAndRow($col, $row, "Acumulados")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $row++;

            $items = $gerentes['items'];
            $totales = [];
            foreach($items as $key => $gerente) {
                foreach ($gerente['totales'] as $ky => $total) {
                    $totales[$ky][$gerente['personalId']] = $total[0];
                }
            }
            $granTotal = [];
            $devAcumulados = [];
            foreach($totales as $key => $total) {
                switch($key) {
                    case 'nominas':
                        $prefix = 'Gastos nominas';
                    break;
                    case 'utilidades':
                        $prefix = 'Utilidades';
                    break;
                    default:
                        $prefix = 'Ingresos '.$key;
                    break;
                }
                $sheet->setCellValueByColumnAndRow(1, $row, strtoupper($prefix))
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
                $row++;
                $initRow = $row;
                $endRowTmp = $initRow + count($total);
                foreach($total as $kt => $var) {
                    $col= 0;
                    $sheet->setCellValueByColumnAndRow($col, $row, $var['nameRol']);
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col, $row, $var['name']);
                    $col++;
                    $sumColumns = [];
                    foreach ($var['meses'] as $keyMes => $mes) {
                        $sumCom = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                        array_push($sumColumns, $sumCom);
                        if ($key === 'devengados') {
                            $devengadosCols[$kt]['meses'][$keyMes] = $sumCom;
                            $colAmountDevMonth = PHPExcel_Cell::stringFromColumnIndex($col).$endRowTmp;

                        } else {
                            $colAmountDevMonth = $devengadosCols[$kt]['meses'][$keyMes];
                        }

                        $sheet->setCellValueByColumnAndRow($col, $row, isset($mes['total']) ? $mes['total'] : 0)
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                        $porcent = $sumCom."/".$colAmountDevMonth;
                        //porcentaje por mes
                        $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror($porcent, 0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                        $col++;
                    }

                    $strSumColumns = implode('+', $sumColumns);
                    $sheet->setCellValueByColumnAndRow($col, $row, "=$strSumColumns")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                    $currentDevAcum = PHPExcel_Cell::stringFromColumnIndex($col).$row;

                    if($key === 'devengados') {
                        $devAcumulados[$kt] = $currentDevAcum;
                        $amountAcum = PHPExcel_Cell::stringFromColumnIndex($col).$endRowTmp;
                    } else {
                        $amountAcum = $devAcumulados[$kt];
                    }
                    $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror($currentDevAcum/$amountAcum, 0)")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                    $row +=1;

                }
                $endRow = $row - 1;

                $sheet->setCellValueByColumnAndRow(1, $row, 'Total ' . $prefix)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
                $colTotal = 2;
                //fila suma por seccion
                foreach($gerentes['headers'] as $kh => $hd) {
                   $initSuma = PHPExcel_Cell::stringFromColumnIndex($colTotal).$initRow;
                   $endSuma = PHPExcel_Cell::stringFromColumnIndex($colTotal).$endRow;
                   $initSumaPorcent = PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$initRow;
                   $endSumaPorcent = PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$endRow;
                   $totalSeccion = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                   // save to array
                   $granTotal[$key]['totales'][$kh] = "$totalSeccion";
                   $sheet->setCellValueByColumnAndRow($colTotal, $row, "= SUM($initSuma : $endSuma)")
                       ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal).$row)->applyFromArray($stylesTotal);

                    // para devengados realizar calculo de %  con monto individual mensual/ monto vertical acumulado mensual
                    if($key === 'devengados') {
                        for ($initPorcent = $initRow; $initPorcent <= $endRow; $initPorcent++) {
                            $rowSeccion = PHPExcel_Cell::stringFromColumnIndex($colTotal) . $initPorcent;
                            $sheet->setCellValueByColumnAndRow($colTotal + 1, $initPorcent, "=iferror($rowSeccion/$totalSeccion, 0)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1) . $initPorcent)->applyFromArray($stylesPorcent);
                        }
                        $sheet->setCellValueByColumnAndRow($colTotal + 1, $row, "= SUM($initSumaPorcent : $endSumaPorcent)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1) . $row)->applyFromArray($stylesTotalPorcent);
                    } else{
                        $porcent = $totalSeccion."/".$granTotal['devengados']['totales'][$kh];
                        $sheet->setCellValueByColumnAndRow($colTotal + 1, $row, "=iferror($porcent, 0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1) . $row)->applyFromArray($stylesTotalPorcent);
                    }

                   $colTotal +=2;

                }
                //suma vertical de totales acumulados por seccion
                $initSumaAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal).$initRow;
                $endSumaAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal).$endRow;
                $sheet->setCellValueByColumnAndRow($colTotal, $row, "= SUM($initSumaAcumulado : $endSumaAcumulado)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal).$row)->applyFromArray($stylesTotal);

                //suma vertical de porcentaje devengado acumulado
                if($key === 'devengados') {
                    $cellGranTotalAcumDev = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                    $initSumaPorcentAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$initRow;
                    $endSumaPorcentAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$endRow;
                    $sheet->setCellValueByColumnAndRow($colTotal+1, $row, "= SUM($initSumaPorcentAcumulado : $endSumaPorcentAcumulado)")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$row)->applyFromArray($stylesTotalPorcent);
                } else {
                    $cellGranTotalAcum = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                    $sheet->setCellValueByColumnAndRow($colTotal+1, $row, "=iferror($cellGranTotalAcumDev/$cellGranTotalAcum, 0)")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$row)->applyFromArray($stylesTotalPorcent);
                }
                $row +=2;
            }
            $row += 3;

            $colsGranDevengados = [];
            foreach($granTotal as $gk => $gran) {
                $col = 1;
                $sheet->setCellValueByColumnAndRow($col, $row, "Gran total ". $gk);
                $col++;
                foreach($gran['totales'] as $kgt => $gt) {
                    $sheet->setCellValueByColumnAndRow($col, $row, "=$gt")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($stylesTotal);
                    if($gk === 'devengados') {
                        $colDev = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                        $colsGranDevengados[$kgt] = $colDev;
                        $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror(($colDev) / $colDev,0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                    } else {
                        $currentCol = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                        $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror(($currentCol) / $colsGranDevengados[$kgt],0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                    }
                    $col++;
                }
                $row++;
            }
        } else {
            foreach ($gerentes as $key => $gerente) {
                if ($hoja != 0)
                    $sheet = $book->createSheet($hoja);
                $row = 1;
                $col = 0;
                $sheet->setTitle(strtoupper(substr($gerente["name"], 0, 6)));
                $sheet->setCellValueByColumnAndRow($col, $row, "Area")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow(++$col, $row, "Encargado")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
                $col++;
                foreach ($gerente['headerMeses'] as $head) {
                    $sheet->setCellValueByColumnAndRow($col, $row, ucfirst(strtolower($head['name'])))
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
                    $sheet->setCellValueByColumnAndRow(++$col, $row, '%')
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);
                    $col++;
                }
                $sheet->setCellValueByColumnAndRow($col, $row, "Acumulados")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFont()->setBold(true);

                $row++;
                $devAcumulados = [];
                foreach ($gerente['totales'] as $ky => $total) {
                    switch($ky) {
                        case 'nominas':
                            $prefix = 'Gastos nominas';
                            break;
                        case 'utilidades':
                            $prefix = 'Utilidades';
                            break;
                        default:
                            $prefix = 'Ingresos '.$ky;
                            break;
                    }
                    $sheet->setCellValueByColumnAndRow(1, $row, strtoupper($prefix))
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1) . $row)->getFont()->setBold(true);
                    $row++;
                    $initRow = $row;
                    $endRowTmp =  $initRow + count($total);

                    // secciones
                    foreach ($total as $kt => $var) {
                        $col = 0;
                        $sheet->setCellValueByColumnAndRow($col, $row, $var['nameRol']);
                        $col++;
                        $sheet->setCellValueByColumnAndRow($col, $row, $var['name']);
                        $col++;
                        $sumColumns = [];
                        // meses
                        foreach ($var['meses'] as $keyMes => $mes) {
                            $sumCom = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                            array_push($sumColumns, $sumCom);

                            if ($ky === 'devengados') {
                                $devengadosCols[$kt]['meses'][$keyMes] = $sumCom;
                                $colAmountDevMonth = PHPExcel_Cell::stringFromColumnIndex($col).$endRowTmp;

                            } else {
                                $colAmountDevMonth = $devengadosCols[$kt]['meses'][$keyMes];
                            }

                            $sheet->setCellValueByColumnAndRow($col, $row, isset($mes['total']) ? $mes['total'] : 0)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styles);
                            $porcent =  $sumCom."/".$colAmountDevMonth;
                            $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror($porcent, 0)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                            $col++;
                        }
                        $strSumColumns = implode('+', $sumColumns);
                        $sheet->setCellValueByColumnAndRow($col, $row, "=$strSumColumns")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                        $currentDevAcum =  PHPExcel_Cell::stringFromColumnIndex($col).$row;

                        if($ky === 'devengados') {
                            $devAcumulados[$kt] = $currentDevAcum;
                            $amountAcum = PHPExcel_Cell::stringFromColumnIndex($col).$endRowTmp;
                        } else {
                            $amountAcum = $devAcumulados[$kt];
                        }

                        $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror($currentDevAcum/$amountAcum, 0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                        $row += 1;
                    }
                    $endRow =  $row-1;

                    $sheet->setCellValueByColumnAndRow(1, $row, 'Total ' . $prefix)
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
                    $colTotal = 2;

                    //suma vertical acumulado de cada seccion
                    foreach ($gerente['headerMeses'] as $kh => $var) {
                        $initSuma = PHPExcel_Cell::stringFromColumnIndex($colTotal).$initRow;
                        $endSuma = PHPExcel_Cell::stringFromColumnIndex($colTotal).$endRow;
                        $initSumaPorcent = PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$initRow;
                        $endSumaPorcent = PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$endRow;
                        $totalSeccion = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                        $granTotal[$ky]['totales'][$kh] = $totalSeccion;
                        $sheet->setCellValueByColumnAndRow($colTotal, $row, "= SUM($initSuma : $endSuma)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal).$row)->applyFromArray($stylesTotal);

                        // para devengados realizar calculo de %  con monto individual mensual/ monto vertical acumulado mensual
                        if($ky === 'devengados') {
                            for($initPorcent = $initRow; $initPorcent<=$endRow; $initPorcent++) {
                                $rowSeccion = PHPExcel_Cell::stringFromColumnIndex($colTotal).$initPorcent;
                                $sheet->setCellValueByColumnAndRow($colTotal+1, $initPorcent, "=iferror($rowSeccion/$totalSeccion, 0)")
                                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$initPorcent)->applyFromArray($stylesPorcent);
                            }
                            $sheet->setCellValueByColumnAndRow($colTotal + 1, $row, "= SUM($initSumaPorcent : $endSumaPorcent)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1).$row)->applyFromArray($stylesTotalPorcent);
                        } else {
                            $porcent = $totalSeccion."/".$granTotal['devengados']['totales'][$kh];
                            $sheet->setCellValueByColumnAndRow($colTotal + 1, $row, "=iferror($porcent, 0)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal + 1) . $row)->applyFromArray($stylesTotalPorcent);
                        }

                        $colTotal +=2;
                    }

                    //suma vertical de totales acumulados por seccion
                    $initSumaAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal).$initRow;
                    $endSumaAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal).$endRow;
                    $sheet->setCellValueByColumnAndRow($colTotal, $row, "= SUM($initSumaAcumulado : $endSumaAcumulado)")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal).$row)->applyFromArray($stylesTotal);

                    //suma vertical de porcentaje devengado
                    if($ky === 'devengados') {
                        $cellGranTotalAcumDev = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                        $initSumaPorcentAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$initRow;
                        $endSumaPorcentAcumulado = PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$endRow;
                        $sheet->setCellValueByColumnAndRow($colTotal+1, $row, "= SUM($initSumaPorcentAcumulado : $endSumaPorcentAcumulado)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$row)->applyFromArray($stylesTotalPorcent);
                    } else {
                        $cellGranTotalAcum = PHPExcel_Cell::stringFromColumnIndex($colTotal).$row;
                        $sheet->setCellValueByColumnAndRow($colTotal+1, $row, "=iferror($cellGranTotalAcumDev/$cellGranTotalAcum, 0)")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($colTotal+1).$row)->applyFromArray($stylesTotalPorcent);
                    }
                    $row += 2;
                }
                $colsGranDevengados = [];
                foreach($granTotal as $gk => $gran) {
                    $col = 1;
                    $sheet->setCellValueByColumnAndRow($col, $row, "Gran total ". $gk)
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesBold);
                    $col++;
                    foreach($gran['totales'] as $kgt => $gt) {
                        $sheet->setCellValueByColumnAndRow($col, $row, "=$gt")
                            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styles);
                        if($gk === 'devengados') {
                            $colDev = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                            $colsGranDevengados[$kgt] = $colDev;
                            $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror(($colDev) / $colDev,0)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                        } else {
                            $currentCol = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                            $sheet->setCellValueByColumnAndRow(++$col, $row, "=iferror(($currentCol) / $colsGranDevengados[$kgt],0)")
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                        }
                        $col++;
                    }
                    $row++;
                }
                $hoja++;
            }
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
        $nameFile= "ESTADO_DE_RESULTADO_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport = $nameFile;
    }
    function generateSimpleReport($ftr){
        $result = $this->edoResult($ftr);
        exit;
        $book = new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('EDO. RESULTADO');
        $sheet->setCellValueByColumnAndRow(0, 3, "INGRESOS DEVENGANDOS");
        $sheet->setCellValueByColumnAndRow(0, 4, "INGRESOS TRABAJADOS");
        $sheet->setCellValueByColumnAndRow(0, 5, "DIFERENCIA");
        $sheet->setCellValueByColumnAndRow(0, 6, "% TRAB/DEV");
        $sheet->getStyle("A9")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 9, "INGRESOS");
        $sheet->setCellValueByColumnAndRow(0, 10, "INGRESOS PROPIOS  DEL AREA");
        $sheet->getStyle("A11")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 11, "TOTAL DE INGRESOS");
        $sheet->getStyle("A13")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 13, "COSTOS");
        $sheet->setCellValueByColumnAndRow(0, 14, "COSTOS DEL SERVICIO");
        $sheet->setCellValueByColumnAndRow(0, 15, "NOMINAS");
        $sheet->getStyle("A16")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 16, "TOTAL DE COSTO DE VENTAS");
        $sheet->getStyle("A18")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 18, "UTILIDAD BRUTA");
        $sheet->getStyle("A20")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 20, "GASTOS");
        $sheet->setCellValueByColumnAndRow(0, 21, "BONOS DE LAS AREAS");
        $sheet->setCellValueByColumnAndRow(0, 22, "TOTAL DE LOS GASTOS");
        $sheet->getStyle("A24")->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 24, "UTILIDAD NETA");
        $col = 1;
        $totalesDevengado = [];
        $totalesTrabajado = [];
        $totalesIgresos = [];
        $totalesVentas = [];
        $totalesUtilidadBruta = [];
        $totalesGastos = [];
        $totalesUtilidadNeta = [];
        foreach ($result as $key => $value) {
            $stringColCurrent = PHPExcel_Cell::stringFromColumnIndex($col);
            $stringColCurrent1 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
            $sheet->getStyle($stringColCurrent . "1")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col, 1, strtoupper(substr($value["name"], 0, 15)));
            $sheet->setCellValueByColumnAndRow($col + 1, 1, '%');
            $sheet->setCellValueByColumnAndRow($col, 3, $value["totalDevengado"]);
            $coorDevengado = $stringColCurrent . "3";
            $totalesDevengado[] = $coorDevengado;
            $porcentDevengado = $stringColCurrent1 . "3";
            $sheet->setCellValueByColumnAndRow($col + 1, 3, "=$coorDevengado/$coorDevengado");
            $sheet->getStyle($porcentDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle($coorDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValueByColumnAndRow($col, 4, $value["totalCompletado"]);
            $coorTrabajado = $stringColCurrent . "4";
            $totalesTrabajado[] = $coorTrabajado;
            $porcentTrabajado = $stringColCurrent1 . "4";
            $sheet->setCellValueByColumnAndRow($col + 1, 4, "=+$coorTrabajado/$coorDevengado");
            $sheet->getStyle($porcentTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->getStyle($coorTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            $porcentColTrabDev = $stringColCurrent . "6";
            $stringColDiferencia = $stringColCurrent . "5";
            $porcentColDiferencia = $stringColCurrent1 . "5";
            $sheet->getStyle($stringColDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($stringColDiferencia, "=$coorDevengado-$coorTrabajado");
            $sheet->getStyle($porcentColDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColDiferencia, "=+$stringColDiferencia/$coorDevengado");

            $sheet->getStyle($porcentColTrabDev)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColTrabDev, "=+$coorTrabajado/$coorDevengado");

            //columnas

            $colPropiosArea = $stringColCurrent . "10";
            $porcentPropiosArea = $stringColCurrent1 . "10";

            $colTotalIngresos = $stringColCurrent . "11";
            $porcentTotalIngresos = $stringColCurrent1 . "11";

            $colCostoServicio = $stringColCurrent . "14";
            $porcentCostoServicio = $stringColCurrent1 . "14";

            $colNominas = $stringColCurrent . "15";
            $porcentColNominas = $stringColCurrent1 . "15";

            $colTotalVentas = $stringColCurrent . "16";
            $porcentColTotalVentas = $stringColCurrent1 . "16";

            $colUtilidadBruta = $stringColCurrent . "18";
            $porcentColUtilidadBruta = $stringColCurrent1 . "18";

            $colBonosAreas = $stringColCurrent . "21";
            $porcentColBonosAreas = $stringColCurrent1 . "21";

            $colTotalGastos = $stringColCurrent . "22";
            $porcentColTotalGastos = $stringColCurrent1 . "22";

            $colUtilidadNeta = $stringColCurrent . "24";
            $porcentColUtilidadNeta = $stringColCurrent1 . "24";

            $sheet->getStyle($colPropiosArea)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colPropiosArea, "=+$coorTrabajado");
            $sheet->getStyle($porcentPropiosArea)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentPropiosArea, "=+$colPropiosArea/$colTotalIngresos");

            $sheet->getStyle($colTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colTotalIngresos, "= SUM($colPropiosArea : $colPropiosArea)");
            $sheet->getStyle($porcentTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentTotalIngresos, "=+$colTotalIngresos/$colTotalIngresos");
            $totalesIgresos[] = $colTotalIngresos;

            $sheet->getStyle($colCostoServicio)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colCostoServicio, "");
            $sheet->getStyle($porcentCostoServicio)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentCostoServicio, "=+$colCostoServicio/$colTotalIngresos");

            $sheet->getStyle($colNominas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colNominas, $value["sueldoTotalConSub"]);
            $sheet->getStyle($porcentColNominas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColNominas, "=+$colNominas/$colTotalIngresos");

            $sheet->getStyle($colTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colTotalVentas, "=SUM($colCostoServicio : $colNominas)");
            $sheet->getStyle($porcentColTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColTotalVentas, "=+$colTotalVentas/$colTotalIngresos");
            $totalesVentas[]=$colTotalVentas;


            $sheet->getStyle($colUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colUtilidadBruta, "=(+$colTotalIngresos-$colTotalVentas)");
            $sheet->getStyle($porcentColUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColUtilidadBruta, "=+$colUtilidadBruta/$colTotalIngresos");
            $totalesUtilidadBruta [] =$colUtilidadBruta;

            $sheet->getStyle($colBonosAreas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $porcentBono = $value["porcentajeBono"]/100;
            $sheet->setCellValue($colBonosAreas,"=+$colUtilidadBruta*$porcentBono");
            $sheet->getStyle($porcentColBonosAreas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColBonosAreas, "=+$colBonosAreas/$colTotalIngresos");

            $sheet->getStyle($colTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colTotalGastos, "=+$colBonosAreas");
            $sheet->getStyle($porcentColTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColTotalGastos, "=+$colTotalGastos/$colTotalIngresos");
            $totalesGastos[] = $colTotalGastos;

            $sheet->getStyle($colUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colUtilidadNeta, "=+$colUtilidadBruta-$colTotalGastos");
            $sheet->getStyle($porcentColUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($porcentColUtilidadNeta, "=+$colUtilidadNeta/$colTotalIngresos");
            $totalesUtilidadNeta [] =$colUtilidadNeta;
            $col += 2;
        }
        //

        $stringColGranTotal = PHPExcel_Cell::stringFromColumnIndex($col);
        $stringColGranTotalPorcent = PHPExcel_Cell::stringFromColumnIndex($col + 1);

        $sheet->getStyle($stringColGranTotal . "1")->getFont()->setBold(true);
        $sheet->setCellValue($stringColGranTotal."1","TOTAL");
        $sheet->getStyle($stringColGranTotalPorcent . "1")->getFont()->setBold(true);
        $sheet->setCellValue($stringColGranTotalPorcent."1","%");

        $colGranTotalDevengado = $stringColGranTotal . "3";
        $colPorcentGranTotalDevengado = $stringColGranTotalPorcent . "3";
        $colGranTotalTrabajado= $stringColGranTotal."4";
        $colPorcentGranTotalTrabajado = $stringColGranTotalPorcent."4";
        $colGranTotalDiferencia= $stringColGranTotal."5";
        $colPorcentGranTotalDiferencia = $stringColGranTotalPorcent."5";
        $colPorcentGranTrabDev = $stringColGranTotal."6";

        $colGranTotalIngresos = $stringColGranTotal."11";
        $colPorcentGranTotalIngresos = $stringColGranTotalPorcent."11";

        $colGranTotalVentas = $stringColGranTotal."16";
        $colPorcentGranTotalVentas = $stringColGranTotalPorcent."16";
        $colGranUtilidadBruta = $stringColGranTotal."18";
        $colPorcentGranUtilidadBruta = $stringColGranTotalPorcent."18";
        $colGranTotalGastos = $stringColGranTotal."22";
        $colPorcentGranTotalGastos = $stringColGranTotalPorcent."22";

        $colGranUtilidadNeta = $stringColGranTotal."24";
        $colPorcentGranUtilidadNeta = $stringColGranTotalPorcent."24";
        $stringDevengado = "=";
        $stringTrabajado = "=";
        $stringTotalesIngresos = "=";
        $stringTotalesVentas = "=";
        $stringTotalesUtilidadBruta = "=";
        $stringTotalesGastos = "=";
        $stringTotalesUtilidadNeta="=";
        foreach ($totalesDevengado as $item) {
            $stringDevengado .="+" . $item;
        }
        if(empty($totalesDevengado))
            $stringDevengado ="";

        foreach ($totalesTrabajado as $item2) {
            $stringTrabajado .="+" . $item2;
        }
        if(empty($totalesTrabajado))
            $stringTrabajado ="";

        foreach ($totalesIgresos as $item3) {
            $stringTotalesIngresos .="+" . $item3;
        }
        if(empty($totalesIgresos))
            $stringTotalesIngresos ="";

        foreach ($totalesVentas as $item4) {
            $stringTotalesVentas .="+" . $item4;
        }
        if(empty($totalesVentas))
            $stringTotalesVentas ="";

        foreach ($totalesUtilidadBruta as $item5) {
            $stringTotalesUtilidadBruta .="+" . $item5;
        }
        if(empty($totalesUtilidadBruta))
            $stringTotalesUtilidadBruta ="";

        foreach ($totalesGastos as $item6) {
            $stringTotalesGastos .="+" . $item6;
        }
        if(empty($totalesGastos))
            $stringTotalesGastos ="";
        foreach ($totalesUtilidadNeta as $item7) {
            $stringTotalesUtilidadNeta .="+" . $item7;
        }
        if(empty($totalesUtilidadNeta))
            $stringTotalesUtilidadNeta ="";

        $sheet->getStyle($colGranTotalDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalDevengado,$stringDevengado);
        $sheet->getStyle($colPorcentGranTotalDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalDevengado,"=+$colGranTotalDevengado/$colGranTotalDevengado");

        $sheet->getStyle($colGranTotalTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalTrabajado,$stringTrabajado);
        $sheet->getStyle($colPorcentGranTotalTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalTrabajado,"=+$colGranTotalTrabajado/$colGranTotalDevengado");
        $sheet->getStyle($colGranTotalDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalDiferencia,"=+$colGranTotalDevengado-$colGranTotalTrabajado");
        $sheet->getStyle($colPorcentGranTotalDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalDiferencia,"=+$colGranTotalDiferencia/$colGranTotalDevengado");
        $sheet->getStyle($colPorcentGranTrabDev)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTrabDev,"=+$colGranTotalTrabajado/$colGranTotalDevengado");

        $sheet->getStyle($colGranTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalIngresos,$stringTotalesIngresos);
        $sheet->getStyle($colPorcentGranTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalIngresos,"=+$colGranTotalIngresos/$colGranTotalIngresos");

        $sheet->getStyle($colGranTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalVentas,$stringTotalesVentas);
        $sheet->getStyle($colPorcentGranTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalVentas,"=+$colGranTotalVentas/$colGranTotalIngresos");

        $sheet->getStyle($colGranUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranUtilidadBruta,$stringTotalesUtilidadBruta);
        $sheet->getStyle($colPorcentGranUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranUtilidadBruta,"=+$colGranUtilidadBruta/$colGranTotalIngresos");

        $sheet->getStyle($colGranTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranTotalGastos,$stringTotalesGastos);
        $sheet->getStyle($colPorcentGranTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranTotalGastos,"=+$colGranTotalGastos/$colGranTotalIngresos");

        $sheet->getStyle($colGranUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->setCellValue($colGranUtilidadNeta,$stringTotalesUtilidadNeta);
        $sheet->getStyle($colPorcentGranUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->setCellValue($colPorcentGranUtilidadNeta,"=+$colGranUtilidadNeta/$colGranTotalIngresos");

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));

        $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "ESTADO_DE_RESULTADO_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport = $nameFile;
    }
    function generateDetailedReport($ftr=[]){
        $result = $this->generateEstadoResultado($ftr);
        $book = new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        foreach ($result as $keyMain => $valueMain) {
            if($hoja!=0)
                $sheet = $book->createSheet($hoja);
            $sheet->setTitle(strtoupper(substr($valueMain["name"], 0, 6)));
            $sheet->setCellValueByColumnAndRow(0, 3, "INGRESOS DEVENGANDOS");
            $sheet->setCellValueByColumnAndRow(0, 4, "INGRESOS TRABAJADOS");
            $sheet->setCellValueByColumnAndRow(0, 5, "DIFERENCIA");
            $sheet->setCellValueByColumnAndRow(0, 6, "% TRAB/DEV");
            $sheet->getStyle("A9")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 9, "INGRESOS");
            $sheet->setCellValueByColumnAndRow(0, 10, "INGRESOS PROPIOS  DEL AREA");
            $sheet->getStyle("A11")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 11, "TOTAL DE INGRESOS");
            $sheet->getStyle("A13")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 13, "COSTOS");
            $sheet->setCellValueByColumnAndRow(0, 14, "COSTOS DEL SERVICIO");
            $sheet->setCellValueByColumnAndRow(0, 15, "NOMINAS");
            $sheet->getStyle("A16")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 16, "TOTAL DE COSTO DE VENTAS");
            $sheet->getStyle("A18")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 18, "UTILIDAD BRUTA");
            $sheet->getStyle("A20")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 20, "GASTOS");
            $sheet->setCellValueByColumnAndRow(0, 21, "BONOS DE LAS AREAS");
            $sheet->setCellValueByColumnAndRow(0, 22, "TOTAL DE LOS GASTOS");
            $sheet->getStyle("A24")->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(0, 24, "UTILIDAD NETA");

            $col = 1;
            $totalesDevengado = [];
            $totalesTrabajado = [];
            $totalesIgresos = [];
            $totalesVentas = [];
            $totalesUtilidadBruta = [];
            $totalesGastos = [];
            $totalesUtilidadNeta = [];
            foreach($valueMain["detalleSubordinados"] as $key=>$value){
                $stringColCurrent = PHPExcel_Cell::stringFromColumnIndex($col);
                $stringColCurrent1 = PHPExcel_Cell::stringFromColumnIndex($col + 1);
                $sheet->getStyle($stringColCurrent . "1")->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow($col, 1, strtoupper(substr($value['nameLevel'],0,3).". ".substr($value["name"], 0, 10))."(".substr($value["nameJefeInmediato"], 0, 6).")");
                $sheet->setCellValueByColumnAndRow($col + 1, 1, '%');
                $sheet->setCellValueByColumnAndRow($col, 3, $value["totalDevengado"]);
                $coorDevengado = $stringColCurrent . "3";
                $totalesDevengado[] = $coorDevengado;
                $porcentDevengado = $stringColCurrent1 . "3";
                $sheet->setCellValueByColumnAndRow($col + 1, 3, "=$coorDevengado/$coorDevengado");
                $sheet->getStyle($porcentDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->getStyle($coorDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValueByColumnAndRow($col, 4, $value["totalCompletado"]);
                $coorTrabajado = $stringColCurrent . "4";
                $totalesTrabajado[] = $coorTrabajado;
                $porcentTrabajado = $stringColCurrent1 . "4";
                $sheet->setCellValueByColumnAndRow($col + 1, 4, "=+$coorTrabajado/$coorDevengado");
                $sheet->getStyle($porcentTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->getStyle($coorTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $porcentColTrabDev = $stringColCurrent . "6";
                $stringColDiferencia = $stringColCurrent . "5";
                $porcentColDiferencia = $stringColCurrent1 . "5";
                $sheet->getStyle($stringColDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($stringColDiferencia, "=$coorDevengado-$coorTrabajado");
                $sheet->getStyle($porcentColDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColDiferencia, "=+$stringColDiferencia/$coorDevengado");

                $sheet->getStyle($porcentColTrabDev)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColTrabDev, "=+$coorTrabajado/$coorDevengado");

                //columnas

                $colPropiosArea = $stringColCurrent . "10";
                $porcentPropiosArea = $stringColCurrent1 . "10";

                $colTotalIngresos = $stringColCurrent . "11";
                $porcentTotalIngresos = $stringColCurrent1 . "11";

                $colCostoServicio = $stringColCurrent . "14";
                $porcentCostoServicio = $stringColCurrent1 . "14";

                $colNominas = $stringColCurrent . "15";
                $porcentColNominas = $stringColCurrent1 . "15";

                $colTotalVentas = $stringColCurrent . "16";
                $porcentColTotalVentas = $stringColCurrent1 . "16";

                $colUtilidadBruta = $stringColCurrent . "18";
                $porcentColUtilidadBruta = $stringColCurrent1 . "18";

                $colBonosAreas = $stringColCurrent . "21";
                $porcentColBonosAreas = $stringColCurrent1 . "21";

                $colTotalGastos = $stringColCurrent . "22";
                $porcentColTotalGastos = $stringColCurrent1 . "22";

                $colUtilidadNeta = $stringColCurrent . "24";
                $porcentColUtilidadNeta = $stringColCurrent1 . "24";

                $sheet->getStyle($colPropiosArea)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colPropiosArea, "=+$coorTrabajado");
                $sheet->getStyle($porcentPropiosArea)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentPropiosArea, "=+$colPropiosArea/$colTotalIngresos");

                $sheet->getStyle($colTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colTotalIngresos, "= SUM($colPropiosArea : $colPropiosArea)");
                $sheet->getStyle($porcentTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentTotalIngresos, "=+$colTotalIngresos/$colTotalIngresos");
                $totalesIgresos[] = $colTotalIngresos;

                $sheet->getStyle($colCostoServicio)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colCostoServicio, "");
                $sheet->getStyle($porcentCostoServicio)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentCostoServicio, "=+$colCostoServicio/$colTotalIngresos");

                $sheet->getStyle($colNominas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colNominas, $value["sueldoTotal"]);
                $sheet->getStyle($porcentColNominas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColNominas, "=+$colNominas/$colTotalIngresos");

                $sheet->getStyle($colTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colTotalVentas, "=SUM($colCostoServicio : $colNominas)");
                $sheet->getStyle($porcentColTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColTotalVentas, "=+$colTotalVentas/$colTotalIngresos");
                $totalesVentas[]=$colTotalVentas;


                $sheet->getStyle($colUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colUtilidadBruta, "=(+$colTotalIngresos-$colTotalVentas)");
                $sheet->getStyle($porcentColUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColUtilidadBruta, "=+$colUtilidadBruta/$colTotalIngresos");
                $totalesUtilidadBruta [] =$colUtilidadBruta;

                $sheet->getStyle($colBonosAreas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $porcentBono = $value["porcentajeBono"]/100;
                $sheet->setCellValue($colBonosAreas, "=+$colUtilidadBruta*$porcentBono");
                $sheet->getStyle($porcentColBonosAreas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColBonosAreas, "=+$colBonosAreas/$colTotalIngresos");

                $sheet->getStyle($colTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colTotalGastos, "=+$colBonosAreas");
                $sheet->getStyle($porcentColTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColTotalGastos, "=+$colTotalGastos/$colTotalIngresos");
                $totalesGastos[] = $colTotalGastos;

                $sheet->getStyle($colUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->setCellValue($colUtilidadNeta, "=+$colUtilidadBruta-$colTotalGastos");
                $sheet->getStyle($porcentColUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $sheet->setCellValue($porcentColUtilidadNeta, "=+$colUtilidadNeta/$colTotalIngresos");
                $totalesUtilidadNeta [] =$colUtilidadNeta;
                $col += 2;
            }
            /*
            $stringColGranTotal = PHPExcel_Cell::stringFromColumnIndex($col);
            $stringColGranTotalPorcent = PHPExcel_Cell::stringFromColumnIndex($col + 1);

            $sheet->getStyle($stringColGranTotal . "1")->getFont()->setBold(true);
            $sheet->setCellValue($stringColGranTotal."1","TOTAL");
            $sheet->getStyle($stringColGranTotalPorcent . "1")->getFont()->setBold(true);
            $sheet->setCellValue($stringColGranTotalPorcent."1","%");

            $colGranTotalDevengado = $stringColGranTotal . "3";
            $colPorcentGranTotalDevengado = $stringColGranTotalPorcent . "3";
            $colGranTotalTrabajado= $stringColGranTotal."4";
            $colPorcentGranTotalTrabajado = $stringColGranTotalPorcent."4";
            $colGranTotalDiferencia= $stringColGranTotal."5";
            $colPorcentGranTotalDiferencia = $stringColGranTotalPorcent."5";
            $colPorcentGranTrabDev = $stringColGranTotal."6";

            $colGranTotalIngresos = $stringColGranTotal."11";
            $colPorcentGranTotalIngresos = $stringColGranTotalPorcent."11";

            $colGranTotalVentas = $stringColGranTotal."16";
            $colPorcentGranTotalVentas = $stringColGranTotalPorcent."16";
            $colGranUtilidadBruta = $stringColGranTotal."18";
            $colPorcentGranUtilidadBruta = $stringColGranTotalPorcent."18";
            $colGranTotalGastos = $stringColGranTotal."22";
            $colPorcentGranTotalGastos = $stringColGranTotalPorcent."22";

            $colGranUtilidadNeta = $stringColGranTotal."24";
            $colPorcentGranUtilidadNeta = $stringColGranTotalPorcent."24";
            $stringDevengado = "=";
            $stringTrabajado = "=";
            $stringTotalesIngresos = "=";
            $stringTotalesVentas = "=";
            $stringTotalesUtilidadBruta = "=";
            $stringTotalesGastos = "=";
            $stringTotalesUtilidadNeta="=";
            foreach ($totalesDevengado as $item) {
                $stringDevengado .="+" . $item;
            }
            if(empty($totalesDevengado))
                $stringDevengado ="";

            foreach ($totalesTrabajado as $item2) {
                $stringTrabajado .="+" . $item2;
            }
            if(empty($totalesTrabajado))
                $stringTrabajado ="";

            foreach ($totalesIgresos as $item3) {
                $stringTotalesIngresos .="+" . $item3;
            }
            if(empty($totalesIgresos))
                $stringTotalesIngresos ="";

            foreach ($totalesVentas as $item4) {
                $stringTotalesVentas .="+" . $item4;
            }
            if(empty($totalesVentas))
                $stringTotalesVentas ="";

            foreach ($totalesUtilidadBruta as $item5) {
                $stringTotalesUtilidadBruta .="+" . $item5;
            }
            if(empty($totalesUtilidadBruta))
                $stringTotalesUtilidadBruta ="";

            foreach ($totalesGastos as $item6) {
                $stringTotalesGastos .="+" . $item6;
            }
            if(empty($totalesGastos))
                $stringTotalesGastos ="";
            foreach ($totalesUtilidadNeta as $item7) {
                $stringTotalesUtilidadNeta .="+" . $item7;
            }
            if(empty($totalesUtilidadNeta))
                $stringTotalesUtilidadNeta ="";

            $sheet->getStyle($colGranTotalDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalDevengado,$stringDevengado);
            $sheet->getStyle($colPorcentGranTotalDevengado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalDevengado,"=+$colGranTotalDevengado/$colGranTotalDevengado");

            $sheet->getStyle($colGranTotalTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalTrabajado,$stringTrabajado);
            $sheet->getStyle($colPorcentGranTotalTrabajado)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalTrabajado,"=+$colGranTotalTrabajado/$colGranTotalDevengado");
            $sheet->getStyle($colGranTotalDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalDiferencia,"=+$colGranTotalDevengado-$colGranTotalTrabajado");
            $sheet->getStyle($colPorcentGranTotalDiferencia)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalDiferencia,"=+$colGranTotalDiferencia/$colGranTotalDevengado");
            $sheet->getStyle($colPorcentGranTrabDev)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTrabDev,"=+$colGranTotalTrabajado/$colGranTotalDevengado");

            $sheet->getStyle($colGranTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalIngresos,$stringTotalesIngresos);
            $sheet->getStyle($colPorcentGranTotalIngresos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalIngresos,"=+$colGranTotalIngresos/$colGranTotalIngresos");

            $sheet->getStyle($colGranTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalVentas,$stringTotalesVentas);
            $sheet->getStyle($colPorcentGranTotalVentas)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalVentas,"=+$colGranTotalVentas/$colGranTotalIngresos");

            $sheet->getStyle($colGranUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranUtilidadBruta,$stringTotalesUtilidadBruta);
            $sheet->getStyle($colPorcentGranUtilidadBruta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranUtilidadBruta,"=+$colGranUtilidadBruta/$colGranTotalIngresos");

            $sheet->getStyle($colGranTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranTotalGastos,$stringTotalesGastos);
            $sheet->getStyle($colPorcentGranTotalGastos)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranTotalGastos,"=+$colGranTotalGastos/$colGranTotalIngresos");

            $sheet->getStyle($colGranUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue($colGranUtilidadNeta,$stringTotalesUtilidadNeta);
            $sheet->getStyle($colPorcentGranUtilidadNeta)->GetNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $sheet->setCellValue($colPorcentGranUtilidadNeta,"=+$colGranUtilidadNeta/$colGranTotalIngresos");*/
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
        $nameFile= "ESTADO_DE_RESULTADO_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport=$nameFile;
    }

}
