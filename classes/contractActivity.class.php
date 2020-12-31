<?php
class ContractActivity extends Contract {
    private $nameFile;
    public function getNameFile() {
        return $this->nameFile;
    }
    public  function getReport () {

        global $personal;
        $strFilter = "";
        if($_POST["responsableGerente"])
            $strFilter .= " and a.personalId = '".$_POST['responsableGerente']."' ";

        if($_POST["departamentoId"]) {
            $deps = [(int) $_POST['departamentoId']];
            if ((int)$_POST['departamentoId'] === 8 || (int)$_POST['departamentoId'] === 24)
                $deps = [8, 24];
            if ((int)$_POST['departamentoId'] === 22 || (int)$_POST['departamentoId'] === 21)
                $deps = [22, 21];


            $strFilter .= " and a.departamentoId IN (0," . implode(',', $deps). ") ";
        }

        $sql = "select a.*, b.nivel,c.departamento, b.name as nameRol from personal a
                inner join roles b on a.roleId = b.rolId
                inner join departamentos c on a.departamentoId = c.departamentoId where b.nivel = 2 $strFilter order by c.departamento ASC,a.name ASC";
        $this->Util()->DB()->setQuery($sql);
        $gerentes = $this->Util()->DB()->GetResult();

        $strAct = "";
        if($_POST['sector'])
            $strAct .= " and c.id=".$_POST['sector'];
        if($_POST['subsector'])
            $strAct .= " and b.id=".$_POST['subsector'];
        if($_POST['actividad_comercial'])
            $strAct .= " and a.id=".$_POST['actividad_comercial'];


        $sql = "select a.id, a.name as actividad, b.id as subsector_id, b.name as subsector, c.id as sector_id, c.name as sector from actividad_comercial a
                inner join subsector b on a.subsector_id = b.id
                inner join sector c on b.sector_id = c.id where 1 " . $strAct;
        $this->Util()->DB()->setQuery($sql);
        $actividades =  $this->Util()->DB()->GetResult();
        $actividades = $this->Util()->changeKeyArray($actividades, 'id');
        $actividades_lineal = array_column($actividades, 'id');
        $subqueryFormat = "select contract.contractId, contract.name, contract.actividadComercialId,
                            CONCAT('[',
                                GROUP_CONCAT(
                                    CONCAT(
                                        '{\"departamentoId',
                                        '\":\"',
                                        contractPermiso.departamentoId,
                                        '\",\"',
                                        'personalId',
                                        '\":\"',
                                        contractPermiso.personalId,
                                        '\"}'
                                    )
                                ),
                                ']'      
                                ) AS encargados
                           from contract
                           inner join customer on contract.customerId=customer.customerId
                           inner join contractPermiso on contract.contractId = contractPermiso.contractId
                           where contractPermiso.personalId in(%s) and contract.activo = 'Si' and customer.active ='1' 
                           group by contractPermiso.contractId
                           ";
        $new_array = [];
        $totales = [];
        $sectores = [];
        $id_sector = [];
        foreach($gerentes as $key => $value) {
            $personal->setPersonalId($value['personalId']);
            $subordinados = $personal->GetCascadeSubordinates();

            $subordinadosId = count($subordinados) > 0 ? array_column($subordinados, 'personalId') : [];
            $subString = "0".implode(',', $subordinadosId);
            $subquery = sprintf($subqueryFormat, $subString);

            $this->Util()->DB()->setQuery($subquery);
            $contracts = $this->Util()->DB()->GetResult();
            $currentDepartamentGerente = $value['departamentId'];
            $companies =  [];
            $supervisores = [];
            foreach($contracts as $con) {
                $encargados =  json_decode($con['encargados'], true);
                $keyId = array_search($currentDepartamentGerente, array_column($encargados, 'departamentoId'));
                $encar_dep_id =  $keyId >= 0  ? $encargados[$keyId]['personalId'] : $value['personalId'];

                $supervisor = $personal->findSupervisor($encar_dep_id, true);
                $supervisorId = $supervisor['personalId'];

                if($_POST['responsableSupervisor'] && $_POST['responsableSupervisor'] != $value['personalId']) {
                    if( $supervisorId != $_POST['responsableSupervisor'])
                        continue;
                }

                if(!in_array($con['actividadComercialId'], $actividades_lineal))
                    continue;

                $con['actividad'] = $actividades[$con['actividadComercialId']];
                $con['supervisor'] = $supervisor['name'];
                $con['supervisorId'] = $supervisorId;
                $current_sector_id =  $con['actividad']['sector_id'];

                if (!in_array($current_sector_id, $id_sector)) {
                    $cad_sector['sector_id'] = $current_sector_id;
                    $cad_sector['sector_name'] =  $con['actividad']['sector'];
                    array_push($id_sector, $current_sector_id);
                    $sectores[$current_sector_id] =  $cad_sector;
                }
                if(!key_exists($supervisorId, $supervisores)) {
                    $supervisores[$supervisorId] = $supervisor;
                    $supervisores[$supervisorId]['sectores'] = [];
                }

                if(!key_exists($current_sector_id, $supervisores[$supervisorId]['sectores'])) {
                    $tot_sec['sector'] =  $con['actividad']['sector'];
                    $tot_sec['total'] = 1;
                    $supervisores[$supervisorId]['sectores'][$current_sector_id] =  $tot_sec;
                } else {
                    $supervisores[$supervisorId]['sectores'][$current_sector_id]['total'] +=1;
                }

                array_push($companies, $con);
            }
            if(!count($companies))
                continue;

            $companies = $this->Util()->orderMultiDimensionalArray($companies, 'supervisor');
            $supervisores = $this->Util()->orderMultiDimensionalArray($supervisores, 'name');
            $cad = $value;
            $cad['companies'] = $companies;
            $cad2 = $value;
            $cad2['supervisores'] = $supervisores;
            array_push($totales, $cad2);
            array_push($new_array, $cad);
        }
        //
        $new_array = $this->Util()->orderMultiDimensionalArray($new_array, 'name');
        $totales = $this->Util()->orderMultiDimensionalArray($totales, 'name');
        foreach($totales as $keyTotal => $total) {
            foreach ($total['supervisores'] as $key => $var) {

            }
        }
        $data['registros'] =  $new_array;
        $data['totales'] = $totales;
        $data['sectores'] = $sectores;
        return $data;
    }

