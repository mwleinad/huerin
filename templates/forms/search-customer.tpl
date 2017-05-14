<div align="center"  id="divForm">
<form id="addCustomerFormSearch" name="addCustomerFormSearch" method="post" action="{$WEB_ROOT}/export/customer.php">
<input type="hidden" id="cliente" name="cliente" value="0"/>
<input type="hidden" id="cuenta" name="cuenta" value="0"/>
<input type="hidden" id="type" name="type" value="{$tipo}" /> 
<table width="100%" align="center">
<tr style="background-color:#CCC">
    <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente o Raz&oacute;n Social:</td>
    <td align="center">Responsable:</td>
    <td align="center">Incluir Subordinados:</td>
</tr>
<tr>	
    <td align="center">
    	<input type="text" size="50" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$customerNameSearch}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>        
    <td align="center">
    	<select name="responsableCuenta" id="responsableCuenta"  class="smallInput">
      	{* if $User.roleId=="1" *}
		<option value="0" selected="selected">Todos...</option>
		{* /if *}
        {foreach from=$personals item=personal}
      	<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
        {/foreach}
      	</select>  
		</td>    
		<td align="center">
			<input name="deep" id="deep" type="checkbox" checked="checked"/>
		</td>  
	 </tr>
<tr>
    <td colspan="3" align="center">
        <div style="margin-left:430px">
        <a class="button_grey" id="btnAddCity" onclick="BuscarServiciosActivos()"><span>Buscar</span></a> 
        </div> 
    </td>
</tr>
</table>
</form>
</div>