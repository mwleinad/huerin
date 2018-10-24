<?php
include_once('init.php');
include_once('config.php');
include_once(DOC_ROOT.'/libraries.php');
	if (!isset($_SESSION))
	{
	  session_start();
	}
	/*switch($infoUser["tipoPersonal"])
	{
		case "Socio": $User['roleId'] = 1; break;
		case "Gerente": $User['roleId'] = 2; break;
		case "Supervisor": $User['roleId'] = 3; break;
		case "Contador": $User['roleId'] = 3; break;
		case "Auxiliar": $User['roleId'] = 3; break;
		case "Asistente": $User['roleId'] = 1; break;
		case "Recepcion": $User['roleId'] = 1; break;
		case "Cliente": $User['roleId'] = 4; break;
		case "Nomina":
			$User['roleId'] = 1;
			$User['subRoleId'] = "Nomina";
		break;
	}*/
	$User['tipoPersonal'] = $infoUser['tipoPersonal'];
	$_SESSION['empresaId'] = IDEMPRESA;
	$pages = array(
		'login',
		'logout',
		'homepage',
		'customer',
		'rol',
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
		'state',
		'city',
		'personal',
		'report-obligaciones',
		'report-basica',
			'report-cliente',
			'report-servicio',
        	'report-servicio-drill',
			'report-cobranza-new',
			'report-servicio-mensual',
			'report-servicio-auditoria',
			'report-cxc',
			'report-documentacion-permanente',
			'report-archivos-permanente',
			'report-bonos',
			'report-cobranza-ejercicio-new',
        	'report-cobranza-mensual',
			'report-cobranza-ejercicio',
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
		'walmart',
		'view-services',

		'regimen',
		'sociedad',
		'tipoServicio',
		'tipoDocumento',
		'tipoRequerimiento',
		'tipoArchivo',
		'exp-imp-data',
		'expediente',

		'services',
		'add-documento',
		'add-requerimiento',
		'add-archivo',
		'add-impuesto',
		'add-obligacion',
		'service-steps',

		'servicios',
			'servicios-cliente',
		'workflow',
			'workflow-cliente',

		'impuesto',
		'obligacion',
		'departamentos',

		'cxc',
		'add-payment',
		'balance',

		//facturacion
		'admin-folios',
		'datos-generales',
		'sistema',

		'print',
		'report-ingresos',
		'mantenimiento',
		'archivos',
		'cfdi',
		'cfdiPagos',
		'customer-only',

		//cfdi 3.3
		'cfdi33-generate',
		'cfdi33-generate-pdf',

	);

	$page = $_GET['page'];
	if(!in_array($page, $pages))
	{
		$page = "homepage";
	}
	include_once(DOC_ROOT.'/modules/user.php');
	include_once(DOC_ROOT.'/modules/'.$page.'.php');

	$smarty->assign('page', $page);
	$smarty->assign('section', $_GET['section']);
	$smarty->assign('User',$User);

	$includedTpl =  $page;
	if($_GET['section'])
	{
		$includedTpl =  $_GET['page']."_".$_GET['section'];
	}
	$smarty->assign('includedTpl', $includedTpl);
	$smarty->assign('lang', $lang);
	$smarty->assign('timestamp', time());
	$smarty->display(DOC_ROOT.'/templates/index.tpl');

?>
