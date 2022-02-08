<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Braun Huerin{if $titlePage neq ''} ::. {$titlePage}{/if}
</title>

<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/960.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/reset.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/text.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/blue.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/wide/grid.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/wide/override.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/jquery-ui/jquery-ui.css?{$timestamp}" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/dropzone/dropzone.css?{$timestamp}" />
{if $page == 'report-servicio-drill'}
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/plugins/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/plugins/bootstrap/css/bootstrap-theme.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/plugins/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/plugins/jstree/dist/themes/default/style.min.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/components-rounded.min.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/plugins.min.css" />
	<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/styles/custom.css" />
{/if}
<link href="{$WEB_ROOT}/assets/plugins/select2_3.5.4/select2.min.css" rel="stylesheet" />
<link href="{$WEB_ROOT}/assets/plugins/jQueryMultiSelect/jquery.multiselect.css" rel="stylesheet" />
<link type="text/css" href="{$WEB_ROOT}/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.standalone.min.css" rel="stylesheet" />
<link type="text/css" href="{$WEB_ROOT}/css/smoothness/ui.css" rel="stylesheet" />

<link rel="icon" type="image/svg+xml" href="{$WEB_ROOT}/images/icons/favicon.svg">
<link rel="icon" type="image/png" sizes="16x16" href="{$WEB_ROOT}/images/icons/favicon.png">
<link rel="mask-icon" href="{$WEB_ROOT}/images/icons/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/lista.css" />
{if $page == "login"}
	<link href="{$WEB_ROOT}/css/login.css?{$timestamp}" rel="stylesheet" type="text/css" media="all" />
{/if}
<script src="{$WEB_ROOT}/js/js-config.js?{$timestamp}" type="text/javascript"></script>
<script type="text/javascript">
		//var WEB_ROOT = "{$WEB_ROOT}/";
		var GB_ROOT_DIR = "{$WEB_ROOT}/GreyBox/greybox/";
</script>
<script type="text/javascript" src="{$WEB_ROOT}/GreyBox/greybox/AJS.js"></script>
<script type="text/javascript" src="{$WEB_ROOT}/GreyBox/greybox/AJS_fx.js"></script>
<script type="text/javascript" src="{$WEB_ROOT}/GreyBox/greybox/gb_scripts.js"></script>
<link href="{$WEB_ROOT}/GreyBox/greybox/gb_styles.css" rel="stylesheet" type="text/css" />

{if $page == "contract-new" || $page == "contract-edit" || $page == "report-obligaciones" || $page == "report-basica"}
<link type="text/css" rel="stylesheet" href="{$WEB_ROOT}/calendar/css/jscal2.css" />
<link type="text/css" rel="stylesheet" href="{$WEB_ROOT}/calendar/css/border-radius.css" />
<link id="skinhelper-compact" type="text/css" rel="alternate stylesheet" href="{$WEB_ROOT}/calendar/css/reduce-spacing.css" />
<script src="{$WEB_ROOT}/calendar/js/jscal2.js" type="text/javascript"></script>
<script src="{$WEB_ROOT}/calendar/js/unicode-letter.js" type="text/javascript"></script>
<script src="{$WEB_ROOT}/calendar/js/es.js"  type="text/javascript"></script>
{/if}

