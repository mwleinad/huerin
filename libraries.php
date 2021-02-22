<?php
if(!isset($_SESSION['lang']))
{
	include_once(DOC_ROOT.'/properties/errors.es.php');
}
elseif($_SESSION['lang'] == 'es')
{
	include_once(DOC_ROOT.'/properties/errors.es.php');
}
else
{
	include_once(DOC_ROOT.'/properties/errors.es.php');
}
require 'vendor/autoload.php';

require(DOC_ROOT.'/libs/Smarty.class.php');
$smarty = new Smarty;

require(DOC_ROOT.'/libs/nusoap.php');
include_once(DOC_ROOT."/libs/qr/qrlib.php");
include_once(DOC_ROOT."/libs/arrayColumn.php");

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
include_once(DOC_ROOT.'/classes/automatic-invoice-braun.class.php');

include_once(DOC_ROOT.'/classes/excel.class.php');


include_once(DOC_ROOT.'/classes/user.class.php');
include_once(DOC_ROOT.'/classes/customer.class.php');
include_once(DOC_ROOT.'/classes/contractCategory.class.php');
include_once(DOC_ROOT.'/classes/contractSubcategory.class.php');
include_once(DOC_ROOT.'/classes/documentBasic.class.php');
include_once(DOC_ROOT.'/classes/report-bonos.class.php');
include_once(DOC_ROOT."/classes/edoResultado.class.php");
include_once(DOC_ROOT.'/classes/report-cobranza-ejercicio.class.php');
include_once(DOC_ROOT.'/classes/report-cobranza-ejercicio-new.class.php');
include_once(DOC_ROOT.'/classes/documentSellado.class.php');
include_once(DOC_ROOT.'/classes/documentGeneral.class.php');
include_once(DOC_ROOT.'/classes/contract.class.php');
include_once(DOC_ROOT.'/classes/state.class.php');
include_once(DOC_ROOT.'/classes/city.class.php');
include_once(DOC_ROOT.'/classes/personal.class.php');
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

if(@$_GET['page'] == 'add-payment') {
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
include_once(DOC_ROOT."/classes/utileriaInvoice.class.php");
include_once(DOC_ROOT."/classes/articulo.class.php");
include_once(DOC_ROOT."/classes/inventory.class.php");
include_once(DOC_ROOT."/classes/prospect.class.php");
include_once(DOC_ROOT."/classes/company.class.php");
$company = new Company;
include_once(DOC_ROOT."/classes/prospectOffer.class.php");
$prospect = new Prospect;
include_once(DOC_ROOT."/classes/activity.class.php");
$activity = new Activity;
include_once(DOC_ROOT."/classes/accountReport.class.php");
$accountReport = new AccountReport();
include_once(DOC_ROOT."/classes/layout.class.php");
//crons
include_once(DOC_ROOT."/classes/cronServicio.class.php");
$cronServicio = new CronServicio();
include_once(DOC_ROOT."/classes/backup.class.php");
$backup = new Backup();
include_once(DOC_ROOT."/classes/huerinGraph.class.php");

include_once(DOC_ROOT."/classes/dataGraph.class.php");
$dataGraph = new DataGraph();

include_once(DOC_ROOT."/classes/reportService.class.php");
include_once(DOC_ROOT."/classes/contractActivity.class.php");

$db = new DB;
$dbRemote = new DBRemote;
$error = new CustomError;
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
$accionista = new Accionista;
$sociedad = new Sociedad;
$regimen = new Regimen;
$tipoServicio = new TipoServicio;
$tipoDocumento = new TipoDocumento;
$tipoRequerimiento = new TipoRequerimiento;
$tipoArchivo = new TipoArchivo;
$departamentos = new Departamentos;
$reportebonos = new ReporteBonos;
$edoResultado = new EdoResultado();
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
$utileriaInvoice = new UtileriaInvoice();
$inventory =  new Inventory;
$permiso = new Permiso;

//services
include_once(DOC_ROOT."/services/Catalogo.php");
$catalogo = new Catalogo;
include_once(DOC_ROOT."/services/Sello.php");
$sello = new Sello;
include_once(DOC_ROOT."/services/Totales.php");
$totales = new Totales;
include_once(DOC_ROOT."/services/ComprobantePago.php");
$comprobantePago = new ComprobantePago;
include_once(DOC_ROOT."/services/CfdiUtil.php");
$cfdiUtil = new CfdiUtil;
include_once(DOC_ROOT."/services/QrService.php");
$qrService = new QrService;
include_once(DOC_ROOT."/services/PdfService.php");
$pdfService = new PdfService;
include_once(DOC_ROOT."/services/XmlReaderService.php");
$xmlReaderService = new XmlReaderService;
include_once(DOC_ROOT."/services/invoice/invoice.php");
$invoiceService = new InvoiceService;
include_once(DOC_ROOT."/services/ControlFromXml.php");
$controlFromXml = new ControlFromXml;
include_once(DOC_ROOT."/services/Cancelation.php");
$cancelation = new Cancelation;

$lang = $util->ReturnLang();
$User = $_SESSION['User'];
$infoUser = $user->Info();
$User['tipoPersonal'] =  $infoUser['tipoPersonal'];
$User['tipoPers'] = $infoUser['tipoPersonal'];

if($User['isRoot']) {
    $rol->setAdmin(1);
} else {
    $rol->setRolId($infoUser['roleId']);
    $row = $rol->Info();
    $User['departamentoId'] = $infoUser['departamentoId'] > 0 ? $infoUser['departamentoId'] : $row['departamentoId'];
}

$rol->setRolId($User['roleId']);
$permissions = $rol->GetPermisosByRol();
$rol->setRolId($User['roleId']);
$firstPages = $rol->FindFirstPage();
$firstDep = $departamentos->GetFirstDep();

$smarty->assign('DOC_ROOT',DOC_ROOT);
$smarty->assign('WEB_ROOT',WEB_ROOT);
$smarty->assign('property', $property);
$smarty->assign('infoUser', $infoUser);
$smarty->assign('permissions', $permissions);
$smarty->assign('firstPages', $firstPages);
$smarty->assign('User',$User);
$smarty->assign("firstDep", $firstDep);
$smarty->assign("PROJECT_STATUS", PROJECT_STATUS);

function dd($data) {
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
?>
