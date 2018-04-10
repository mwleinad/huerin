<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Editar Razon Social para {$infoCustomer.nameContact}</h1>
  </div>
  
  <div class="grid_6 backbutton" id="eventbox">
		<a href="{$WEB_ROOT}/contract/id/{$infoCustomer.customerId}" >&raquo; Regresar</a>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">

  {if $msgOk}
   <p class="info" id="success" style="width:98%; margin-left:10px" onclick="hideMessage()">
   	<span class="info_inner">
    	{if $msgOk == 1}
        La Razon Social ha sido editada satisfactoriamente
      {/if}
    </span>
   </p>
   {/if}
     
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="forms/edit-contract-new.tpl"}            
        
      </div>
      
        <div style="clear:both"></div>
        
        <div class="divBotones" align="center">
        	<div id="divLoading">
                <img src="{$WEB_ROOT}/images/loading.gif" />
                <br />Guardando...
            </div>
            {if in_array(64,$permissions)||$User.isRoot}
            <div class="btnSave" onclick="VerifyForm()"></div>
            {/if}
        </div>
      
    </div>

 </div>
  <div class="clear"> </div>
  <div class="grid_9">
  &nbsp;
  </div>
  <div class="grid_7 backbutton" id="eventbox">
      <a href="{$WEB_ROOT}/customer" >Regresar</a>
  </div>
</div>