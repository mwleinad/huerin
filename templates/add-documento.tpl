<input type="hidden" name="contractId" id="contractId" value="{$id}" />
<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Agregar Documento.</h1>
  </div>
  
  <div class="clear"></div>
  
  {include file="boxes/status_no_ajax.tpl"}     
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
         
          {include file="forms/add-documento.tpl"}            
        
      </div>
      
      	<div id="loading" align="center" style="display:none">
            <img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
  		</div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>

<div id="fview" style="display:none;">	
      <input type="hidden" id="inputs_changed" value="0" />  	
        <div id="fviewload" style="display:block"><img src="{$WEB_ROOT}/images/load.gif" border="0" /></div>
        <div id="fviewcontent" style="display:none"></div>
        <div id="modal">
            <div id="submodal">
               
            </div>
        </div>
    </div>
    <div style="position:relative" id="divStatus"></div>