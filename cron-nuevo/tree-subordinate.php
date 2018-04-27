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
    switch($needle){
        case 'Socio':
              $cad=$value;
        break;
        case 'Gerente':
            $cad=$value;
            $personal->setPersonalId($value['jefeInmediato']);
            $cad['jefeMax'] = $personal->GetNameById();
        break;
        case 'Sistemas':
        case 'Gestoria':
        case 'Supervisor':
            $cad = $value;
            $gerenteId = $value['jefeInmediato'] == 0 ? $value['personalId'] : $value['jefeInmediato'];
            $personal->setPersonalId($gerenteId);
            $jGer = $personal->Info();

            $role = $rol->getInfoByData($jGer);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];

            if($needle=='Gerente'){//||$needle=='Asistente'
                $personal->setPersonalId($jGer['personalId']);
                $cad['gerente'] = $personal->GetNameById();
            }else
               $jGer['jefeInmediato']=$gerenteId;

            $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
            $personal->setPersonalId($jefeId);
            $cad['jefeMax'] = $personal->GetNameById();
        break;
        case 'Asistente':
        case 'Cuentas':
        case 'Contador':
            $cad = $value;
            $supervisorId = $value['jefeInmediato'] == 0 ? $value['personalId'] : $value['jefeInmediato'];
            $personal->setPersonalId($supervisorId);
            $jSup = $personal->Info();
            $role = $rol->getInfoByData($jSup);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];
            if($needle=='Supervisor'){
                $personal->setPersonalId($jSup['personalId']);
                $cad['supervisor'] = $personal->GetNameById();
            }else
                $jSup['jefeInmediato']=$supervisorId;

            $gerenteId = $jSup['jefeInmediato'] == 0 ? $jSup['personalId'] : $jSup['jefeInmediato'];
            $personal->setPersonalId($gerenteId);
            $jGer = $personal->Info();
            $role = $rol->getInfoByData($jGer);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];
            if($needle=='Gerente' || $needle=='Asistente'){
                $personal->setPersonalId($jGer['personalId']);
                $cad['gerente'] = $personal->GetNameById();
            }else
                $jGer['jefeInmediato']=$gerenteId;

            $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
            $personal->setPersonalId($jefeId);
            $cad['jefeMax'] = $personal->GetNameById();
        break;
        case 'Recepcion':
        case 'Auxiliar':
            $cad = $value;
            $contadorId = $value['jefeInmediato'] == 0 ? $value['personalId'] : $value['jefeInmediato'];
            $personal->setPersonalId($contadorId);
            $jCont = $personal->Info();
            $role = $rol->getInfoByData($jCont);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];
            if($needle=='Contador' || $needle=='Auxiliar'){
                $personal->setPersonalId($jCont['personalId']);
                $cad['contador'] = $personal->GetNameById();
            }else
                $jCont['jefeInmediato']=$contadorId;

            $supervisorId = $jCont['jefeInmediato'] == 0 ? $jCont['personalId'] : $jCont['jefeInmediato'];
            $personal->setPersonalId($supervisorId);
            $jSup = $personal->Info();
            $role = $rol->getInfoByData($jSup);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];
            if($needle=='Supervisor'){
                $personal->setPersonalId($jSup['personalId']);
                $cad['supervisor'] = $personal->GetNameById();
            }else
                $jSup['jefeInmediato']=$supervisorId;

            $gerenteId = $jSup['jefeInmediato'] == 0 ? $jSup['personalId'] : $jSup['jefeInmediato'];
            $personal->setPersonalId($gerenteId);
            $jGer = $personal->Info();
            $role = $rol->getInfoByData($jGer);
            $rolArray = explode(' ',$role['name']);
            $needle = $rolArray[0];
            if($needle=='Gerente' ||$needle=='Asistente'){
                $personal->setPersonalId($jGer['personalId']);
                $cad['gerente'] = $personal->GetNameById();
            }else
                $jGer['jefeInmediato']=$gerenteId;

            $jefeId = $jGer['jefeInmediato'] == 0 ? $jGer['personalId'] : $jGer['jefeInmediato'];
            $personal->setPersonalId($jefeId);
            $cad['jefeMax'] = $personal->GetNameById();
        break;
    }
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





