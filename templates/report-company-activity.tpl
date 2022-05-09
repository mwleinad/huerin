<div class="grid_16" id="content">
    <div class="grid_9">
    	<h1 class="reportes">Reporte de empresas y actividades economicas</h1>
    </div>
    <div class="grid_6" id="eventbox">
  	</div>
	<div class="clear"></div>
    <div id="portlets">
	<div class="portlet">
        {include file="forms/search-report-company-activity.tpl"}
        <br />
        <div align="center" id="loading" style="display:none">
            <img src="{$WEB_ROOT}/images/loading.gif" />
                Cargando...
        </div>
        <div class="portlet-content nopadding borderGray" id="contenido" style="overflow:auto; max-height:550px; margin-left:10px;">
        </div>
        <div style="clear:both"></div>
	</div>
    </div>
	<div class="clear"></div>

</div>
