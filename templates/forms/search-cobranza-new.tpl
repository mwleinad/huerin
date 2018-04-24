 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="80%" align="center">
<tr style="background-color:#CCC">
    <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Responsable:</td>

    <td align="center">Incluir Subordinados:</td>

    <td align="center">Solo Atrasados:</td>

   <td align="center">Departamento:</td>

    <td align="center">A&ntilde;o:</td>
    <td align="center"></td>
</tr>
<tr>	
    <td style="width:35%; padding:0px 4px 4px 8px;" align="center">
    	<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>        
    <td style="width:35%; padding:0px 4px 4px 8px;" align="center">
            <select name="responsableCuenta" id="responsableCuenta"  class="largeInput">
            {* if $User.roleId=="1" *}
            <option value="0" selected="selected">Todos...</option>
            {* /if *}
            {foreach from=$personals item=personal}
            <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
            {/foreach}
            </select>  
		</td>    

		<td  style="width: 5%; padding:0px 4px 4px 8px;"align="center">
			<input name="deep" id="deep" type="checkbox"/>
		</td>  

		<td style="width: 5%; padding:0px 4px 4px 8px;" align="center">
			<input name="atrasados" id="atrasados" type="checkbox"/>
		</td>  
		

		<td style="width: 15%; padding:0px 4px 4px 8px;" align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
		</td>  

		<td style="width:10%; padding:0px 4px 4px 8px;" align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}

    </td>
    </tr>
<tr>
    <td colspan="6" align="center">
        <div style="margin-left:380px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>