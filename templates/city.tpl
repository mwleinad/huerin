<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">{$nomState} - Municipios</h1>
  </div>
  
  <div class="grid_6" id="eventbox">
      <a href="javascript:void(0)" class="inline_add" id="addCity">Agregar Municipio</a>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/city.tpl"}            
        
      </div>
    </div>
    
    <div style="clear:both"></div>
    
    <div align="center">
    	<a href="{$WEB_ROOT}/state">Regresar</a>
    </div>

 </div>
  <div class="clear"> </div>

</div>