<?php
class AccountReport extends Personal
{
    private $nameReport;
    public function getNameReport(){
        return $this->nameReport;
    }
    public function generateDinamicHeaders(array $dep) {
        $new = [];

        if(!is_array($dep))
          return $new;

      $strDep =  count($dep) > 0 ? implode(',', $dep) : '0';
      $query = "select nombreServicio as name,0 as total, tipoServicioId from tipoServicio 
                where departamentoId in($strDep) and status = '1'
                order by nombreServicio asc";
      $this->Util()->DB()->setQuery($query);
      $result = $this->Util()->DB()->GetResult();
      foreach ($result as $item) {
          $new[$item['tipoServicioId']] = $item;
      }
      return $new;
    }

    public function getContratosPropio ($responsable) {

        $subString = is_array($responsable) && count($responsable) > 0
            ? implode(',', $responsable)
            : $responsable;

        $queryString = "select a.contractId from contract a
                        inner join customer b on a.customerId=b.customerId 
                        inner join contractPermiso c on a.contractId = c.contractId
                        where c.personalId in(%s) and a.activo = 'Si' and b.active ='1'
                        having (select count(servicio.servicioId) 
                        from servicio
                        inner join tipoServicio on tipoServicio.tipoServicioId = servicio.tipoServicioId
                        where servicio.contractId = a.contractId and tipoServicio.status = '1' ) ";
        $query = sprintf($queryString, $subString);
        $this->Util()->DB()->setQuery($query);
        return $this->Util()->DB()->GetResult();
    }

    public function getServiciosPropio($contratos, $departamentos) {

        $stringContratos =  count($contratos) > 0 ? implode(',', array_column($contratos, 'contractId')) : '0';
        $queryFormat = "select count(servicioId) as total, a.tipoServicioId, b.name from servicio a
                        inner join (select tipoServicio.tipoServicioId, tipoServicio.departamentoId, tipoServicio.nombreServicio as name, tipoServicio.status from tipoServicio
                                  inner join departamentos on tipoServicio.departamentoId=departamentos.departamentoId
                                  ) b on a.tipoServicioId = b.tipoServicioId
                        where a.status in ('activo', 'bajaParcial') 
                        and a.contractId in(%s) and b.departamentoId in (%s) and b.status = '1' 
                        group by a.tipoServicioId order by b.name ASC";
        $query = sprintf($queryFormat, $stringContratos, $departamentos);

        $this->Util()->DB()->setQuery($query);
        $result = $this->Util()->DB()->GetResult();
        return  $this->Util()->changeKeyArray($result, 'tipoServicioId');

    }
    public function generateArray(array $filters) {
        global $customer;
        $strFilter = "";
        if($filters['responsableCuenta'])
            $strFilter .=" and a.personalId = '".$filters['responsableCuenta']."' ";

        if($filters['departamentoId'])
            $strFilter .=" and c.departamentoId = '".$filters['departamentoId']."' ";

        $sql = "select a.*, b.nivel,c.departamento,b.name as nameRol, numberAccountsAllowed from personal a 
                inner join roles b on a.roleId = b.rolId 
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();
        foreach($gerentes as $key => $value) {
            $departamentos = [(int)$value['departamentoId']];

            if((int)$value['departamentoId'] === 8)
                array_push($departamentos, 24);
            if((int)$value['departamentoId'] === 22)
                array_push($departamentos, 21);

            $dep = count($departamentos) > 0 ? implode(',', $departamentos) : '0';

            $base = $this->generateDinamicHeaders($departamentos);
            $gerentes[$key]['headers'] = $base;
            $this->setPersonalId($value['personalId']);
            $supervisores_subgerentes = $this->getSubordinadosNoDirectoByLevel([3,4]);

            $subordinadosId = [];
            array_push($subordinadosId, $value['personalId']);
            $contratos = $this->getContratosPropio($subordinadosId);
            $gerentes[$key]['totalCuentas'] = count($contratos);

            $result = $this->getServiciosPropio($contratos, $dep);
            $result = array_replace_recursive($base, $result);
            $gerentes[$key]['services'] = $result;

            $childLevel2 = [];
            foreach($supervisores_subgerentes as $ksupge => $supge) {
                $childrenSubId = [];
                array_push($childrenSubId, $supge['personalId']);
                $contratosSupge = $this->getContratosPropio($childrenSubId);
                $supge['totalCuentas'] = count($contratosSupge);

                $resultChild = $this->getServiciosPropio($contratosSupge, $dep);;
                $resultChild =  $this->Util()->changeKeyArray($resultChild, 'tipoServicioId');
                $resultChild = array_replace_recursive($base, $resultChild);
                $supge['services'] = $resultChild;

                $this->setPersonalId($supge['personalId']);
                $subordinados = $this->getSubordinadosByLevel([5,6,7,8]);
                $childLevel3 = [];
                foreach($subordinados as $sub) {
                    $contratosLevel3 = $this->getContratosPropio([$sub['personalId']]);
                    $sub['totalCuentas'] = count($contratosLevel3);

                    $resultChildLevel3 = $this->getServiciosPropio($contratosLevel3, $dep);;
                    $resultChildLevel3 =  $this->Util()->changeKeyArray($resultChildLevel3, 'tipoServicioId');
                    $resultChildLevel3 = array_replace_recursive($base, $resultChildLevel3);
                    $sub['services'] = $resultChildLevel3;

                    unset($sub['children']);
                    $childLevel3[]= $sub;
                }
                $supge['children'] = $childLevel3;
                $childLevel2[]= $supge;
            }
            $gerentes[$key]['children'] = $childLevel2;
        }
        return $gerentes;
    }

