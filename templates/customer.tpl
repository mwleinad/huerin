<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="clientes">Clientes {$tipo}</h1>
  </div>
  
  
  
  <div class="grid_6" id="eventbox" >
	<a href="javascript:;" title="Exportar a Excel" onclick="ExportExcel()">
    	<img src="{$WEB_ROOT}/images/excel.PNG" width="16" border="0" />
    </a>
	<a style="cursor:pointer" title="Exportar a PDF" onclick="printExcel('contenido', 'pdf')"><img src="{$WEB_ROOT}/images/pdf_icon.png" width="16" /></a>
  {if $User.roleId < 2 && $tipo == "Inactivos"}
      <!--<a href="javascript:void(0)" onclick="EliminarInactivos()" style="color:#FF0033">&raquo;Eliminar Inactivos&laquo;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
  {/if}    
  {if $User.roleId <= 2}
      <a href="javascript:void(0)" class="inline_add" id="addCustomer">Agregar Cliente</a>
      <input type="hidden" id="tipoModulo" name="tipoModulo" value="{$tipo}"/>
  {/if}  
	<div id="loadPrint">   
  	</div>  
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
  
  {include file="forms/search-customer.tpl"} 
  <br />      
  {include file="boxes/loader.tpl"}
	
      <div class="portlet-content nopadding borderGray" id="contenido">
          
          {include file="lists/customer.tpl"}            
        
      </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>