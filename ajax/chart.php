<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');


switch($_POST["type"])
{
	case 'generateChart':
		switch ($_POST['typeChart']) {
			case 'altas_bajas':
				$dataGraph->chartAltasBajas();
				break;
			case 'status_company':
				$dataGraph->chartContracts();
				break;
			case 'type_person':
				$dataGraph->chartTypePerson();
				break;
			case 'month_13':
				$dataGraph->chartMonth13();
				break;
			default:
				$dataGraph->chartAltasBajas();
				$dataGraph->chartContracts();
				$dataGraph->chartTypePerson();
				$dataGraph->chartMonth13();
				break;

		}

	 $smarty->assign('charts', $dataGraph->getData());
	 $smarty->display(DOC_ROOT.'/templates/lists/chart.tpl');
	break;
}
?>
