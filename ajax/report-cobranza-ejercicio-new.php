<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();

switch($_POST["type"])
{


	case "search":
			// $trimestre = explode(' ',$_POST['trimestre']);
			$anio = $_POST['anio'];
			$cliente = $_POST['cliente'];
			echo "<pre>";
			 print_r($_POST);
			 exit;


			$reporteCobranzaEjercicioNew->setAnio($anio);
			$reporteCobranzaEjercicioNew->setCliente($cliente);

			// $reporteCobranzaEjercicio->setMesUno($trimestre[0]);
			// $reporteCobranzaEjercicio->setMesDos($trimestre[1]);
			// $reporteCobranzaEjercicio->setMesTres($trimestre[2]);

			// $reporteCobranzaEjercicio->setPersonalId($_POST['personalId']);
			// $reporteCobranzaEjercicio->setDepartamentoId($_POST['departamentoId']);

			// if ($_POST['personalId'] == "" || $_POST['personalId'] == 0) {
			// 	echo "<o style='color:red'>Selecciona Supervisor.....</o><br>";
			// }
			// if ($_POST['departamentoId'] == "") {
			// 	echo "<o style='color:red'>Selecciona Departamento.....</o><br>";
			// }

			// if ($_POST['personalId'] != 0 && $_POST['personalId'] != '' && $_POST['departamentoId'] != '') {
				$INFO = $reporteCobranzaEjercicioNew->EnumerateClientesCobranza();
				echo "ok[#]";
				$smarty->assign("DATOS", $INFO);
				$smarty->assign("DOC_ROOT", DOC_ROOT);
				$smarty->display(DOC_ROOT.'/templates/lists/report-cobranza-ejercicio-new.tpl');
			// }

		break;

}

?>
