<?php

class organigrama extends Personal
{

    private $resultados;
    private $nameReport;

    function convertirToArbol(array $nodos, $parentId)
    {
        $arbol = array();
        foreach ($nodos as $nodo) {
            if ($nodo['jefe'] == $parentId) {
                $nodo['subordinados'] = $this->convertirToArbol($nodos, $nodo['id']);
                $arbol[] = $nodo;
            }
        }
        return $arbol;
    }

    function convertirToLineal(array $array)
    {

        $return = array();
        foreach ($array as $value) {
            $tmpValue = $value;
            unset($tmpValue['subordinados']);
            array_push($return, $tmpValue);

            if (is_array($value['subordinados'])) {
                $return = array_merge($return, $this->convertirToLineal($value['subordinados']));
            }
        }
        return $return;
    }

    public function acumularTotales($padre, $mes)
    {

        $tree = $this->convertirToArbol($this->resultados, $padre);
        $lineal = $this->convertirToLineal($tree);
        $devengados = array_column($lineal, $mes);
        $trabajados = array_column($lineal, $mes . "_trabajado");
        $sueldos = array_column($lineal, 'sueldo');

        $acumulados['devengado'] = array_sum($devengados);
        $acumulados['trabajado'] = array_sum($trabajados);
        $acumulados['sueldo'] = array_sum($sueldos) * (1 + (PORCENTAJE_AUMENTO_SALARIO / 100));
        return $acumulados;
    }

    function getInformacionEnCascada()
    {

        $sql = "call sp_get_empleados()";
        $this->Util()->DB()->setQuery($sql);
        $resultados = $this->Util()->DB()->GetResult();
        $this->resultados = $resultados;
        $directores = array_filter($resultados, fn($item) => ($item['area'] !== 'Socios'));
        $new = [];
        foreach ($directores as $director) {
            $newLineal = array_column($new, 'id');
            if (in_array($director['id'], $newLineal))
                continue;

            $new[] = $director;
            $subordinados = $this->convertirToArbol($resultados, $director['id']);
            $subordinadosCascada = $this->convertirToLineal($subordinados);
            $new = array_merge($new, $subordinadosCascada);
        }

        return $new;
    }

