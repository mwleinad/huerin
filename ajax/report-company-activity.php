<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST["type"]) {
	case 'search':

            $contractActivity = new ContractActivity();
			/*$result = $contractActivity->getReport();
			$smarty->assign("registros", $result['registros']);
            $smarty->assign("totales", $result['totales']);
            $smarty->assign("sectores", $result['sectores']);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-company-activity.tpl');*/
            $contractActivity->getFileReport();
            $nameFile = $contractActivity->getNameFile();
            echo "ok[#]";
            echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
	break;
}
