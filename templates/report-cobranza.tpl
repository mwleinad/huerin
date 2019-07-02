<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="reportes">Cobranza acumulada</h1>
  </div>
  <div class="grid_6" id="eventbox">
    <a style="cursor:pointer" title="Exportar a Excel" onclick="ExportRepServBono()"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
    <div id="loadPrint">
    </div>
  </div>
  <div class="clear"></div>
  <div id="portlets">
    <div class="clear"></div>
        <div class="portlet">
          {include file="forms/search-report-cobranza.tpl"}
          <div class="portlet-content nopadding borderGray" id="contenido">
          </div>
        </div>
  </div>
  <div class="clear"> </div>
  <div style="clear:both">
  {assign var=status value=['green'=>'Pagadas','yellow'=>'Parcialmente pagado','red'=>'Sin pagos']}
  {include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl" data=$status}
</div>