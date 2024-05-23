<div class="grid_16" id="content">

  <div class="grid_9">
  <h1 class="catalogos">Tipos de Servicio</h1>
  </div>

  <div class="grid_6" id="eventbox">
      {if in_array(29,$permissions)}
          <a style="cursor:pointer" title="Exportar catalogo de servicio" onclick="ExportCatalogoServicio()"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
          <a style="cursor:pointer" title="Exportar matriz de servicio" onclick="ExportMatrizServicio()"><img src="{$WEB_ROOT}/images/catalogos.png" width="16" /></a>
      {/if}
      {if in_array(25,$permissions)}
          <a href="javascript:void(0)" class="inline_add spanControlService" data-type="addTipoServicio" >Agregar Servicio</a>
      {/if}
  <div id="loadPrint">
  </div>
  </div>

  <div class="clear">
  </div>

  <div id="portlets">

  <div class="clear"></div>

  <div class="portlet">

      <div class="portlet-content nopadding borderGray" id="contenido">

	{include file="lists/tipoServicio.tpl"}

      </div>

    </div>

 </div>
  <div class="clear"> </div>

</div>
