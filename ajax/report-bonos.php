<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
session_start();
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
        $_POST['deep'] = 1;
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
        switch($_POST["tipoReporte"]){
            case 'detallado':
                $edoResultado->generateDetailedReport($_POST);
            break;
            default:
                $consolidado2023->generateReport();
            break;
        }

        $nameFile = $consolidado2023->getNameReport();
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
	break;
    case 'accountByManager':
        $accountReport->generarReporteV2($_POST);
        $nameFile = $accountReport->getNameReport();
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
        break;
    case 'generateBono':
        $_POST['deep'] = 1;
        $bono->generateReport();
        $nameFile = $bono->getNameReport();
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
        break;

    case 'generateBonoConsolidado':
        $_POST['deep'] = 1;
        $bonoConcentrado->generateReport();
        $nameFile = $bonoConcentrado->getNameReport();
        echo "ok[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
        break;

}
?>
