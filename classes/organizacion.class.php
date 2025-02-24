<?php

class Organizacion extends Personal
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
        $areasOperativas =  array_filter($departamentos, fn($depa) =>  !in_array(trim($depa['departamento']), $areasAdministrativas));
        $areasOperativas =  array_column($areasOperativas, 'departamento');
        $areasOperativas =  array_map(fn($item) => trim($item),$areasOperativas);

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $sheet->setTitle('organizacion');

        $headersEstatico = ['AREA', 'DEPARTAMENTO', 'NOMENCLATURA','PADRE', 'TIPO DE PUESTO', 'NIVEL', 'NOMBRE'];
        $puestos = [
            'Director' => 'Director',
            'Gerente' => 'Gerente',
            'Supervisor' => 'Supervisor',
            'Contador' => 'Encargado',
            'Contador Sr' => 'Encargado',
            'Contador Jr' => 'Encargado',
            'Encargado Jr' => 'Encargado',
            'Encargado Sr' => 'Encargado',
            'Auxiliar' => 'Encargado',
        ];
        // Todos los headers realizados.
        $row = 1;
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

        $col = 0;
        foreach ($headersEstatico as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }

        $row++;
        foreach ($resultados as $resultado) {
            $col = 0;
            switch ($resultado['puesto']) {
                case 'Director':
                    $color = '000000';
                    $bold = false;
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Gerente':
                    $color = '2E1304';
                    $bold = false;
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Subgerente':
                    $color = '833C0C';
                    $bold = false;
                    $colorFont = 'FFFFFF';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Supervisor':
                    $color = 'FFDCC1';
                    $bold = false;
                    $colorFont = '000000';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                case 'Contador Jr':
                case 'Contador Sr':
                case 'Contador':
                    $color = 'FFFFFF';
                    $bold = true;
                    $colorFont = '000000';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
                default:
                    $color = 'FFFFFF';
                    $bold = false;
                    $colorFont = '000000';
                    $typeFill = PHPExcel_Style_Fill::FILL_SOLID;
                    break;
            }

            $styleSimpleText2 = array_merge($styleSimpleText, array(
                    'font' => array(
                        'bold' => $bold,
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
                    'bold' => $bold,
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

            //AREA
            $sheet->setCellValueByColumnAndRow($col, $row, trim($resultado['area']))
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

            $sheet->setCellValueByColumnAndRow($col, $row, trim($resultado['departamento']))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NOMENCLATURA DEL PUESTO
            $explodeNombre  = explode(' ', trim($resultado['nombre']));
            $sheet->setCellValueByColumnAndRow($col, $row, $explodeNombre[0])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            $jefe = current(array_filter($resultados,function($res) use($resultado) {
                return $res['id'] === $resultado['jefe'] ;
            }));

            //JEFE
            $explodeNombreJefe  = $jefe ? explode(' ', trim($jefe['nombre'])) : null;
            $sheet->setCellValueByColumnAndRow($col, $row, $explodeNombreJefe[0] ?? '')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NOMBRE DEL PUESTO
            $puestoReal = $puestos[$resultado['puesto']];
            $sheet->setCellValueByColumnAndRow($col, $row, $puestoReal)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NIVEL
            $sheet->setCellValueByColumnAndRow($col, $row, 'Nivel 1')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
            $col++;

            //NOMBRE
            $nombre = trim(substr($resultado['nombre'], strlen(trim($explodeNombre[0])), strlen($resultado['nombre'])));
            $sheet->setCellValueByColumnAndRow($col, $row, $nombre)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText2);
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
        $nameFile = "ORGANIGRAMA_" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }
}
