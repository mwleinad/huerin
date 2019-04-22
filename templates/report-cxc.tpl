<div class="grid_16" id="content">
  <div class="grid_9">
  <h1 class="reportes">Reporte CxC</h1>
  </div>
  <div class="grid_6" id="eventbox">
    <a style="cursor:pointer" title="Exportar a Excel" onclick="ExportReporteCxc()"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
    <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
    <div id="loadPrint">
    </div>
  </div>
  <div class="clear"></div>
  <div id="portlets">
      <div class="portlet">
           {include file="forms/search-report-cxc.tpl"}
           <br />
           <div align="center" id="loading" style="display:none">
                <img src="{$WEB_ROOT}/images/loading.gif" />
                <br />
                Cargando...
                <br />&nbsp;
           </div>
          <div class="portlet-content nopadding borderGray" id="contenido">
              {include file="lists/report-cxc.tpl"}
          </div>
          <div style="clear:both"></div>
            <br />
        </div>
  </div>
  <div class="clear"> </div>
</div>