<?php

//language
if(!isset($_SESSION['lang']))
{
//	include_once(DOC_ROOT.'/properties/language.es.php');
	include_once(DOC_ROOT.'/properties/errors.es.php');
}
elseif($_SESSION['lang'] == 'es')
{
//	include_once(DOC_ROOT.'/properties/language.es.php');
	include_once(DOC_ROOT.'/properties/errors.es.php');
}
else
{
//	include_once(DOC_ROOT.'/properties/language.en.php');
	include_once(DOC_ROOT.'/properties/errors.es.php');
}


require 'vendor/autoload.php';

//include_once(DOC_ROOT.'/properties/config.php');
require(DOC_ROOT.'/libs/Smarty.class.php');
require(DOC_ROOT.'/libs/nusoap.php');
include_once(DOC_ROOT."/libs/qr/qrlib.php");


include_once(DOC_ROOT."/constants.php");


include_once(DOC_ROOT.'/classes/db.class.php');
include_once(DOC_ROOT.'/classes/db-remote.class.php');
include_once(DOC_ROOT.'/classes/error.class.php');
include_once(DOC_ROOT.'/classes/util.class.php');
include_once(DOC_ROOT.'/classes/main.class.php');

include_once(DOC_ROOT.'/classes/empresa.class.php');
include_once(DOC_ROOT.'/classes/rfc.class.php');
include_once(DOC_ROOT.'/classes/folios.class.php');
include_once(DOC_ROOT.'/classes/sucursal.class.php');
include_once(DOC_ROOT.'/classes/producto.class.php');
include_once(DOC_ROOT.'/classes/comprobante.class.php');
include_once(DOC_ROOT.'/classes/vista_previa.class.php');

include_once(DOC_ROOT.'/classes/cadena_original_v3.class.php');
include_once(DOC_ROOT.'/classes/generate_xml_default.class.php');
include_once(DOC_ROOT.'/classes/override_generate_pdf_default.class.php');
include_once(DOC_ROOT."/classes/CNumeroaLetra.class.php");
include_once(DOC_ROOT."/classes/pac.class.php");
include_once(DOC_ROOT.'/classes/automatic-invoice.class.php');
include_once(DOC_ROOT.'/classes/automatic-invoice-rif.class.php');
//include_once(DOC_ROOT.'/classes/month13.class.php');
include_once(DOC_ROOT.'/classes/automatic-invoice-braun.class.php');

include_once(DOC_ROOT.'/classes/excel.class.php');


include_once(DOC_ROOT.'/classes/user.class.php');
include_once(DOC_ROOT.'/classes/customer.class.php');
include_once(DOC_ROOT.'/classes/contractCategory.class.php');
include_once(DOC_ROOT.'/classes/contractSubcategory.class.php');
include_once(DOC_ROOT.'/classes/documentBasic.class.php');
include_once(DOC_ROOT.'/classes/report-bonos.class.php');
include_once(DOC_ROOT.'/classes/report-cobranza-ejercicio.class.php');
include_once(DOC_ROOT.'/classes/report-cobranza-ejercicio-new.class.php');
include_once(DOC_ROOT.'/classes/documentSellado.class.php');
include_once(DOC_ROOT.'/classes/documentGeneral.class.php');
include_once(DOC_ROOT.'/classes/contract.class.php');
include_once(DOC_ROOT.'/classes/state.class.php');
include_once(DOC_ROOT.'/classes/city.class.php');
include_once(DOC_ROOT.'/classes/personal.class.php');
include_once(DOC_ROOT.'/classes/wallmart.class.php');
include_once(DOC_ROOT.'/classes/class.phpmailer.php');
include_once(DOC_ROOT.'/classes/class.smtp.php');
include_once(DOC_ROOT.'/classes/accionista.class.php');
include_once(DOC_ROOT.'/classes/regimen.class.php');
include_once(DOC_ROOT.'/classes/sociedad.class.php');
include_once(DOC_ROOT.'/classes/servicio.class.php');
include_once(DOC_ROOT.'/classes/tipoServicio.class.php');
include_once(DOC_ROOT.'/classes/tipoDocumento.class.php');
include_once(DOC_ROOT.'/classes/tipoRequerimiento.class.php');
include_once(DOC_ROOT.'/classes/tipoArchivo.class.php');
include_once(DOC_ROOT.'/classes/departamentos.class.php');
include_once(DOC_ROOT.'/classes/documento.class.php');
include_once(DOC_ROOT.'/classes/requerimiento.class.php');
include_once(DOC_ROOT.'/classes/archivo.class.php');
include_once(DOC_ROOT.'/classes/step.class.php');
include_once(DOC_ROOT.'/classes/task.class.php');
include_once(DOC_ROOT.'/classes/workflow.class.php');
include_once(DOC_ROOT.'/classes/impuesto.class.php');
include_once(DOC_ROOT.'/classes/obligacion.class.php');
include_once(DOC_ROOT.'/classes/sendmail.class.php');
include_once(DOC_ROOT.'/pdf/dompdf_config.inc.php');

