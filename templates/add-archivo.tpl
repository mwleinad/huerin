<input type="hidden" name="contractId" id="contractId" value="{$id}" />
<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Agregar Archivo.</h1>
  </div>
  
  <div class="clear"></div>
  {include file="boxes/status_no_ajax.tpl"}       
  {if $msgOk}
   <p class="info" id="success" style="width:915px; margin-left:10px" onclick="hideMessage()">
   	<span class="info_inner">
    	{if $msgOk == 1}
    	El contrato ha sido guardado correctamente
        {else}
        El contrato ha sido actulizado correctamente
        {/if}
    </span>
   </p>
   {/if}
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="forms/add-archivo.tpl"}            
        
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