 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="800" align="center">
<tr style="background-color:#CCC">
    <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Responsable:</td>
    <td align="center">Incluir Subordinados:</td>
    <td align="center">Departamento:</td>
    <td align="center">A&ntilde;o:</td>               
    <td align="center">Mes:</td>               
    <td align="center"></td>
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
			<input name="deep" id="deep" type="checkbox"/>
		</td>  
		
		   <td align="center">
    	<select name="departamentoId" id="departamentoId"  class="smallInput">
      	<option value="" selected="selected">Todos...</option>
        {foreach from=$departamentos item=depto}
      	<option value="{$depto.departamentoId}" >{$depto.departamento}</option>
        {/foreach}
      </select>  
		</td>  
		
		<td align="center">
      	{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl" class='smallInput"'}
       </td>
	<td>
	    <select name="month" id="month"  class="smallInput"  style="min-width:100px">
      	<option value="1" {if $month == "1"} selected="selected" {/if}>Enero</option>
      	<option value="2" {if $month == "2"} selected="selected" {/if}>Febrero</option>
      	<option value="3" {if $month == "3"} selected="selected" {/if}>Marzo</option>
      	<option value="4" {if $month == "4"} selected="selected" {/if}>Abril</option>
      	<option value="5" {if $month == "5"} selected="selected" {/if}>Mayo</option>
      	<option value="6" {if $month == "6"} selected="selected" {/if}>Junio</option>
      	<option value="7" {if $month == "7"} selected="selected" {/if}>Julio</option>
      	<option value="8" {if $month == "8"} selected="selected" {/if}>Agosto</option>
      	<option value="9" {if $month == "9"} selected="selected" {/if}>Septiembre</option>
      	<option value="10" {if $month == "10"} selected="selected" {/if}>Octubre</option>
      	<option value="11" {if $month == "11"} selected="selected" {/if}>Noviembre</option>
      	<option value="12" {if $month == "12"} selected="selected" {/if}>Diciembre</option>
     </select>
	</td>
    </tr>
<tr>
    <td colspan="6" align="center">
        <div style="margin-left:410px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>