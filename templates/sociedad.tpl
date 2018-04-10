<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Tipos de Sociedad</h1>
  </div>
  
  <div class="grid_6" id="eventbox">
    {if in_array(23,$permissions)}
	<a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
	<a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
    {/if}
    {if in_array(20,$permissions)}
      <a href="javascript:void(0)" class="inline_add" id="addSociedad">Agregar Tipo de Sociedad</a>
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
          
	{include file="lists/sociedad.tpl"}
        
      </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>
