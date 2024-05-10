<?php

class CobranzaDetallado extends Personal
{

    private $nameReport;

    public function getNameReport()
    {
        return $this->nameReport;
    }

    public function generateData($year, $mes=0)
    {
        $sql = "call sp_get_data_reporte_cobranza_detallado(".$year.",".$mes.")";
        $this->Util()->DB()->setQuery($sql);
        $resultados = $this->Util()->DB()->GetResult();

        return $resultados;
    }

    public function generateReport($year, $mes = 0)
    {
        global $global_config_style_cell, $catalogue;

        $departamentos = $catalogue->EnumerateCatalogue('departamentos');

        $areasFiltradas = [
            'Cuentas por cobrar',
            'Atención al Cliente',
            'Atencion al Cliente',
            'Contabilidad e Impuestos',
            'Nominas',
            'Legal',
            'Fiscal',
            'Administración',
            'Administracion',
            'Auditoria',
            'Desarrollo Organizacional'
        ];

        $departamentos = array_filter($departamentos, fn($dep) => in_array($dep['departamento'], $areasFiltradas));
        $departamentos = array_values($departamentos);

        $headersEstatico = [
            'FOLIO FACTURA',
            'FECHA',
            'CLIENTE',
            'RAZÓN SOCIAL',
            'SERVICIO',
            'ÁREA',
            'SUBTOTAL',
            'IVA',
            'TOTAL',
            'PAGOS',
            'SALDO',
            'ESTATUS DEL SERVICIO'
        ];

        $styleHeaderDark = array_merge($global_config_style_cell['style_header'], array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000000')
            ),
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 9,
                'name' => 'Aptos',
            )
        ));

        $styleHeaderLight = array_merge($global_config_style_cell['style_header'], array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_NONE
            ),
            'font' => array(
                'bold' => false,
                'size' => 8,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));

        $styleSimpleText = array_merge($global_config_style_cell['style_simple_text_whit_border'], array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ],
            'font' => array(
                'size' => 8,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));

        $styleCurrency = array_merge($global_config_style_cell['style_currency'], array(
            'font' => array(
                'bold' => false,
                'size' => 8,
                'name' => 'Aptos',
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                )
            )
        ));


        $results = $this->generateData($year, $mes);
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $sheet = $book->createSheet(0);
        $sheet->setTitle('COBRANZA DETALLADO');


        $sheet->setCellValueByColumnAndRow(0, 1, 'REPORTE DETALLADO DE COBRANZA')
            ->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(0, 2, 'FECHA Y HORA DE CONSULTA: ' . date('d/m/Y H:i'))
            ->getStyle('A2')->applyFromArray($styleHeaderLight);

        $col = 0;
        $row = 4;
        foreach ($headersEstatico as $header) {
            $sheet->setCellValueByColumnAndRow($col, $row, $header)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }
        $sheet->setCellValueByColumnAndRow($col, 3, 'ENCARGADOS');

        foreach ($departamentos as $departamento) {
            $sheet->setCellValueByColumnAndRow($col, $row, mb_strtoupper($departamento['departamento']))
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleHeaderDark);
            $col++;
        }

        $row++;
        foreach ($results as $result) {
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col,$row,$result['serie'].$result['folio'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row,$result['fecha'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row,$result['cliente'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row,$result['razon_social'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row,$result['servicio'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $sheet->setCellValueByColumnAndRow($col,$row,$result['area'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
            $col++;

            $colCostoSinIva = PHPExcel_Cell::stringFromColumnIndex($col);
            $sheet->setCellValueByColumnAndRow($col,$row,$result['costo_servicio'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;

            $colIva = PHPExcel_Cell::stringFromColumnIndex($col);
            $sheet->setCellValueByColumnAndRow($col,$row, "=+".$colCostoSinIva.$row." * 0.16")
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;

            $colTotal = PHPExcel_Cell::stringFromColumnIndex($col);
            $sheet->setCellValueByColumnAndRow($col,$row, "=".$colCostoSinIva.$row."+".$colIva.$row)
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;

            $colPagos = PHPExcel_Cell::stringFromColumnIndex($col);
            $costoServicio = $result['costo_servicio'] * 1.16;
            $sheet->setCellValueByColumnAndRow($col,$row,$result['pagado'] === 'Si' ? number_format($costoServicio,2) : 0)
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row, "=".$colTotal.$row."-".$colPagos.$row)
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleCurrency);
            $col++;
            $sheet->setCellValueByColumnAndRow($col,$row, $result['estatus_servicio'])
                  ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);

            $col++;

            $responsables = json_decode($result['responsables'], true);

            foreach ($departamentos as $departamento) {
                $responsable = array_filter($responsables, fn($item) => $item['departamento'] == $departamento['departamento']);
                $responsable = array_values($responsable);
                $sheet->setCellValueByColumnAndRow($col,$row, is_array($responsable) ? $responsable[0]['nombre'] : '')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->applyFromArray($styleSimpleText);
                $col++;
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
        $nameFile = "REPORTE-COBRANZA-DETALLADO-" . $_SESSION["User"]["userId"] . ".xlsx";
        $this->nameReport = $nameFile;
        $writer->save(DOC_ROOT . "/sendFiles/" . $nameFile);
    }
}