    public function generateReport(array $filters) {
        $gerentes = $this->generateArray($filters);
        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);

        $stylesHeader = array(
            'font' => array (
                'bold' => true,
            ),
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'bdb5b5']
            ]
        );

        foreach ($gerentes as $key => $gerente) {
            $granTotalCuentas = 0;
            $totalCol = [];
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $row = 1;
            $col = 0;
            $sheet->setTitle(strtoupper(substr($gerente["name"], 0, 6)));
            $sheet->setCellValueByColumnAndRow($col, $row, "Nivel")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Grupo ".$gerente['name'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Total de cuentas")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
            $col++;
            foreach($gerente['headers'] as $head) {
                $sheet->setCellValueByColumnAndRow($col, $row, ucfirst(strtolower($head['name'])))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
                $col++;
            }
            $stylesGerente = array(
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '00B04F']
                ]
            );

            $row ++;
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $gerente['nameRol'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesGerente);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $gerente['name'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesGerente);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $gerente['totalCuentas'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row);
            $col++;
            foreach ($gerente['services'] as $serv) {
                $currentCol = PHPExcel_Cell::stringFromColumnIndex($col);
                $totalCol[$currentCol] +=$serv['total'];
                $sheet->setCellValueByColumnAndRow($col, $row, $serv['total']);
                $col++;
            }

            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, 'TOTAL')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(2, $row, $gerente['totalCuentas'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$row)->getFont()->setBold(true);
            $row +=2;
            $granTotalCuentas = $gerente['totalCuentas'];
            $totalCuentas =  0;
            foreach ($gerente['children'] as $child) {
                $col = 0;
                $sheet->setCellValueByColumnAndRow($col, $row, "Nivel")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
                $sheet->setCellValueByColumnAndRow(++$col, $row, "Grupo ".$child['name'])
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
                $sheet->setCellValueByColumnAndRow(++$col, $row, "Total de cuentas")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
                $col++;
                foreach($gerente['headers'] as $head) {
                    $sheet->setCellValueByColumnAndRow($col, $row, ucfirst(strtolower($head['name'])))
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesHeader);
                    $col++;
                }
                $row++;
                $totalCuentas = $child['totalCuentas'];
                $styles = $this->getStylesByLevel((int)$child['nivel']);
                $col = 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $child['nameRol'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $child['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $child['totalCuentas'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row);
                $col++;
                foreach ($child['services'] as $servChild) {
                    $currentCol = PHPExcel_Cell::stringFromColumnIndex($col);
                    $totalCol[$currentCol] +=$servChild['total'];
                    $sheet->setCellValueByColumnAndRow($col, $row, $servChild['total']);
                    $col++;
                }
                $row++;
                foreach ($child['children'] as $level3) {
                    $totalCuentas = $totalCuentas + $level3['totalCuentas'];
                    $styles = $this->getStylesByLevel((int)$level3['nivel']);
                    $col = 0;
                    $sheet->setCellValueByColumnAndRow($col, $row, $level3['nameRol'])
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                    $sheet->setCellValueByColumnAndRow(++$col, $row, $level3['name'])
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                    $sheet->setCellValueByColumnAndRow(++$col, $row, $level3['totalCuentas'])
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row);
                    $col++;
                    foreach ($level3['services'] as $servLevel3) {
                        $currentCol = PHPExcel_Cell::stringFromColumnIndex($col);
                        $totalCol[$currentCol] +=$servLevel3['total'];
                        $sheet->setCellValueByColumnAndRow($col, $row, $servLevel3['total']);
                        $col++;
                    }
                    $row++;
                }
                $sheet->setCellValueByColumnAndRow(1, $row, 'TOTAL')
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
                $sheet->setCellValueByColumnAndRow(2, $row, $totalCuentas)
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$row)->getFont()->setBold(true);
                $granTotalCuentas = $granTotalCuentas + $totalCuentas;
                $row +=2;
            }

            // gran total suma
            $sheet->setCellValueByColumnAndRow(1, $row, 'GRAN TOTAL')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(1).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(2, $row, $granTotalCuentas)
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex(2).$row)->getFont()->setBold(true);

            $newCol  = array_reverse($totalCol, true);
            foreach ($newCol as $kt => $tot) {
                if((int) $tot === 0) {
                    $book->getSheet($hoja)->removeColumn($kt);
                }
            }
            $hoja++;
        }
        $book->setActiveSheetIndex(0);
        $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Worksheet')));
        $writer= PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        foreach ($book->getAllSheets() as $sheet1) {
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++) {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "cuentas_x_gerente_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport=$nameFile;
    }

    private function getStylesByLevel ($level) {
        switch ($level) {
            case 3: $background = '00B04F'; break;
            case 4: $background = '6cfb25'; break;
            case 5: $background = '92d050'; break;
            case 6: $background = 'c6e0b4'; break;
        }
       return $styles = array(
            'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => $background]
            ]
        );
    }
}
