<?php
include_once('init.php');
include_once('config.php');
include_once(DOC_ROOT.'/libraries.php');
	$User['tipoPersonal'] = $infoUser['tipoPersonal'];
	$pages = array(
		//login,homepage
		'login',
		'logout',
		'homepage',
		//catalogos
        'personal',
        'state',
        'city',
        'rol',
        'regimen',
        'sociedad',
        'tipoServicio',
        'tipoDocumento',
        'tipoRequerimiento',
        'tipoArchivo',
        'expediente',
        'impuesto',
        'obligacion',
        'departamentos',
		'resource-office',
		'resource-office-pdf',
		'responsables-resource',
		'upkeeps-resource',
		'activity',
		'question-service',

		//clientes y contratos
		'customer',
        'exp-imp-data',
		'contract-category',
		'contract-subcategory',
		'document-basic',
		'document-general',
		'document-sellado',
		'contract',
        'contract-customer',
		'contract-new',
		'contract-edit',
		'contract-view',
		'contract-docs',
        'prospect',
        'company',
        'prospect-offer-pdf',

        //Servicios
        'servicios',
        'servicios-cliente',
        'report-servicio',
        'report-servicio-drill',
        'workflow',
        'workflow-cliente',

		//CXC
        'cxc',
        'add-payment',
        'balance',

        //facturacion
        'sistema',
		'cfdi33-generate',//cfdi 3.3
		'cfdi33-generate-pdf',//cfdi 3.3
        'comp-from-xml',//cfdi 3.3

		//Departamentos
        'archivos',

		//Reportes
		'report-cliente',
		'report-cobranza-new',
		'report-servicio-mensual',
		'report-servicio-auditoria',
		'report-cxc',
		'report-documentacion-permanente',
		'report-archivos-permanente',
		'report-bonos',
		'report-cobranza-mensual',
		'report-invoice',
		'log',
		'report-servicio-bono',
		'bitacora',
		'historial',
		'historialContract',
		'historialCustomer',
		'tree-subordinate',
		'report-walmart',
		'report-cobranza',
		'docs-files',
		'sellado-files',
		'view-services',
		'report-razon-social',
		'report-up-down',
        'report-pending',
		'edo-result',
		'report-exp-employe',
		'report-account-manager',
		'chart',
		'report-ab-all',
		'report-company-activity',

		//cafeteria
        'coffe',
		'vp_menu',

		//modulos para marcos
		'services',
		'add-documento',
		'add-requerimiento',
		'add-archivo',
		'add-impuesto',
		'add-obligacion',
		'service-steps',

		//modulos sin definir
		'print',
		'report-ingresos',
		'mantenimiento',

		//cfdi viejo
		'cfdi',
		'cfdiPagos',

		//Modulos para el usuario tipo cliente
        'customer-only',

		//configuracion
		 'admin-folios',
         'datos-generales',
		 'backup_system',
		 'utileria'


	);
	$page = $_GET['page'];
	if(!in_array($page, $pages))
	{
		$page = "homepage";
	}
	include_once(DOC_ROOT.'/modules/'.$page.'.php');

	$smarty->assign('page', $page);
	$smarty->assign('section', $_GET['section']);
	$smarty->assign('User',$User);
	$includedTpl =  $page;
	if($_GET['section'])
	{
		$includedTpl =  $_GET['page']."_".$_GET['section'];
	}
	$titlePage = $titlesPages[$includedTpl];
	$smarty->assign('titlePage',$titlePage);
	$smarty->assign('includedTpl', $includedTpl);
	$smarty->assign('lang', $lang);
	$smarty->assign('timestamp', time());
	$smarty->display(DOC_ROOT.'/templates/index.tpl');

?>
