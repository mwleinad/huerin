<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 31/01/2018
 * Time: 06:57 PM
 */

ini_set('memory_limit','3G');
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
define('DOC_ROOT', $docRoot);

include_once(DOC_ROOT.'/initContent.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');
if(!isset($_SESSION['User'])){
    echo "no se puede ejecutar, favor de iniciar sesion";
    exit;
}
$sql = "SELECT * FROM personal WHERE active='1'  ORDER BY personalId  ASC";
$db->setQuery($sql);
$results = $db->GetResult();
$new = array();
foreach($results as $key => $value){
    $role = $rol->getInfoByData($value);
    $rolArray = explode(' ',$role['name']);
    $needle = trim($rolArray[0]);
    $jefes=array();
    $personal->findDeepJefes($value['personalId'],$jefes);
    $cad=$value;
    $cad['contador'] = $jefes['Contador'];
    $cad['supervisor'] = $jefes['Supervisor'];
    $cad['gerente'] = $jefes['Gerente'];
    $cad['jefeMax'] = $jefes['Socio'];
    $new[] = $cad;
}
$html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 15px "Trebuchet MS";
						font-size:15px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 14px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						vertical-align: center;
						border-collapse: collapse;
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #686DAB;
						background: #686DAB;
						color: #FFFFFF;
						vertical-align: center;
						text-align: center;
						height: 50px;
					}
				</style>
			</head>
			';
$smarty->assign("registros", $new);
$contents = $smarty->fetch(DOC_ROOT . '/templates/lists/rep-subordinado.tpl');
$html .= $contents;
$file = 'arbol-subordinados';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$file.".xls");
header("Pragma: no-cache");
echo "\xEF\xBB\xBF";
header("Expires: 0");

echo $html;
exit;
/*$excel->ConvertToExcel($html, 'xlsx', false,$file,true);
$path = DOC_ROOT . "/sendFiles/".$file.".xlsx";

header('Content-disposition: attachment; filename='.$file.'.xlsx');
header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Length: '.filesize($path));
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
ob_clean();
flush();
readfile($path);*/





