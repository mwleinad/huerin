<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="reportes">Reporte de Servicio Mensual</h1>
  </div>
  {if in_array(107,$permissions)}
  <div class="grid_6" id="eventbox">
		  <a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
		  <a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  <div id="loadPrint">
  </div>
  </div>
  {/if}
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
       {include file="forms/search-report-servicio-mensual.tpl"}       
       
       <br />
       
       <div align="center" id="loading" style="display:none">
       		<img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
            <br />&nbsp;
       </div>
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          {include file="lists/report-servicio-mensual.tpl"}            
      </div>
      
      <div style="clear:both"></div>
        <br />
      
      {include file="{$DOC_ROOT}/templates/boxes/report-walmart-status.tpl"}
                   
    </div>
	    
 </div>
  <div class="clear"> </div>
 
</div>