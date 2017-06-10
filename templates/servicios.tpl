<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Servicios Activos (directos y subordinados)</h1>
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
	<div id="divForm">
        <form id="addCustomerForm" name="addCustomerForm" method="post">
        <input type="hidden" id="type" name="type" value="saveAddCustomer"/>
        <input type="hidden" id="cliente" name="cliente" value="0"/>
        <input type="hidden" id="cuenta" name="cuenta" value="0"/>
      
		<fieldset>
    	<table width="600" align="center">
        <tr style="background-color:#CCC">
            <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
        </tr>
        <tr>
            <td align="center">Busca por Cliente o Raz&oacute;n Social:</td>
            <td align="center">Responsable:</td>
            <td align="center">Incluir Subordinados:</td>
            <td align="center">Departamento:</td>
             <td align="center">A&ntilde;o:</td>               
            <td align="center"></td>
        </tr>
        <tr>
          	<td align="center">
                    <div style="width:25%;float:left">
                <input type="text" size="35" name="rfc" id="rfc" class="largeInput" value="{$customerNameSearch}" autocomplete="off" />
              <div id="loadingDivDatosFactura"></div>
                        <div style="position:relative">
                    <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                    </div>
                </div>
    
                    </div>          
          	</td>
          	<td align="center">
                <select name="responsableCuenta" id="responsableCuenta"  class="smallInput">
                {if $User.roleId=="1"}
                <option value="0" selected="selected">Todos...</option>
                {/if}
                {foreach from=$personals item=personal}
                <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
                {/foreach}
                </select>
            </td>    
        	<td align="center">
          	<select name="deep" id="deep">
            	<option value="propio">No</option>
              <option value="subordinado">Si</option>
            </select>
          	</td>  
          	<td align="center">
             <div style="width:25%;float:left;">
            <select name="departamentoId" id="departamentoId"  class="smallInput">
            <option value="" selected="selected">Todos...</option>
            {foreach from=$departamentos item=depto}
            <option value="{$depto.departamentoId}" >{$depto.departamento}</option>
            {/foreach}
          	</select> 
            </div>   
           	</td>
           	<td align="center">    	
            <select name="year" id="year"  class="smallInput">
                <option value="2012" {if $year == "2012"} selected="selected" {/if}>2012</option>
                <option value="2013" {if $year == "2013"} selected="selected" {/if}>2013</option>
                <option value="2014" {if $year == "2014"} selected="selected" {/if}>2014</option>
                <option value="2015" {if $year == "2015"} selected="selected" {/if}>2015</option>
                <option value="2016" {if $year == "2016"} selected="selected" {/if}>2016</option>
                <option value="2017" {if $year == "2017"} selected="selected" {/if}>2017</option>
                <option value="2018" {if $year == "2018"} selected="selected" {/if}>2018</option>
                <option value="2019" {if $year == "2019"} selected="selected" {/if}>2019</option>
                <option value="2020" {if $year == "2020"} selected="selected" {/if}>2020</option>
             </select>    
          	</td>
     	</tr>
       	<tr>
           <td colspan="6" align="center">            
            <div style="margin-left:270px">
            <a class="button_grey" id="btnAddCity" onclick="BuscarServiciosActivos()"><span>Buscar</span></a>        
            </div>
            </td>
           </tr>
          </table>	

		</fieldset>
		</form>
		<div align="center" id="loading" style="display:none">
       		<img src="{$WEB_ROOT}/images/loading.gif" />
            <br />
            Cargando...
            <br />&nbsp;
		</div>
        
  {if $msgOk}
   <p class="info" id="success" style="width:98%; margin-left:10px" onclick="hideMessage()">
   	<span class="info_inner">
    	{if $msgOk == 1}
        El Workflow ha sido desactivado correctamente
      {/if}
    	{if $msgOk == 2}
        El Workflow ha sido activado correctamente
      {/if}
    </span>
   </p>
   {/if}
   
   <div id="contenido"  style="display:none">
          {include file="lists/servicios_activos2.tpl"}            
          </div>
        
      </div>
      <div class="portlet-content nopadding borderGray" id="contenido2">
      

   <div id="busquedaServicios">
          {include file="lists/servicios_activos.tpl"}            
          </div>
        
      </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>