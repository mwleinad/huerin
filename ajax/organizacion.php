<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
include_once(DOC_ROOT.'/libs/excel/PHPExcel.php');
session_start();
$organizacion->generateReport();
$nameFile = $organizacion->getNameReport();
echo "ok[#]";
echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/$nameFile";
?>
