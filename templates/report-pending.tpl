<div class="grid_16" id="content">
  <div class="grid_9">
  <h1 class="catalogos">Configuracion de formulario</h1>
  </div>
  <div class="grid_6" id="eventbox">
    <a style="cursor:pointer" title="Agregar pendiente" class="spanAll spanAddPendiente">
        <img src="{$WEB_ROOT}/images/icons/add.png"/>
    </a>
    <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
    <div id="loadPrint">
    </div>
  </div>
  <div class="clear">
  </div>
  <div id="portlets">
  <div class="clear"></div>
  <div class="portlet">
      <div class="portlet-content nopadding borderGray" id="contenido">
          {include file="{$DOC_ROOT}/templates/lists/report-pending.tpl"}
      </div>
    </div>
 </div>
  <div class="clear"> </div>
</div>