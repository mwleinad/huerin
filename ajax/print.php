<?php

include_once('../init_files.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

session_start();
ini_set('memory_limit','3G');
$html = $_POST["contenido"];
$html = str_replace('$','', $html);
//$html = str_replace(',','', $html);
$wrap = $_POST['type'] === 'pdf' ?  false : true;
$excel->ConvertToExcel($html, $_POST["type"],false,'exportar',$wrap,45);

if(!$_POST["type"]) {
	$_POST["type"] = "xlsx";
}
echo "ok[#]";
echo WEB_ROOT."/download.php?file=".WEB_ROOT."/exportar.".$_POST["type"];

?>
