 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="500" align="center">
<tr style="background-color:#CCC; width:700px">
    <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Filtro de Busqueda</b></td>
</tr>
<tr>
    <td align="center">Cliente o Razon social</td>
    <td align="center">Responsable</td>
    <td align="center">Incluir subordinados</td>
    <td align="center">Facturador</td>
</tr>
<tr>	
    <td align="center">
    	<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
        <td align="center">
    	<select name="responsableCuenta" id="responsableCuenta"  class="largeInput">
      	{if $User.tipoPersonal=="Socio" ||  $User.tipoPersonal=="Coordinador" ||  $User.tipoPersonal=="Admin"}
		<option value="0" selected="selected">Todos...</option>
		{/if}
        {foreach from=$personals item=personal}
      	<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
        {/foreach}
      </select>  
		</td>    
		<td align="center">
			<input name="deep" id="deep" type="checkbox"/>
		</td>  
    	<td align="center">
			<select id="facturador" class="largeInput" name="facturador">
                    <option value="0">Todos</option>
                    <option value="BHSC">BHSC Contadores SC</option>
                    <option value="Huerin">Braun Huerin SC</option>
                    <option value="Braun">Jacobo Braun</option>
                    <option value="Efectivo">Efectivo</option>
			</select>
		</td>
    <td align="center">
        {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
    </td>
</tr>
<tr>
    <td align="center" colspan="4">
        <div style="margin-left:400px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>