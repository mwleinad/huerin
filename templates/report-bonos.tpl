<div class="grid_16" id="content">
	<div class="grid_9">
		<h1 class="reportes">Reporte Bonos</h1>
	</div>
	<div class="grid_6" id="eventbox">
		<a style="cursor:pointer" title="Exportar a Excel" onclick="ExportRepServBono('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
		<a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido','pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
		<div id="loadPrint">
		</div>
	</div>
	<div class="clear"></div>
	<div id="portlets">
		<div class="clear"></div>
		<div class="portlet">
			{include file="forms/search-report-bonos.tpl"}
			<div class="portlet-content nopadding borderGray" id="contenido">
				<div style="text-align:center"><b>Este reporte puede tardar varios minutos si no eliges un cliente. Por favor sea paciente.</b></div>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>