    function agruparPorDepartamento($items) {

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
        global $global_config_style_cell, $catalogue;

        $resultados = $this->generateData();
        $departamentos = $catalogue->EnumerateCatalogue('departamentos');

        // TODO agrupar por el departamento pero respetar el orden


        $areasSinCambioDePuesto = [
            'Administración',
            'Administracion',
            'Atención al Cliente',
            'Atencion al Cliente',
            'Cuentas por cobrar',
            'Finanzas',
            'Fiscal',
            'Sistemas'
        ];

        $areasAdministrativas = [
            'Administración',
            'Administracion',
            'Atención al Cliente',
            'Atencion al Cliente',
            'Cuentas por cobrar',
            'Finanzas',
            'Desarrollo Organizacional',
            'Fiscal',
            'Sistemas'
        ];

        $areasOperativas =  array_filter($departamentos, fn($depa) =>  !in_array($depa['departamento'], $areasAdministrativas));
        $areasOperativas =  array_column($areasOperativas, 'departamento');

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $sheet->setTitle('ORGANIGRAMA');

        $headersEstatico = ['No', 'AREA', 'DEPARTAMENTO', 'NOMENCLATURA DEL PUESTO', 'NOMBRE DEL PUESTO', 'FECHA DE INGRESO', 'NOMBRE', 'SUELDO', 'CUENTA', 'EXT', 'EMAIL', 'EMAIL GRUPO', 'LISTA DISTRIBUCION'];
        $puestos = [
            'Director' => 'Director',
            'Gerente' => 'Gerente',
            'Supervisor' => 'Supervisor',
            'Contador' => 'Encargado Sr',
            'Auxiliar' => 'Encargado Jr',
        ];
        // Todos los headers realizados.
        $row = 4;
        $styleHeaderDark = array_merge($global_config_style_cell['style_header'], array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 10,
                'name' => 'Aptos',
            )
        ));
        $styleHeaderDarkLight = array_merge($global_config_style_cell['style_header'], array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            ),
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 8,
                'name' => 'Aptos',
            )
        ));


        $styleSimpleText = array_merge($global_config_style_cell['style_simple_text_whit_border'], array(
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

        $darkInicio = 'A1';
        $darkFin = PHPExcel_Cell::stringFromColumnIndex(count($headersEstatico) - 1) . '3';
        $sheet->setCellValueByColumnAndRow(1, 2, 'ORGANIGRAMA ' . $_POST['year']);
        $sheet->getStyle($darkInicio . ":" . $darkFin)->applyFromArray($styleHeaderDark);
        $sheet->setCellValueByColumnAndRow(1, 3, 'FECHA Y HORA DE CONSULTA: ' . date('d/m/Y H:i'))
            ->getStyle('B3')->applyFromArray($styleHeaderDarkLight);
        $col = 0;
        foreach ($headersEstatico as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }

        $row++;
        // Todo agrupar por campo departamento para obtener totales.
        $next = 1;
        $rowInicial = $row;
        $rowInicialGeneral = $row;
        foreach ($resultados as $resultado) {
            $col = 0;
            switch ($resultado['puesto']) {
                case 'Director':
                    $color = '000000';
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Gerente':
                    $color = '2E1304';
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Subgerente':
                    $color = '833C0C';
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Supervisor':
                    $color = 'FFDCC1';
                    $colorFont = '000000';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                default:
                    $color = '';
                    $colorFont = '000000';
                    $typeFill = PHPExcel_Style_Fill::FILL_NONE;
                    break;
            }

            $styleSubtotalDepartamento = array_merge($styleSimpleText, array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00'))
                )
            );

            $styleSimpleText2 = array_merge($styleSimpleText, array(
                    'font' => array(
                        'bold' => false,
                        'color' => array('rgb' => $colorFont),
                        'size' => 10,
                        'name' => 'Aptos',
                    ),
                    'fill' => array(
                        'type' => $typeFill,
                        'color' => array('rgb' => $color)),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_NONE,
                        )
                    )
                )
            );

            $styleSimpleText2Date = $styleSimpleText2;
            $styleSimpleText2Date['numberformat']['code'] = PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY;

            $styleCurrency = array_merge($global_config_style_cell['style_currency'], array(
                'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => $colorFont),
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => $typeFill,
                    'color' => array('rgb' => $color)
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE,
                    )
                )
            ));

            $styleCurrencySubtotal = array_merge($styleCurrency, array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE,
                    )
                )
            ));


            $styleGranTotalDark = array_merge($styleSimpleText, array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FFFFFF'),
                        'size' => 10,
                        'name' => 'Aptos',
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000'))
                )
            );
            $styleGranTotalLight = $styleGranTotalDark;
            $styleGranTotalLight['font']['color']['rgb'] = '000000';
            $styleGranTotalLight['fill']['color']['rgb'] = 'FFFFFF';

            $styleCurrencyGranTotalDark = array_merge($styleCurrency, array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '000000')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE,
                    )
                )
            ));
            $styleCurrencyGranTotalLight = $styleCurrencyGranTotalDark;
            $styleCurrencyGranTotalLight['font']['color']['rgb'] = '000000';
            $styleCurrencyGranTotalLight['fill']['color']['rgb'] = 'FFFFFF';

            $stylePorcentajeGranTotalDark = array_merge($global_config_style_cell['style_porcent'], array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size' => 10,
                    'name' => 'Aptos',
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '000000')
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_NONE,
                    )
                )
            ));

            $stylePorcentajeGranTotalLight = $stylePorcentajeGranTotalDark;
            $stylePorcentajeGranTotalLight['font']['color']['rgb'] = '000000';
            $stylePorcentajeGranTotalLight['fill']['color']['rgb'] = 'FFFFFF';

            $sheet->setCellValueByColumnAndRow($col, $row, 1)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            //AREA
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['area'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            if($resultado['activo'] == 'No') {
                $styleSimpleText2['font']['color']['rgb'] = 'FFFFFF';
                $styleSimpleText2['fill']['color']['rgb'] = 'FF0000';

                $styleSimpleText2Date['font']['color']['rgb'] = 'FFFFFF';
                $styleSimpleText2Date['fill']['color']['rgb'] = 'FF0000';

                $styleCurrency['font']['color']['rgb'] = 'FFFFFF';
                $styleCurrency['fill']['color']['rgb'] = 'FF0000';
            }

            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['departamento'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NOMENCLATURA DEL PUESTO
            $explodeNombre  = explode(' ', $resultado['nombre']);
            $sheet->setCellValueByColumnAndRow($col, $row, $explodeNombre[0])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NOMBRE DEL PUESTO
            $puestoReal = in_array($resultado['area'],$areasSinCambioDePuesto) ? $resultado['puesto'] : $puestos[$resultado['puesto']];
            $sheet->setCellValueByColumnAndRow($col, $row, $puestoReal)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //FECHA DE INGRESO
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['activo'] == 'Si' ? $resultado['fecha_ingreso'] : 'VACANTE')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($resultado['activo'] == 'Si' ? $styleSimpleText2Date : $styleSimpleText2);
            $col++;

            //NOMBRE
            $nombre = trim(substr($resultado['nombre'], strlen(trim($explodeNombre[0])), strlen($resultado['nombre'])));
            $sheet->setCellValueByColumnAndRow($col, $row, $nombre)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //SUELDO
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['sueldo'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;

            //CUENTA
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['cuenta'] ?? '' )
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //EXTENSION
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['extension'] ?? '' )
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //EMAIL
            $sheet->setCellValueByColumnAndRow($col, $row, strlen($resultado['email']) ? $resultado['email']: 'Pendiente' )
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //EMAIL GRUPO
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['mail_grupo'] ?? '' )
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //LISTA DISTRIBUCION
            $sheet->setCellValueByColumnAndRow($col, $row, $resultado['lista_distribucion'] ?? '' )
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;
            $row++;

            $colInicial = PHPExcel_Cell::stringFromColumnIndex(0).$row;
            $colFinal   = PHPExcel_Cell::stringFromColumnIndex($col-1).$row;
            if($resultados[$next]['departamento'] !== $resultado['departamento']) {

                // CANTIDAD
                $coorInicioCantSueldoVertical = PHPExcel_Cell::stringFromColumnIndex(0).$rowInicial;
                $coorFinCantSueldoVertical    = PHPExcel_Cell::stringFromColumnIndex(0).($row -1);

                $coorInicioCriterioVertical = PHPExcel_Cell::stringFromColumnIndex(2).$rowInicial;
                $coorFinCriterioVertical    = PHPExcel_Cell::stringFromColumnIndex(2).($row -1);

                $formula = "=SUMIFS({$coorInicioCantSueldoVertical}:{$coorFinCantSueldoVertical},{$coorInicioCriterioVertical}:{$coorFinCriterioVertical},\"{$resultado['departamento']}\")";
                $sheet->setCellValueByColumnAndRow(0, $row, $formula);
                $sheet->getStyle("{$colInicial}:{$colFinal}")->applyFromArray($styleSubtotalDepartamento);
                $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(0).$row)->applyFromArray($styleSubtotalDepartamento);

                // SUBTOTAL
                $sheet->setCellValueByColumnAndRow(6, $row, "SUBTOTAL ".mb_strtoupper($resultado['departamento']));
                $coorInicioSubtotalSueldoVertical = PHPExcel_Cell::stringFromColumnIndex(7).$rowInicial;
                $coorFinSubtotalSueldoVertical    = PHPExcel_Cell::stringFromColumnIndex(7).($row -1);

                $coorInicioCriterioVertical = PHPExcel_Cell::stringFromColumnIndex(2).$rowInicial;
                $coorFinCriterioVertical    = PHPExcel_Cell::stringFromColumnIndex(2).($row -1);

                $formula = "=SUMIFS({$coorInicioSubtotalSueldoVertical}:{$coorFinSubtotalSueldoVertical},{$coorInicioCriterioVertical}:{$coorFinCriterioVertical},\"{$resultado['departamento']}\")";
                $sheet->setCellValueByColumnAndRow(7, $row, $formula);
                $sheet->getStyle("{$colInicial}:{$colFinal}")->applyFromArray($styleSubtotalDepartamento);
                $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(7).$row)->applyFromArray($styleCurrencySubtotal);
                $row++;
                $rowInicial =  $row;
            }
            $next ++;
        }

        $coorInicioTotalCriterioVertical = PHPExcel_Cell::stringFromColumnIndex(6).$rowInicialGeneral;
        $coorFinTotalCriterioVertical    = PHPExcel_Cell::stringFromColumnIndex(6).($row -1);

        $colInicial      = PHPExcel_Cell::stringFromColumnIndex(0).$row;
        $colFinal        = PHPExcel_Cell::stringFromColumnIndex($col-1).$row;


        // NOMINA TOTAL
        $coorNominaTotal = PHPExcel_Cell::stringFromColumnIndex(7).($row);

        $coorInicioCanTotalVertical = PHPExcel_Cell::stringFromColumnIndex(0).$rowInicialGeneral;
        $coorFinCanTotalVertical    = PHPExcel_Cell::stringFromColumnIndex(0).($row -1);
        $formula = "=SUMIFS({$coorInicioCanTotalVertical}:{$coorFinCanTotalVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},\"*SUBTOTAL*\")";
        $sheet->setCellValueByColumnAndRow(0, $row, $formula);

        $sheet->setCellValueByColumnAndRow(5, $row, 1);
        $sheet->setCellValueByColumnAndRow(6, $row, "NOMINA COMPLETA");
        $coorInicioTotalSueldoVertical = PHPExcel_Cell::stringFromColumnIndex(7).$rowInicialGeneral;
        $coorFinTotalSueldoVertical    = PHPExcel_Cell::stringFromColumnIndex(7).($row -1);

        $formula = "=SUMIFS({$coorInicioTotalSueldoVertical}:{$coorFinTotalSueldoVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},\"*SUBTOTAL*\")";
        $sheet->setCellValueByColumnAndRow(7, $row, $formula);
        $sheet->getStyle("{$colInicial}:{$colFinal}")->applyFromArray($styleGranTotalDark);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(7).$row)->applyFromArray($styleCurrencyGranTotalDark);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(5).$row)->applyFromArray($stylePorcentajeGranTotalDark);

        // NOMINA OPERATIVA
        $coorNominaOperativa = PHPExcel_Cell::stringFromColumnIndex(7).($row+1);
        $colInicial = PHPExcel_Cell::stringFromColumnIndex(0).($row + 1);
        $colFinal   = PHPExcel_Cell::stringFromColumnIndex($col-1).($row +1);

        $sheet->setCellValueByColumnAndRow(5, ($row+1), "=$coorNominaOperativa/$coorNominaTotal");
        $sheet->setCellValueByColumnAndRow(6, ($row+1), "NOMINA OPERATIVA");
        $coorInicioTotalCriterioVertical = PHPExcel_Cell::stringFromColumnIndex(1).$rowInicialGeneral;
        $coorFinTotalCriterioVertical    = PHPExcel_Cell::stringFromColumnIndex(1).($row -1);

        $orOperativo = "";
        foreach ($areasOperativas as $operativo) {
            $orOperativo .='"'.$operativo.'",';
        }

        $orOperativo = substr($orOperativo, 0, -1);
        $formula = "=SUM(SUMIFS({$coorInicioCanTotalVertical}:{$coorFinCanTotalVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},{".$orOperativo."}))";
        $sheet->setCellValueByColumnAndRow(0, $row + 1, $formula);

        $formula = "=SUM(SUMIFS({$coorInicioTotalSueldoVertical}:{$coorFinTotalSueldoVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},{".$orOperativo."}))";
        $sheet->setCellValueByColumnAndRow(7, ($row + 1), $formula);
        $sheet->getStyle("{$colInicial}:{$colFinal}")->applyFromArray($styleGranTotalLight);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(7).($row+1))->applyFromArray($styleCurrencyGranTotalLight);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(5).($row+1))->applyFromArray($stylePorcentajeGranTotalLight);

        // NOMINA ADMINISTRATIVA
        $coorNominaAdministrativa = PHPExcel_Cell::stringFromColumnIndex(7).($row+2);
        $colInicial = PHPExcel_Cell::stringFromColumnIndex(0).($row + 2);
        $colFinal   = PHPExcel_Cell::stringFromColumnIndex($col-1).($row +2);
        $sheet->setCellValueByColumnAndRow(5, ($row+2), "=$coorNominaAdministrativa/$coorNominaTotal");
        $sheet->setCellValueByColumnAndRow(6, ($row+2), "NOMINA ADMINISTRATIVA");

        $orAdministrativa = "";
        foreach ($areasAdministrativas as $administrativa) {
            $orAdministrativa .='"'.$administrativa.'",';
        }
        $orAdministrativa = substr($orAdministrativa, 0, -1);

        $formula = "=SUM(SUMIFS({$coorInicioCanTotalVertical}:{$coorFinCanTotalVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},{".$orAdministrativa."}))";
        $sheet->setCellValueByColumnAndRow(0, $row + 2, $formula);

        $formula = "=SUM(SUMIFS({$coorInicioTotalSueldoVertical}:{$coorFinTotalSueldoVertical},{$coorInicioTotalCriterioVertical}:{$coorFinTotalCriterioVertical},{".$orAdministrativa."}))";
        $sheet->setCellValueByColumnAndRow(7, ($row + 2), $formula);
        $sheet->getStyle("{$colInicial}:{$colFinal}")->applyFromArray($styleGranTotalLight);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(7).($row+2))->applyFromArray($styleCurrencyGranTotalLight);
        $sheet->getStyle( PHPExcel_Cell::stringFromColumnIndex(5).($row+2))->applyFromArray($stylePorcentajeGranTotalLight);


        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile = "ORGANIGRAMA_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }
}
