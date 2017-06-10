 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="900" align="center">
<tr style="background-color:#CCC">
    <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Raz&oacute;n Social:</td>
    <td align="center">Responsable:</td>
    <td align="center">Incluir Subordinados:</td>
    {*}
    <td align="center">Facturador:</td>
    {*}
    <td align="center">Departamento:</td>
</tr>
<tr>	
    <td align="center">
    	<input type="text" size="25" name="rfc" id="rfc" class="smallInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
		    <td align="center">
    	<input type="text" size="25" name="rfc2" id="rfc2" class="smallInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura2"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
        	 	</div>
         	</div>
		</td>
        <td align="center">
            <select name="responsableCuenta" id="responsableCuenta"  class="smallInput">
            {* if $User.roleId=="1" *}
            <option value="0" selected="selected">Todos...</option>
            {* /if *}
            {foreach from=$personals item=personal}
            <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name|truncate:20:"..."}</option>
            {/foreach}
            </select>
		</td>    
		<td align="center">
			<input name="subordinados" id="subordinados" type="checkbox" value="1"/>
		</td>  
        {*}
    	<td align="center">
			<select id="facturador" class="smallInput" name="facturador">
                    <option value="0">Todos</option>
                    <option value="Huerin">Braun Huerin SC</option>
                    <option value="Braun">Jacobo Braun</option>
                    <option value="Efectivo">Efectivo</option>
			</select>
		</td>
        {*}
		<td align="center">
    	<select name="departamentoId" id="departamentoId"  class="smallInput">
      	<option value="" selected="selected">Todos...</option>
        {foreach from=$departamentos item=depto}
      	<option value="{$depto.departamentoId}" >{$depto.departamento}</option>
        {/foreach}
      </select> 
		</td>
</tr>        
<tr>
    <td align="center" colspan="5">
        <div style="margin-left:320px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>