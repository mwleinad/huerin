 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="customerId" id="customerId" value="0" />
<input type="hidden" name="contractId" id="contractId" value="0" />
<table width="60%" align="center">
<tr style="background-color:#CCC">
    <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Raz&oacute;n Social:</td>
    <td align="center">Responsable:</td>
    <td align="center">Incluir Subordinados:</td>
    <td align="center">Departamento:</td>
</tr>
<tr>	
    <td align="center" style="padding-left: 5px; padding-right: 5px">
    	<input type="text" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%;" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
		    <td align="center" style="padding-left: 5px; padding-right: 5px">
    	<input type="text" name="rfc2" id="rfc2" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%;" />
          <div id="loadingDivDatosFactura2"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
        	 	</div>
         	</div>
		</td>
        <td align="center" style="padding-left: 5px; padding-right: 5px">
            <select name="responsableCuenta" id="responsableCuenta"  class="largeInput" style="width: 90%;">
            {* if $User.roleId=="1" *}
            <option value="0" selected="selected">Todos...</option>
            {* /if *}
            {foreach from=$personals item=personal}
            <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name|truncate:20:"..."}</option>
            {/foreach}
            </select>
		</td>    
		<td align="center" style="padding-left: 5px; padding-right: 5px">
			<input name="subordinados" id="subordinados" type="checkbox" value="1" style="width: 90%;"/>
		</td>     	
		<td align="center" style="padding-left: 5px; padding-right: 5px">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
		</td>
</tr>        
<tr>
    <td align="center" colspan="5">
        <div style="margin: 0 415px 0 415px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>