<?php

class BonoConcentrado extends Personal
{

    private $resultados;
    private $nameReport;
    function convertirToArbol(array $nodos, $parentId)
    {
        $arbol = array();
        foreach($nodos as $nodo) {
            if($nodo['jefe'] == $parentId) {
                $nodo['subordinados'] = $this->convertirToArbol($nodos, $nodo['id']);
                $arbol[] = $nodo;
            }
        }
        return $arbol;
    }

    function convertirToLineal(array $array) {

        $return =  array();
        foreach ($array as $value) {
            $tmpValue = $value;
            unset($tmpValue['subordinados']);
            array_push($return, $tmpValue);

            if (is_array($value['subordinados'])){
                $return = array_merge($return, $this->convertirToLineal($value['subordinados']));
            }
        }
        return $return;
    }

    public function acumularTotales($padre, $mes) {

        $tree   = $this->convertirToArbol($this->resultados, $padre);
        $lineal =  $this->convertirToLineal($tree);
        $devengados = array_column($lineal, $mes);
        $trabajados = array_column($lineal, $mes."_trabajado");
        $sueldos    = array_column($lineal, 'sueldo');

        $acumulados['devengado'] = array_sum($devengados);
        $acumulados['trabajado'] = array_sum($trabajados);
        $acumulados['sueldo'] = array_sum($sueldos) *  (1 + (PORCENTAJE_AUMENTO_SALARIO/100));
        return $acumulados;
    }

