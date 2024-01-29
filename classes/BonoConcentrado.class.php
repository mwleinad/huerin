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

        $headersEstatico = ['No','AREA', 'PUESTO', 'FECHA INGRESO', 'NOMBRE'];
        $headersMeses = $this->Util()->listMonthCompleteHeaderForReport($_POST['period']);

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
                'color' => array('rgb' => '1F4E78')
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

        $darkInicio = 'A1';
        $darkFin = PHPExcel_Cell::stringFromColumnIndex(count($headersEstatico)-1).'3';
        $sheet->setCellValueByColumnAndRow(1, 2, 'REPORTE CONSOLIDADO DE BONOS');
        $sheet->getStyle($darkInicio.":".$darkFin)->applyFromArray($styleHeaderDark);
        $col = 0;
        foreach ($headersEstatico  as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header)
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }

        $headersTotales =  ['DEVENGADO','TRABAJADO','% EFECTIVIDAD','SUELDO 40%','GASTO','UTILIDAD','UTILIDAD(%)','BONO(%)', 'BONO','SUBTOTAL'];
        foreach ($headersMeses  as $headerMes) {
            $darkInicio = PHPExcel_Cell::stringFromColumnIndex($col).'1';

            $sheet->setCellValueByColumnAndRow($col, 3,'*CIFRAS ACUMULADAS');
            $sheet->setCellValueByColumnAndRow($col + count($headersTotales)-1, 2, strtoupper($headerMes));
            foreach ($headersTotales as $headerTotal) {
                $sheet->setCellValueByColumnAndRow($col, $row, $headerTotal);
                $col++;
            }

            $darkFin = PHPExcel_Cell::stringFromColumnIndex($col - 1 + count($headersTotales)-1).'4';
            $sheet->getStyle($darkInicio.":".$darkFin)->applyFromArray($styleHeaderBlue);
        }

        $row++;
        foreach ($resultados as $kdir => $resultado) {
            $col = 0;
            switch ($resultado['puesto']) {
                case 'Director':
                            $color =  '219ebc';
                            $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;

                case 'Gerente':
                    $color =  '2a9d8f';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                default:
                    $color = '';
                    $typeFill = PHPExcel_Style_Fill::FILL_NONE;
                    break;
            }

            $global_config_style_cell['style_simple_text_whit_border']['fill'] = array(
                'type' => $typeFill,
                'color' => array('rgb' => $color)
            );

            $sheet->setCellValueByColumnAndRow($col, $row, 1)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_simple_text_whit_border']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['departamento'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_simple_text_whit_border']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['puesto'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_simple_text_whit_border']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['fecha_ingreso'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_simple_text_whit_border']);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['nombre'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_simple_text_whit_border']);
            $col++;
            foreach ($headersMeses  as $mes) {
                $colDevegandoActual = '';
                $colTrabajadoActual = '';
                $acumulados =  $this->acumularTotales($resultado['id'], strtolower($mes));
                foreach ($headersTotales as $kh => $headerTotal) {
                    switch ($kh) {
                        case 0:
                            $totalDevengado = $acumulados['devengado'] + $resultado[strtolower($mes)];
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalDevengado)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
                            $colDevegandoActual =  $col;
                            break;
                        case 1:
                            $totalTrabajado = $acumulados['trabajado'] + $resultado[strtolower($mes)."_trabajado"];
                            $coorTrabajado = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalTrabajado)
                                ->getStyle($coorTrabajado)->applyFromArray($global_config_style_cell['style_currency']);
                            $colTrabajadoActual =  $col;
                            break;
                        case 2:
                            $formula = "=IFERROR(+".PHPExcel_Cell::stringFromColumnIndex($colTrabajadoActual) . $row."/".PHPExcel_Cell::stringFromColumnIndex($colDevegandoActual).$row.",0)";
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                            break;
                        case 3:
                            $totalSueldo = $resultado['sueldo'] * (1 + (PORCENTAJE_AUMENTO_SALARIO/100));
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalSueldo)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
                            break;
                        case 4:
                            $totalGasto = $acumulados['sueldo'] + ($resultado['sueldo'] * (1 + (PORCENTAJE_AUMENTO_SALARIO/100)));
                            $coorGasto = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $totalGasto)
                                ->getStyle($coorGasto)->applyFromArray($global_config_style_cell['style_currency']);
                            break;
                        case 5:
                            $formula = "=IFERROR(+".$coorTrabajado."-".$coorGasto.",0)";
                            $coorUtilidad = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
                            break;
                        case 6:
                            $formula = "=IFERROR(+".$coorUtilidad."/".$coorTrabajado.",0)";
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_porcent']);
                            break;
                        case 7:
                            $coorPorBono = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, 0)
                                ->getStyle($coorPorBono)->applyFromArray($global_config_style_cell['style_porcent']);
                            break;
                        case 8:
                            $formula = "=IFERROR(+".$coorUtilidad."*".$coorPorBono.",0)";
                            $coorBono = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle($coorBono)->applyFromArray($global_config_style_cell['style_currency']);
                            break;
                        case 9:
                            $formula = "=IFERROR(+".$coorBono.",0)";
                            $sheet->setCellValueByColumnAndRow($col, $row, $formula)
                                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($global_config_style_cell['style_currency']);
                            break;
                    }
                    $col++;
                }
            }
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
