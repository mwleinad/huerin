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
    public function generateArray(array $filters) {
        $strFilter = "";
        if($filters['responsableCuenta'])
            $strFilter .=" and a.personalId = '".$filters['responsableCuenta']."' ";

        if($filters['departamentoId'])
            $strFilter .=" and c.departamentoId = '".$filters['departamentoId']."' ";

        $sql = "select a.*, b.nivel,c.departamento,b.name as nameRol from personal a 
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

            $base = $this->generateDinamicHeaders($departamentos);
            $gerentes[$key]['headers'] = $base;
            $this->setPersonalId($value['personalId']);
            $subordinados = $this->GetCascadeSubordinates();
            $subordinadosId = [];
            array_push($subordinadosId, $value['personalId']);

            $subString = count($subordinadosId) > 0 ? implode(',', $subordinadosId):  "0";

            $dep = count($departamentos) > 0 ? implode(',', $departamentos) : '0';

            $subqueryFormat = "select contract.contractId from contract 
                               inner join customer on contract.customerId=customer.customerId 
                               inner join contractPermiso on contract.contractId = contractPermiso.contractId
                               where contractPermiso.personalId in(%s) and contract.activo = 'Si' and customer.active ='1' ";
            $subquery = sprintf($subqueryFormat, $subString);

            $this->Util()->DB()->setQuery($subquery);
            $contratos = $this->Util()->DB()->GetResult();
            $gerentes[$key]['totalCuentas'] = count($contratos);

            $stringContratos =  count($contratos) > 0 ? implode(',', array_column($contratos, 'contractId')) : '0';

            $queryFormat = "select count(servicioId) as total, a.tipoServicioId, b.name from servicio a
                            inner join (select tipoServicio.tipoServicioId, tipoServicio.departamentoId, tipoServicio.nombreServicio as name, tipoServicio.status from tipoServicio
                                  inner join departamentos on tipoServicio.departamentoId=departamentos.departamentoId
                                  ) b on a.tipoServicioId = b.tipoServicioId
                            where a.status in ('activo', 'bajaParcial') and a.contractId in(%s) and b.departamentoId in (%s) and b.status = '1' group by a.tipoServicioId order by b.name ASC";
            $query = sprintf($queryFormat, $stringContratos, $dep);

            $this->Util()->DB()->setQuery($query);
            $result = $this->Util()->DB()->GetResult();
            $result =  $this->Util()->changeKeyArray($result, 'tipoServicioId');
            $result = array_replace_recursive($base, $result);
            $gerentes[$key]['services'] = $result;
            $childs = [];
            foreach($subordinados as $sub) {
                $childrenSubId = [];
                array_push($childrenSubId, $sub['personalId']);
                $subStringChild = count($childrenSubId) > 0 ? implode(',', $childrenSubId) : "0";
                $subQueryChild = sprintf($subqueryFormat, $subStringChild);
                $this->Util()->DB()->setQuery($subQueryChild);
                $contratos = $this->Util()->DB()->GetResult();
                $sub['totalCuentas'] = count($contratos);
                $stringContratos =  count($contratos) > 0 ? implode(',', array_column($contratos, 'contractId')) : '0';
                $queryChild = sprintf($queryFormat, $stringContratos, $dep);
                $this->Util()->DB()->setQuery($queryChild);
                $resultChild = $this->Util()->DB()->GetResult();
                $resultChild =  $this->Util()->changeKeyArray($resultChild, 'tipoServicioId');
                $resultChild = array_replace_recursive($base, $resultChild);
                $sub['services'] = $resultChild;
                unset($sub['children']);
                $childs[]= $sub;
            }
            $gerentes[$key]['children'] = $childs;
        }
        return $gerentes;
    }
    public function generateReport(array $filters) {
        $gerentes = $this->generateArray($filters);
        $book = new PHPExcel();
        PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        foreach ($gerentes as $key => $gerente) {
            if ($hoja != 0)
                $sheet = $book->createSheet($hoja);
            $row = 1;
            $col = 0;
            $sheet->setTitle(strtoupper(substr($gerente["name"], 0, 6)));
            $sheet->setCellValueByColumnAndRow($col, $row, "Cargo")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Nombre")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Cuentas deben tener")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(++$col, $row, "Cuentas tienen")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $col++;
            foreach($gerente['headers'] as $head) {
                $sheet->setCellValueByColumnAndRow($col, $row, ucfirst(strtolower($head['name'])))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
                $col++;
            }
            $stylesGerente = array(
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => '00B04F']
                ]
            );

            $row++;
            $col = 0;
            $sheet->setCellValueByColumnAndRow($col, $row, $gerente['nameRol'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesGerente);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $gerente['name'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesGerente);
            $sheet->setCellValueByColumnAndRow(++$col, $row, 0);
            $sheet->setCellValueByColumnAndRow(++$col, $row, $gerente['totalCuentas'])
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);;
            $col++;
            foreach ($gerente['services'] as $serv) {
                $sheet->setCellValueByColumnAndRow($col, $row, $serv['total']);
                $col++;
            }

            $row++;
            foreach ($gerente['children'] as $child) {
                switch ((int)$child['nivel']) {
                    case 3: $background = '00B04F'; break;
                    case 4: $background = '6cfb25'; break;
                    case 5: $background = '92d050'; break;
                    case 6: $background = 'c6e0b4'; break;
                }
                $styles = array(
                    'fill' => [
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => ['rgb' => $background]
                    ]
                );
                $col = 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $child['nameRol'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $child['name'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($styles);
                $sheet->setCellValueByColumnAndRow(++$col, $row, 0);
                $sheet->setCellValueByColumnAndRow(++$col, $row, $child['totalCuentas'])
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
                $col++;
                foreach ($child['services'] as $servChild) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $servChild['total']);
                    $col++;
                }
                $row++;
            }
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
        $nameFile= "cuentas_x_gerente_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameReport=$nameFile;
    }
}