include_once(DOC_ROOT.'/classes/cxc.class.php');
include_once(DOC_ROOT.'/classes/balance.class.php');
include_once(DOC_ROOT.'/classes/log.class.php');
include_once(DOC_ROOT.'/classes/notice.class.php');
include_once(DOC_ROOT.'/classes/pendiente.class.php');

include_once(DOC_ROOT."/classes/xmlTransform.class.php");
include_once(DOC_ROOT."/classes/filtro.class.php");
include_once(DOC_ROOT."/classes/instanciaServicio.class.php");
include_once(DOC_ROOT."/classes/contractRep.class.php");
include_once(DOC_ROOT."/classes/serie.class.php");
include_once(DOC_ROOT."/classes/expediente.class.php");
include_once(DOC_ROOT."/classes/rol.class.php");
include_once(DOC_ROOT."/classes/catalogue.class.php");

if($_GET['page'] == 'add-payment') {
	include_once(DOC_ROOT."/services/Cfdi.php");
} else{
	include_once(DOC_ROOT."/classes/cfdi.class.php");

}

include_once(DOC_ROOT."/classes/archivos.class.php");
include_once(DOC_ROOT."/classes/razon.class.php");
include_once(DOC_ROOT."/classes/validar.class.php");
include_once(DOC_ROOT."/classes/CreatePdfNotification.class.php");
include_once(DOC_ROOT."/classes/permiso.class.php");
include_once(DOC_ROOT."/classes/dropzone.class.php");
include_once(DOC_ROOT."/classes/coffe.class.php");
include_once(DOC_ROOT."/classes/compressed.class.php");
include_once(DOC_ROOT."/classes/changePlatform.class.php");

//cron
include_once(DOC_ROOT."/classes/cronServicio.class.php");
include_once(DOC_ROOT."/classes/backup.class.php");

$db = new DB;
$dbRemote = new DBRemote;
$error = new Error;
$util = new Util;
$main = new Main;
$notice = new Notice;
$pendiente = new Pendiente;
$user = new User;
$customer = new Customer;
$contCat = new ContractCategory;
$contSubcat = new ContractSubcategory;
$docBasic = new DocumentBasic;
$docSellado = new DocumentSellado;
$docGral = new DocumentGeneral;
$contract = new Contract;
$state = new State;
$city = new City;
$personal = new Personal;
$wallmart = new Wallmart;
$accionista = new Accionista;
$sociedad = new Sociedad;
$regimen = new Regimen;
$tipoServicio = new TipoServicio;
$tipoDocumento = new TipoDocumento;
$tipoRequerimiento = new TipoRequerimiento;
$tipoArchivo = new TipoArchivo;
$departamentos = new Departamentos;
$reportebonos = new ReporteBonos;
$reporteCobranzaEjercicio = new ReporteCobranzaEjercicio;
$reporteCobranzaEjercicioNew = new ReporteCobranzaEjercicioNew;
$documento = new Documento;
$requerimiento = new Requerimiento;
$archivo = new Archivo;
$servicio = new Servicio;
$step = new Step;
$task = new Task;
$workflow = new Workflow;
$impuesto = new Impuesto;
$obligacion = new Obligacion;
$log = new Log;

$filtro = new Filtro;
$instanciaServicio = new InstanciaServicio;
$contractRep = new ContractRep;

