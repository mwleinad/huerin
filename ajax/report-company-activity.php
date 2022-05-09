<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
switch($_POST["type"]) {
	case 'search':

            $contractActivity = new ContractActivity();
            $contractActivity->getFileReport();
            $nameFile = $contractActivity->getNameFile();
            echo "ok[#]";
            echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
	break;
}
