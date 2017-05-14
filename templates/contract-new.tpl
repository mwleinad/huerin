<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Agregar Nueva Razon Social para {$infoCustomer.nameContact}</h1>
  </div>
  
  <div class="grid_6" id="eventbox">
		<a href="{$WEB_ROOT}/contract/id/{$infoCustomer.customerId}" >&raquo; Regresar</a>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="forms/add-contract-new.tpl"}            
        
      </div>
      
        <div style="clear:both"></div>
        
        <div class="divBotones" align="center">
        	<div id="divLoading">
                <img src="{$WEB_ROOT}/images/loading.gif" />
                <br />Guardando...
            </div>           
            <div class="btnSave" onclick="VerifyForm()"></div>
        </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>