    public function getFileReport () {
        $results = $this->getReport();
        $stylesPorcent = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
            ],
        );
        $stylesTotalPorcent = array(
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
            ],
            'font' => array(
                'bold' => true
            ),
        );
        $stylesTotal = array(
            'font' => [
                'bold' => true,
            ],
            'numberformat' => [
                'code' => PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            ]
        );

        $book = new PHPExcel();
        $book->getProperties()->setCreator('B&H');
        $hoja = 0;
        $sheet = $book->createSheet($hoja);
        $sheet->setTitle('reporte');

        $row = 1;
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col, $row, "Gerente")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "Supervisor")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "Razon social")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "Sector")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "Subsector")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow(++$col, $row, "Actividad")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);

        $row++;
        foreach($results['registros'] as $gerente) {
            foreach($gerente['companies'] as $company) {
                $col= 0;
                $sheet->setCellValueByColumnAndRow($col, $row, $gerente['name']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $company['supervisor']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $company['name']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $company['actividad']['sector']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $company['actividad']['subsector']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $company['actividad']['actividad']);
                $row++;
            }
        }

        $col = 0;
        $row +=4;
        $sheet->setCellValueByColumnAndRow($col, $row, "Gerente")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row, "Supervisor")
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $col++;
        foreach($results['sectores'] as $sector) {
           $sheet->setCellValueByColumnAndRow($col, $row, $sector['sector_name'])
               ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
           $col++;
            $sheet->setCellValueByColumnAndRow($col, $row, '%')
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $col++;
        }
        $row++;

        $rowGranTotal = count($results['totales']);
        foreach ($results['totales'] as $total) {
            $rowGranTotal += count($total['supervisores']);
        }
        $initRowGranTotal =  $row;
        $rowGranTotal = $rowGranTotal + $row;
        $exclude_col = [];
        foreach ($results['totales'] as $total) {
            $init_row = $row;
            $end_row = $row + count ($total['supervisores']);
            foreach ($total['supervisores'] as $supervisor) {
                $col=0;
                $sheet->setCellValueByColumnAndRow($col, $row, $total['name']);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row, $supervisor['name']);
                $col++;
                foreach ($results['sectores'] as $keySector => $sector) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $supervisor['sectores'][$keySector]['total']);
                    $gran_total_col = PHPExcel_Cell::stringFromColumnIndex($col).$rowGranTotal;
                    $current_col = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col, $row, "=iferror($current_col/$gran_total_col, 0)")
                        ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesPorcent);
                    $col++;
                }
                $row++;
            }
            $col=0;
            $sheet->setCellValueByColumnAndRow($col, $row, '');
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,'Total '.substr($total['name'],0,15))
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
            $col++;
            $end_row = $end_row-1;
            foreach($results['sectores'] as $sec) {
                $sum_init = PHPExcel_Cell::stringFromColumnIndex($col).$init_row;
                $sum_end = PHPExcel_Cell::stringFromColumnIndex($col).$end_row;
                $gran_total_col = PHPExcel_Cell::stringFromColumnIndex($col).$rowGranTotal;
                $current_total_col = PHPExcel_Cell::stringFromColumnIndex($col).$row;

                if(!key_exists($col, $exclude_col))
                    $exclude_col[$col]['excluidos'] = [];

                array_push($exclude_col[$col]['excluidos'], $current_total_col);

                $sheet->setCellValueByColumnAndRow($col, $row,"=sum($sum_init:$sum_end)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesTotal);
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row,"=iferror($current_total_col/$gran_total_col, 0)")
                    ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesTotalPorcent);
                $col++;
            }
            $row++;
        }
        $col=0;
        $endRowGranTotal = $rowGranTotal - 1;
        $sheet->setCellValueByColumnAndRow($col, $row, '');
        $col++;
        $sheet->setCellValueByColumnAndRow($col, $row,'Total')
            ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFont()->setBold(true);
        $col++;
        $end_row = $end_row-1;
        foreach($results['sectores'] as $sec) {
            $sum_init_gran_total = PHPExcel_Cell::stringFromColumnIndex($col).$initRowGranTotal;
            $sum_end_gran_total = PHPExcel_Cell::stringFromColumnIndex($col).$endRowGranTotal;
            $gran_total_col = PHPExcel_Cell::stringFromColumnIndex($col).$rowGranTotal;
            $current_total_col = PHPExcel_Cell::stringFromColumnIndex($col).$row;
            $current_excluidos =  $exclude_col[$col]['excluidos'];
            $excluir = implode('-', $current_excluidos);
            $sheet->setCellValueByColumnAndRow($col, $row,"=sum($sum_init_gran_total:$sum_end_gran_total) - $excluir")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesTotal);
            $col++;
            $sheet->setCellValueByColumnAndRow($col, $row,"=iferror($current_total_col/$gran_total_col, 0)")
                ->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$row)->applyFromArray($stylesTotalPorcent);
            $col++;
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
        $nameFile= "reporte_actividades_empresas_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        $this->nameFile=$nameFile;
    }

}
