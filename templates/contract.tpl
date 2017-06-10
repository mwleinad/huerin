<input type="hidden" name="customerId" id="customerId" value="{$infoCustomer.customerId}"/>
<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="content_edit">Razones Sociales 
  {if $infoCustomer}
  	para {$infoCustomer.nameContact}
   {else}
  	por Cliente.
   {/if} </h1>
  </div>
  
  {if $User.roleId == 1 || $User.roleId == 2 && $infoCustomer}
  <div class="grid_9" id="eventbox">
      {if $infoUser.tipoPersonal != "Gerente" || $infoUser.tipoPersonal != "Supervisor"}
      <a href="{$WEB_ROOT}/contract-new/id/{$infoCustomer.customerId}" class="inline_add">Agregar Nueva Razon Social</a> | 
      {/if}
      <a href="{$WEB_ROOT}/customer" class="backbutton">Regresar</a>
  </div>
  {/if}
   
  <div class="clear"></div>
  
  {if !$infoCustomer}
  <div align="center">
  	<form name="frmSearch" id="frmSearch">
    <input type="hidden" name="type" value="doSearch" />
  	<table width="500" align="center">
    <tr>
    	<td align="center">
        Nombre o Razon Social<br />
        <input type="text" name="name" id="name" class="smallInput" />
        </td>
    </tr>
    <tr>
    	<td align="center" colspan="4">
        <input type="button" name="btnBuscar" id="btnBuscar" value="Buscar" onclick="doSearch()" class="btnGral" />
        </td>
    </tr>
    </table>
    </form>
  </div>
  {/if}
    
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
          
          {include file="lists/contract.tpl"}            
        
      </div>
      
      	<div id="loading" align="center" style="display:none">
            <img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
  		</div>
      
    </div>

 </div>
  <div class="clear"> </div>
  <div class="grid_9">
  &nbsp;
  </div>
  <div class="grid_7 backbutton" id="eventbox">
      {if $User.roleId < 4}

      <a href="{$WEB_ROOT}/customer" >Regresar</a>
      {/if}
  </div>
</div>