<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();include(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST["type"]) {
    case "search":
        $trimestre = explode(' ', $_POST['trimestre']);
        $anio = $_POST['anio'];
        $reportebonos->setAnio($anio);
        $reportebonos->setMesUno($trimestre[0]);
        $reportebonos->setMesDos($trimestre[1]);
        $reportebonos->setMesTres($trimestre[2]);

        $reportebonos->setPersonalId($_POST['personalId']);
        $reportebonos->setDepartamentoId($_POST['departamentoId']);

        if ($_POST['personalId'] == "" || $_POST['personalId'] == 0) {
            echo "<o style='color:red'>Selecciona Supervisor.....</o><br>";
        }
        if ($_POST['departamentoId'] == "") {
            echo "<o style='color:red'>Selecciona Departamento.....</o><br>";
        }

        if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
            $INFO = $reportebonos->DATOS_REPORTE_BONO();
            echo "ok[#]";
            $smarty->assign("DATOS", $INFO['DATOS']);
            $smarty->assign("DOC_ROOT", DOC_ROOT);
            $smarty->display(DOC_ROOT . '/templates/lists/report-bonos.tpl');
        }
        break;
    case 'searchBonos':
        $data = $reportebonos->generateReportBonosWhitLevel($_POST);
        $period = $_POST['period'];
        if ($period == "efm") {
            $monthNames = array("Ene", "Feb", "Mar");
        } elseif ($period == "amj") {
            $monthNames = array("Abr", "May", "Jun");
        } elseif ($period == "jas") {
            $monthNames = array("Jul", "Ago", "Sep");
        } elseif ($period == "ond") {
            $monthNames = array("Oct", "Nov", "Dic");
        } else {
            $monthNames = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
        }
        echo "ok[#]";
        $smarty->assign("nombreMeses", $monthNames);
        $smarty->assign("data", $data);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/report-servicio-bono-order-rol.tpl');
        break;
    case 'estadoResultado':
        $result = $reportebonos->generateEstadoResultado($_POST);
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
            $sheet->setCellValueByColumnAndRow($col, 4, $value["totalTrabajado"]);
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
            $sheet->setCellValue($colBonosAreas, "");
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
            // Iterating through all the columns //
            // The after Z column problem is solved by using numeric columns; thanks to the columnIndexFromString method
            for ($col = 0; $col < PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn()); $col++)
            {
                $sheet1->getColumnDimensionByColumn($col)->setAutoSize(true);
            }
        }
        $nameFile= "ESTADO_DE_RESULTADO_".$_SESSION["User"]["userId"].".xlsx";
        $writer->save(DOC_ROOT."/sendFiles/".$nameFile);
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
	break;

}

?>
