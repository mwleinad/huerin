<?php
//$empresa->AuthUser();

//$info = $empresa->Info();
//$smarty->assign("info", $info);
switch($_SERVER['HTTP_HOST'])
{
    case "localhost": //Configuracion Local

        if(strpos($_SERVER['REQUEST_URI'],'huerin_test')){
            $webRoot = "http://".$_SERVER['HTTP_HOST']."/huerin_test";
            $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
            $sqlUser = "root";
            $sqlPw = "admonavanti";
            $sqlHost = "52.7.45.195:63306";
            $projectStatus = "test";
            $servicioContabilidad = 1;
        }else{
            $webRoot = "http://".$_SERVER['HTTP_HOST'];
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            $sqlPw = "root";
            $sqlHost = "localhost";
            $projectStatus = "produccion";
            $servicioContabilidad = 2;
        }

        break;

    case "52.7.45.195": //Server de Pruebas
        $webRoot = "http://".$_SERVER['HTTP_HOST']."/huerin_test";
        $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
        $sqlUser = "root";
        $sqlPw = "admonavanti";
        $sqlHost = "52.7.45.195:63306";
        $projectStatus = "test";
        $servicioContabilidad = 1;
        break;

    default:         //Server de Produccion
        $webRoot = "http://".$_SERVER['HTTP_HOST'];
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $sqlPw = "root";
        $sqlHost = "localhost";
        $projectStatus = "produccion";
        $repStatus = "produccion";
        $emailDev = "isc061990@outlook.com";
        $servicioContabilidad = 2;
        break;
}
echo $webRoot."<br>";
echo $docRoot;
exit;

if(!$_GET['filename']) {
  echo "No hay nombre de archivo";
  print_r($_GET);
  exit;
}

$docRoot = $_SERVER['DOCUMENT_ROOT'];
define('DOC_ROOT', $docRoot);

$webRoot = "http://".$_SERVER['HTTP_HOST'];
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$sqlPw = "root";
$sqlHost = "localhost";
$projectStatus = "produccion";
$servicioContabilidad = 2;

define('WEB_ROOT', $webRoot);
define('PROJECT_STATUS', $projectStatus);

define('SQL_HOST', $sqlHost);
define('SQL_DATABASE', 'huerin');
define('SQL_USER', 'root');
define('SQL_PASSWORD', $sqlPw);

date_default_timezone_set('America/Mexico_City');

//& ~E_NOTICE & ~E_STRICT
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

include_once("classes/main.class.php");
include_once("classes/empresa.class.php");
include_once("classes/rfc.class.php");
include_once("classes/sucursal.class.php");
include_once("classes/producto.class.php");

include_once("classes/comprobante.class.php");

require(DOC_ROOT.'/libs/Smarty.class.php');
include_once(DOC_ROOT."/libs/qr/qrlib.php");

include_once("services/PdfService.php");
include_once("services/QrService.php");
include_once("services/XmlReaderService.php");
include_once("services/CfdiUtil.php");
include_once("services/ComprobantePago.php");
include_once("classes/db.class.php");
include_once("classes/error.class.php");
include_once("classes/util.class.php");

include_once("classes/CNumeroaLetra.class.php");

$pdfService = new PdfService;

$pdfService->generate(21, $_GET['filename'], $_GET['type']);