<style type="text/css">
{if $page == "docs-files" || $page == "sellado-files"
	|| $page == "services" || $page == "add-documento"
	|| $page == "add-requerimiento" || $page == "add-archivo"
	|| $page == "add-impuesto" || $page == "add-obligacion"
	|| $page == "service-steps" || $page == "responsables-resource"
	|| $page == "upkeeps-resource"}
	body { background:; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #333; }
{else}
	body { background:url({$WEB_ROOT}/images/bg.gif) repeat-x left top #e3e3e3; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #333; }
{/if}
</style>
{if $page != 'costeo-add' && $page != 'invoice-new'}
<script type="text/javascript" src="{$WEB_ROOT}/js/prototype.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/jquery/jquery-3.2.1.min.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js?{$timestamp}"></script>
<script type="text/javascript">var jQ = jQuery.noConflict()</script>
<script src="{$WEB_ROOT}/assets/plugins/select2_3.5.4/select2.min.js" type="text/javascript"></script>
<script src="{$WEB_ROOT}/js/util.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/js/functions.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/js/script-service.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/js/datetimepicker.js" type="text/javascript"></script>
{if $page eq 'prospect'}
	<script src="{$WEB_ROOT}/js/driverApi.js?{$timestamp}" type="text/javascript"></script>
{/if}
<script type="text/javascript" src="{$WEB_ROOT}/libs/sorter/js/fabtabulous.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/sorter/js/tablekit.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/dropzone/dropzone.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/bootstrap-datepicker/js/bootstrap-datepicker.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/bootstrap-datepicker/js/bootstrap-datepicker.es.min.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/jstree/dist/jstree.min.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/moment_2.18.1/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/assets/plugins/dt_1.10.25/datatables.min.css"/>
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/dt_1.10.25/datatables.min.js"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/scripts/datatable.js?{$timestamp}"></script>
{/if}

{if $section == 'consultar-facturas'}
<script src="{$WEB_ROOT}/js/consultar-facturas.js?{$timestamp}" type="text/javascript"></script>
{/if}

{if $page eq 'contract-edit' || $page eq 'contract-view' || $page eq 'workflow' || $page eq 'report-servicio-drill' || $page eq 'contract'}
	<script src="{$WEB_ROOT}/js/huerinDropzone.js?{$timestamp}" type="text/javascript"></script>
	<script src="{$WEB_ROOT}/js/add-documento.js?{$timestamp}" type="text/javascript"></script>
{/if}

<script src="{$WEB_ROOT}/js/autocomplete.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/js/pure_autocomplete.js?{$timestamp}" type="text/javascript"></script>
<script type="text/javascript" src="{$WEB_ROOT}/assets/scripts/ui-tree.js?{$timestamp}"></script>
{assign var="urlfile"  value="`$DOC_ROOT`/js/`$includedTpl`.js"}
{if is_file("`$urlfile`")}
	<script src="{$WEB_ROOT}/js/{$includedTpl}.js?{$timestamp}" type="text/javascript"></script>
{/if}
<script type="text/javascript" src="{$WEB_ROOT}/assets/plugins/alpinejs/alpine.min.js?{$timestamp}" defer></script>
</head>

<body>
{if $page == "login"}
<div style="background-color:#{($PROJECT_STATUS eq 'test') ? 'FF9800':'009900'}; color:#FFFFFF" align="center">
 {if $PROJECT_STATUS eq 'test'}Sistema en entorno de desarrollo{else}Sistema en linea{/if}
</div>
{/if}

{if $page == "docs-files"}
	{include file="templates/docs-files.tpl"}
{elseif $page == "sellado-files"}
	{include file="templates/sellado-files.tpl"}
{elseif $page == "services"}
	{include file="templates/services.tpl"}
{elseif $page == "responsables-resource"}
	{include file="templates/responsables-resource.tpl"}
{elseif $page == "upkeeps-resource"}
	{include file="templates/upkeeps-resource.tpl"}
{elseif $page == "add-documento"}
	{include file="templates/add-documento.tpl"}
{elseif $page == "add-requerimiento"}
	{include file="templates/add-requerimiento.tpl"}
{elseif $page == "add-archivo"}
	{include file="templates/add-archivo.tpl"}
{elseif $page == "add-impuesto"}
	{include file="templates/add-impuesto.tpl"}
{elseif $page == "add-obligacion"}
	{include file="templates/add-obligacion.tpl"}
{elseif $page == "service-steps"}
	{include file="templates/service-steps.tpl"}
{else}
	<div {if $page == "costeo-prvw" || $page == "costeo-html"}style="width:800px"{else}class="container_16"{/if} {if $page != "login" && $page != "product-add" && $page != "obs-add" && $page != "costeo-prvw" && $page != "costeo-html" && $page != "costeo-add"}id="wrapper"{/if}>
		{include file="header.tpl"}
		{include file="main.tpl"}
		<div class="clear"></div>
	</div>
{include file="footer.tpl"}
{/if}
</body>
</html>
