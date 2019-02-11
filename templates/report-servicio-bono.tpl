<div class="grid_16" id="content">

	<div class="grid_9">
		<h1 class="reportes">Reporte de Bonos</h1>
	</div>
	<div class="grid_6" id="eventbox">
        {if in_array(164,$permissions) || $User.isRoot}
		<!-- <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a> -->
		<a href="javascript:;" onclick="ExportRepServBono(); return false;">
			<img src="{$WEB_ROOT}/images/icons/excel.png"  title="Exportar Reporte" width="16"/>
		</a>
		<a href="javascript:;"  style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
		<div id="loadPrint">
		</div>
		{/if}
	</div>


	<div class="clear">
	</div>

	<div id="portlets">

		<div class="clear"></div>

		<div class="portlet">

			{include file="forms/search-report-servicio-bono.tpl"}

			<br />

			<div align="center" id="loading" style="display:none">
				<img src="{$WEB_ROOT}/images/loading.gif" />
				<br />
				Cargando...
				<br />&nbsp;
			</div>

			<div class="portlet-content nopadding borderGray" id="contenido">
				<div style="text-align:center"><b>Este reporte puede tardar varios minutos si no eliges un cliente. Por favor sea paciente.</b></div>
				{include file="lists/report-servicio-bono.tpl"}
			</div>

			<div style="clear:both"></div>
			<br />

			{include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl"}

		</div>

	</div>
	<div class="clear"> </div>

</div>