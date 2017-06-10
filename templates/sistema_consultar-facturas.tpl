<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Consulta de Comprobantes. Emitidos {$totalFacturas}</h1>
  </div>
  <div class="grid_6" id="eventbox">
		  <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
		  <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  <div id="loadPrint">
  </div>
  </div>
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray">
     {include file="forms/facturas-busqueda.tpl"}
    <br />
    <div id="total">
        {include file="boxes/resumen-facturas.tpl"}
    </div>    
    <br />
    <div id="facturasListDiv">
    {include file="lists/facturas.tpl"}
    </div>

     </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>

