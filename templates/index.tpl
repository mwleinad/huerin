<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Plataforma Operativa Huerin Braun, S.C.</title>

<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/960.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/reset.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/text.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/blue.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/wide/grid.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/wide/override.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/jquery-ui/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/dropzone/dropzone.css" />
{if $page == 'report-servicio-drill'}
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/bootstrap/css/bootstrap-theme.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/tree/style.min.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/components-rounded.min.css" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/plugins.min.css" />
{/if}
<link href="{$WEB_ROOT}/libs/bootstrap-select/css/bootstrap-select.min.css"  />
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet" />
<link type="text/css" href="{$WEB_ROOT}/css/smoothness/ui.css" rel="stylesheet" />
<link rel="icon" href="{$WEB_ROOT}/css/animated_favicon.gif" type="image/gif" />
<link rel="stylesheet" type="text/css" href="{$WEB_ROOT}/css/lista.css" />


{if $page == "login"}
<link href="{$WEB_ROOT}/css/login.css" rel="stylesheet" type="text/css" media="all" />  
{/if}
<script src="{$WEB_ROOT}/javascript/js-config.js?{$timestamp}" type="text/javascript"></script>
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
<script src="{$WEB_ROOT}/calendar/js/jscal2.js"></script>
<script src="{$WEB_ROOT}/calendar/js/unicode-letter.js"></script>
<script src="{$WEB_ROOT}/calendar/js/es.js"></script>
<script src="{$WEB_ROOT}/javascript/clearbox.js"></script>
{/if}

<style type="text/css">
{if $page == "docs-files" || $page == "sellado-files" || $page == "services" || $page == "add-documento" || $page == "add-requerimiento" || $page == "add-archivo" || $page == "add-impuesto" || $page == "add-obligacion" || $page == "service-steps"}
body { background:; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #333; }
{else}
body { background:url({$WEB_ROOT}/images/bg.gif) repeat-x left top #e3e3e3; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #333; }
{/if}
</style>
    
<!--[if IE]>
<script language="javascript" type="text/javascript" src="js/flot/excanvas.pack.js"></script>
<![endif]-->
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="css/iefix.css" />
<script src="js/pngfix.js"></script>
<script>
    DD_belatedPNG.fix('#menu ul li a span span');
</script>        
<![endif]-->
{if $page != 'costeo-add' && $page != 'invoice-new'}
<script type="text/javascript" src="{$WEB_ROOT}/javascript/prototype.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/jquery/jquery-3.2.1.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/jquery/jquery-ui.js?{$timestamp}"></script>
<script>var jQ = jQuery.noConflict()</script>
<script src="{$WEB_ROOT}/javascript/util.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/javascript/functions.js?{$timestamp}" type="text/javascript"></script>
<script src="{$WEB_ROOT}/javascript/{$page}.js?{$timestamp}" type="text/javascript"></script>
{/if}

{if $section == 'consultar-facturas'}
<script src="{$WEB_ROOT}/javascript/consultar-facturas.js?{$timestamp}" type="text/javascript"></script>
{/if}
<script src="{$WEB_ROOT}/javascript/datetimepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/sorter/js/fabtabulous.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/sorter/js/tablekit.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/dropzone/dropzone.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/bootstrap-datepicker/js/bootstrap-datepicker.js?{$timestamp}"></script>
<script type="text/javascript" src="{$WEB_ROOT}/libs/bootstrap-datepicker/js/bootstrap-datepicker.es.min.js?{$timestamp}"></script>

<script>
   /* var datepicker = jQ.fn.datepicker.noConflict(); // return $.fn.datepicker to previously assigned value
    jQ.fn.bootstrapDP = datepicker;*/
</script>
</head>

<body>
{if $page == "login"}
<div style="background-color:#009900; color:#FFFFFF" align="center">
Sistema ejecutandose desde nuevo servidor
</div>
{/if}

{if $page == "docs-files"}
	{include file="templates/docs-files.tpl"} 
{elseif $page == "sellado-files"}
	{include file="templates/sellado-files.tpl"} 
{elseif $page == "services"}
	{include file="templates/services.tpl"} 
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