$empresa = new Empresa;
$rfc = new Rfc;
$folios = new Folios;
$sucursal = new Sucursal;
$producto = new Producto;
$comprobante = new Comprobante;
$vistaPrevia = new VistaPrevia;
$cadena = new Cadena;
$xmlGen = new XmlGen;
//$override = new Override;
$pac = new Pac;
$automaticInvoice = new AutomaticInvoice;
$automaticInvoiceRif = new AutomaticInvoiceRif;
//$month13 = new Month13;
$automaticInvoiceBraun = new AutomaticInvoiceBraun;
$sendmail = new SendMail;
$excel = new Excel;

$cxc = new CxC;
$balance = new Balance;

$xmlTransform = new XmlTransform;
$cfdi = new Cfdi;

$archivos = new Archivos();
$objectSerie=  new Serie;
$expediente=  new Expediente;
$rol=  new Rol;
$valida = new Validar();
$dropzone = new Dropzone();
$catalogue = new Catalogue();
$change = new ChangePlatform();

//cron
$cronServicio = new CronServicio();
$backup = new Backup();
//echo $page;exit;
include_once(DOC_ROOT."/services/Catalogo.php");
include_once(DOC_ROOT."/services/Sello.php");
include_once(DOC_ROOT."/services/Totales.php");
include_once(DOC_ROOT."/services/ComprobantePago.php");
include_once(DOC_ROOT."/services/CfdiUtil.php");
include_once(DOC_ROOT."/services/Cancelation.php");
include_once(DOC_ROOT."/services/ControlFromXml.php");

$catalogo = new Catalogo;
$sello = new Sello;
$totales = new Totales;
$comprobantePago = new ComprobantePago;
$cfdiUtil = new CfdiUtil;
$cancelation = new Cancelation;
$controlFromXml = new ControlFromXml;

$smarty = new Smarty;
$smarty->assign('DOC_ROOT',DOC_ROOT);
$smarty->assign('WEB_ROOT',WEB_ROOT);

$smarty->assign('property', $property);

$lang = $util->ReturnLang();

$User = $_SESSION['User'];

$infoUser = $user->Info();
$smarty->assign('infoUser', $infoUser);

if($_SESSION['User']['tipoPers']=='Admin')	{
    $rol->setAdmin(1);
    $User['roleId'] = 1;
}else{
    //primero buscar por nombre el rol
    $rol->setTitulo($infoUser['tipoPersonal']);
    $roleId = $rol->GetIdByName();
     if($roleId<=0){
         //si por nombre de rol no se encuentra entonces usar el rolId que tiene en la tabla personal
         //ese nunca debe fallar aun que se cambie de nombre de nombre de el rol. cuando se haya asignado los roles a todos
         // se dejara de usar tipoPersonal salvo en unos casos que se necesite usar el tipoPers
         $rol->setRolId($infoUser['roleId']);
         $row = $rol->Info();
         $infoUser['tipoPersonal'] = $row['name'];
         $roleId=$row['rolId'];
         $User['tipoPers'] = $row['name'];
     }
    $User['roleId'] = $roleId;
     //find departamento user active
     if($infoUser['departamentoId']>0)
         $User['departamentoId']=$infoUser['departamentoId'];
     else{
         $rol->setRolId($User['roleId']);
         $dep = $rol->Info();
         $User['departamentoId']=$dep['departamentoId'];
     }
}


$rol->setRolId($User['roleId']);
$permissions = $rol->GetPermisosByRol();
$smarty->assign('permissions', $permissions);


$rol->setRolId($User['roleId']);
$firstPages = $rol->FindFirstPage();
$smarty->assign('firstPages', $firstPages);

$User['tipoPersonal'] = $infoUser['tipoPersonal'];
if($User["tipoPersonal"] == "Asistente" || $User["tipoPersonal"] == "Socio" || $User["tipoPersonal"] == "Gerente")
{
	$smarty->assign('canEdit', true);
}

$smarty->assign('User',$User);
$firstDep = $departamentos->GetFirstDep();
$smarty->assign("firstDep", $firstDep);
function dd($data)
{
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey (http://benramsey.com)
 * @license http://opensource.org/licenses/MIT MIT
 */
if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}
?>