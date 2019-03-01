 <div align="center"  id="divForm">
<form name="frmSearch" id="frmSearch" action="" method="post" onsubmit="return false">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table class="tableFull" align="center">
<tr style="background-color:#CCC">
    <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de Busqueda</b></td>
</tr>
<tr>
    <td align="center">Cliente o Razon social</td>
    <td align="center">Responsable</td>
    <td align="center">Incluir subordinados</td>
    <td align="center">Departamento:</td>
	<td align="center">Mes:</td>
	<td align="center">A&ntilde;o:</td>
</tr>
<tr>	
    <td align="center">
    	<input type="text" size="35" name="rfc" id="rfc" class="largeInput medium2" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
        <td align="center">
    	<select name="responsableCuenta" id="responsableCuenta"  class="largeInput medium2">
			{if $User.tipoPersonal eq 'Socio' ||$User.tipoPersonal eq 'Coordinador'||$User.tipoPersonal eq 'Admin'}
			 	<option value="0">Todos...</option>
			{/if}
			{foreach from=$personals item=personal}
		 		<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId && $search.responsableCuenta} selected="selected" {/if} >{$personal.name}</option>
			{/foreach}
      	</select>
		</td>    
		<td align="center">
			<input name="deep" id="deep" type="checkbox"/>
		</td>  
	   <td align="center">
			<select name="departamentoId" id="departamentoId"  class="largeInput medium2">
			<option value="" selected="selected">Todos...</option>
			{foreach from=$departamentos item=depto}
			<option value="{$depto.departamentoId}" >{$depto.departamento}</option>
			{/foreach}
			</select>
		</td>
		<td align="center">
			{include file="{$DOC_ROOT}/templates/forms/comp-filter-month.tpl" nameField='month' clase='largeInput medium2' all=true}
		</td>
		<td align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
        </td>
</tr>
<tr>
    <td align="center" colspan="5">
		<div style="text-align: center; width: 100%">
            {include file="{$DOC_ROOT}/templates/boxes/loaded.tpl"}
			<div style="display: inline-block">
				<a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
			</div>
		</div>

    </td>
</tr>
</table>
</form>
</div>