    function getInformacionEnCascada() {

        // sp_get_acumulado_x_empleado(empleadoId,anio,soloDesuDepartameno)

        $sql = "call sp_get_acumulado_x_empleado(0, ".$_POST["year"].",1)";
        $this->Util()->DB()->setQuery($sql);
        $resultados  = $this->Util()->DB()->GetResult();
        $this->resultados = $resultados;
        $directores =  array_filter($resultados, fn($item) => ($item['puesto'] === 'Director' && $item['departamento'] !== 'Socios'));
        $new = [];
        foreach($directores as $director) {

            $new[] = $director;
            $subordinados =  $this->convertirToArbol($resultados, $director['id']);

           $subordinadosCascada = $this->convertirToLineal($subordinados);
            $new = array_merge($new, $subordinadosCascada);
        }

        return $new;
    }


    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData()
    {
        return $this->getInformacionEnCascada();
    }
    public function generateReport()
    {
        global $global_config_style_cell;
        $resultados = $this->generateData();

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $sheet->setTitle('CONSOLIDACION DE BONOS');

        $headersEstatico = ['No','AREA', 'PUESTO', 'FECHA INGRESO', 'NOMBRE'];
        $_POST['period'] =  12;
        $headersMeses = $this->Util()->listMonthCompleteHeaderForReport($_POST['period']);

        $puestos = [
            'Director'   => 'Director',
            'Gerente'    => 'Gerente',
            'Supervisor' => 'Supervisor',
            'Contador'   => 'Encargado Sr',
            'Auxiliar'   => 'Encargado Jr',
        ];

        // Todos los headers realizados.
        $row =  4;
        $col = 0;
        $styleHeaderDark = array_merge($global_config_style_cell['style_header'],array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Aptos',
            )
        ));
        $styleHeaderBlue = array_merge($global_config_style_cell['style_header'],array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '2F75B5')
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));
        $styleHeaderBlueDark = array_merge($global_config_style_cell['style_header'],array(
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '203764')
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));
        $styleSimpleText = array_merge($global_config_style_cell['style_simple_text_whit_border'],array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));
        $styleCurrency = array_merge($global_config_style_cell['style_currency'],array(
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));

        $stylePorcentaje= array_merge($global_config_style_cell['style_porcent'],array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE,
            ],
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));

        $styleBorderRight  = array_merge($styleCurrency,array(
            'font' => array(
                'size' => 10,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb' => '000000')
                )
            )
        ));

        $darkInicio = 'A1';
        $darkFin = PHPExcel_Cell::stringFromColumnIndex(count($headersEstatico)-1).'3';
        $sheet->setCellValueByColumnAndRow(1, 2, 'REPORTE CONSOLIDADO DE BONOS CORRESPONDIENTE AL AÑO FISCAL '.$_POST['year']);
        $sheet->setCellValueByColumnAndRow(1, 3, 'FECHA Y HORA DE CONSULTA: '.date('d/m/Y H:i'));
        $sheet->getStyle($darkInicio.":".$darkFin)->applyFromArray($styleHeaderDark);
        $col = 0;
        foreach ($headersEstatico  as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header)
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }

        $headersTotales =  ['DEVENGADO','TRABAJADO','% EFECTIVIDAD','SUELDO 40%','GASTO','UTILIDAD','% UTILIDAD','% BONO', 'BONO A PAGAR','BONO EFEC 100%','BONO PENDIENTE'];

        $trimestre = [];
        $numTrimestre = 1;
        foreach ($headersMeses  as $headerMes) {
            $colInicio = $col;
            $darkInicio = PHPExcel_Cell::stringFromColumnIndex($col).'1';

            $sheet->setCellValueByColumnAndRow($col, 3,'*CIFRAS ACUMULADAS');
            $sheet->setCellValueByColumnAndRow($col + count($headersTotales)-1, 2, strtoupper($headerMes));
            foreach ($headersTotales as $headerTotal) {
                $sheet->setCellValueByColumnAndRow($col, $row, $headerTotal);
                $col++;
            }

            array_push($trimestre, $headerMes);

            $darkFin = PHPExcel_Cell::stringFromColumnIndex($colInicio + count($headersTotales) - 1) . '4';
            $sheet->getStyle($darkInicio . ":" . $darkFin)->applyFromArray($styleHeaderBlue);


            if(count($trimestre) === 3) {
                $coorInicialTotalesTrimestre = PHPExcel_Cell::stringFromColumnIndex($col) . '1';

                $sheet->setCellValueByColumnAndRow($col, 2, $numTrimestre.' TRIMESTRE');
                $sheet->setCellValueByColumnAndRow($col, 4, 'TOTAL A PAGAR');
                $col++;
                $sheet->setCellValueByColumnAndRow($col, 4, 'BONO EFEC 100%');
                $col++;
                $sheet->setCellValueByColumnAndRow($col, 4, 'BONO PENDIENTE');
                $col++;

                $coorFinTotalesTrimestre = PHPExcel_Cell::stringFromColumnIndex($col-1) . '4';

                $sheet->getStyle($coorInicialTotalesTrimestre.":".$coorFinTotalesTrimestre)->applyFromArray($styleHeaderBlueDark);

                $trimestre = [];
                $numTrimestre++;
            }
        }
        // ANUAL
        $coorInicialGranTotal = PHPExcel_Cell::stringFromColumnIndex($col) . '1';
        $sheet->setCellValueByColumnAndRow($col, 2, 'GRAN TOTAL ANUAL');
        $sheet->setCellValueByColumnAndRow($col, 4, 'TOTAL A PAGAR');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, 4, 'BONO EFECTIVO 100%');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, 4, 'BONO PENDIENTE');
        $col++;

        $coorFinGranTotal = PHPExcel_Cell::stringFromColumnIndex($col-1) . '4';
        $sheet->getStyle($coorInicialGranTotal.":".$coorFinGranTotal)->applyFromArray($styleHeaderDark);



        $row++;
        foreach ($resultados as $kdir => $resultado) {
            $col = 0;
            switch ($resultado['puesto']) {
                case 'Director':
                            $color =  'ffc000';
                            $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Gerente':
                    $color =  '808080';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Supervisor':
                    $color =  'BFBFBF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Contador':
                    $color =  'D9D9D9';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                default:
                    $color = '';
                    $typeFill = PHPExcel_Style_Fill::FILL_NONE;
                    break;
            }

            $styleSimpleText2 = array_merge($styleSimpleText, array(
                 'fill' => array(
                     'type' => $typeFill,
                     'color' => array('rgb' => $color))
                )
            );
            $styleBorderRight2 = array_merge($styleSimpleText, array(
                    'fill' => array(
                        'type' => $typeFill,
                        'color' => array('rgb' => $color))
                )
            );

            $sheet->setCellValueByColumnAndRow($col, $row, 1)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['departamento'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $puestos[$resultado['puesto']] ?? $resultado['puesto'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['fecha_ingreso'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['nombre'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleBorderRight2);
            $col++;

            $acumuladoBonoPagar     = [];
            $acumuladoBonoEfectivo  = [];
            $acumuladoBonoPendiente = [];

            $totalesBonoPagarTrimestre     = [];
            $totalesBonoEfectivoTrimestre  = [];
            $totalesBonoPendienteTrimestre = [];

            foreach ($headersMeses  as $mes) {
                $colDevegandoActual = '';
                $colTrabajadoActual = '';
                $acumulados =  $this->acumularTotales($resultado['id'], strtolower($mes));

                foreach ($headersTotales as $kh => $headerTotal) {
                    switch ($kh) {
                        case 0:
                            $totalDevengado = $acumulados['devengado'] + $resultado[strtolower($mes)];
                            $coorDevengado = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalDevengado)
                                ->getStyle($coorDevengado)->applyFromArray($styleCurrency);
                            $colDevegandoActual =  $col;
                            break;
                        case 1:
                            $totalTrabajado = $acumulados['trabajado'] + $resultado[strtolower($mes)."_trabajado"];
                            $coorTrabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalTrabajado)
                                ->getStyle($coorTrabajado)->applyFromArray($styleCurrency);
                            $colTrabajadoActual =  $col;
                            break;
                        case 2:
                            $formula = "=IFERROR(+".PHPExcel_Cell::stringFromColumnIndex($colTrabajadoActual) . $row."/".PHPExcel_Cell::stringFromColumnIndex($colDevegandoActual).$row.",0)";
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($stylePorcentaje);
                            break;
                        case 3:
                            $totalSueldo = $resultado['sueldo'] * (1 + (PORCENTAJE_AUMENTO_SALARIO/100));
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalSueldo)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
                            break;
                        case 4:
                            $totalGasto = $acumulados['sueldo'] + ($resultado['sueldo'] * (1 + (PORCENTAJE_AUMENTO_SALARIO/100)));
                            $coorGasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalGasto)
                                ->getStyle($coorGasto)->applyFromArray($styleCurrency);
                            break;
                        case 5:
                            $formula = "=IFERROR(+".$coorTrabajado."-".$coorGasto.",0)";
                            $coorUtilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
                            break;
                        case 6:
                            $formula = "=IFERROR(+".$coorUtilidad."/".$coorTrabajado.",0)";
                            $coorPorUtilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($stylePorcentaje);
                            break;
                        case 7:
                            $coorPorBono = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, "=IFERROR(".$resultado['porcentaje']."/100,0)")
                                ->getStyle($coorPorBono)->applyFromArray($stylePorcentaje);
                            break;
                        case 8:
                            $formula = "=IFERROR(IF(AND(".$coorUtilidad.">0,".$coorPorUtilidad.">=90%),".$coorUtilidad."*(".$coorPorBono."),0), 0)";
                            $coorBonoPagar = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            array_push($acumuladoBonoPagar, $coorBonoPagar);
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle($coorBonoPagar)->applyFromArray($styleCurrency);
                            break;
                        case 9:
                            $formula = "=IFERROR(IF(".$coorDevengado.">".$coorGasto.",((".$coorDevengado."-".$coorGasto.")*".$coorPorBono."),0), 0)";
                            $coorBonoEfectivo = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            array_push($acumuladoBonoEfectivo, $coorBonoEfectivo);
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle($coorBonoEfectivo)->applyFromArray($styleCurrency);
                            break;
                        case 10:
                            $formula = "=IFERROR(IF(".$coorDevengado."<".$coorGasto.",0,(".$coorBonoEfectivo."-".$coorBonoPagar.")), 0)";
                            $coorBonoPendiente = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            array_push($acumuladoBonoPendiente, $coorBonoPendiente);
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle($coorBonoPendiente)->applyFromArray($styleBorderRight);
                            break;
                    }
                    $col++;
                }

                if(count($acumuladoBonoPagar) === 3) {

                    $styleTotalTrimeste  = array_merge($styleCurrency,array(
                        'font' => array(
                            'bold' => true,
                            'size' => 10,
                            'name' => 'Aptos',
                        ),
                        'borders' => array(
                            'left' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                                'color' => array('rgb' => '000000')
                            ),
                            'right' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    ));
                    $formula = "=+".implode('+',  $acumuladoBonoPagar);
                    $coorTotalBonoPagarTrimestre= PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleTotalTrimeste);
                    $col++;
                    $totalesBonoPagarTrimestre[] = $coorTotalBonoPagarTrimestre;
                    $acumuladoBonoPagar = [];

                    $formula = "=+".implode('+',  $acumuladoBonoEfectivo);
                    $coorTotalBonoEfectivoTrimestre= PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                        ->getStyle($coorTotalBonoEfectivoTrimestre)->applyFromArray($styleTotalTrimeste);
                    $col++;
                    $totalesBonoEfectivoTrimestre[] = $coorTotalBonoEfectivoTrimestre;
                    $acumuladoBonoEfectivo = [];

                    $formula = "=+".implode('+',  $acumuladoBonoPendiente);
                    $coorTotalBonoPendienteTrimestre= PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                    $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleTotalTrimeste);
                    $col++;
                    $totalesBonoPendienteTrimestre[] = $coorTotalBonoPendienteTrimestre;
                    $acumuladoBonoPendiente = [];
                }
            }
            // ANUAL GRAN TOTAL POR FILA
            $formula = "=+".implode('+',  $totalesBonoPagarTrimestre);
            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleTotalTrimeste);
            $col++;
            $formula = "=+".implode('+',  $totalesBonoEfectivoTrimestre);
            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleTotalTrimeste);
            $col++;
            $formula = "=+".implode('+',  $totalesBonoPendienteTrimestre);
            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleTotalTrimeste);
            $row++;
        }

        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "BONOS_CONSOLIDADO" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }
}
