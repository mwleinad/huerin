<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Clientes {$tipo}</h1>
  </div>
  
  
  
  <div class="grid_6" id="eventbox" >
	<a style="cursor:pointer" title="Exportar a Excel" onclick="printExcel('contenido')"><img src="{$WEB_ROOT}/images/excel.PNG" width="16" /></a>
	<a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  {if $User.roleId < 2 && $tipo == "Inactivos"}
      <!--<a href="javascript:void(0)" onclick="EliminarInactivos()" style="color:#FF0033">&raquo;Eliminar Inactivos&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
  {/if}    
  {if $User.roleId < 2}
      <a href="javascript:void(0)" class="inline_add" id="addCustomer">Agregar Cliente</a>
  {/if}  
	<div id="loadPrint">
  </div>  
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
  
<div id="divForm">
<form id="addCustomerFormSearch" name="addCustomerFormSearch" method="post">
			<input type="hidden" id="cliente" name="cliente" value="0"/>
			<input type="hidden" id="cuenta" name="cuenta" value="0"/>
      
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:15%;float:left; padding-left:10px">Busca por Cliente o Razon Social:</div>
				<div style="width:25%;float:left">
        	<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$customerNameSearch}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>

				</div>          
        
        <div style="float:left; padding-left:80px; margin-top:-10px">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="button_grey" id="btnAddCity" onclick="BuscarServiciosActivos()"><span>Buscar</span></a>        
        </div>
        </div>		

		</fieldset>
</form>
  
<input type="hidden" id="type" name="type" value="{$tipo}" />
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/customer.tpl"}            
        
      </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>