 <div align="center"  id="divForm">
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="80%" align="center">
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
    <td style="width:30%; padding:0px 4px 4px 8px;" align="center">
    	<input type="text" size="25" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
		<td style="width: 30%; padding:0px 4px 4px 8px;" align="center">
    	<input type="text" size="25" name="rfc2" id="rfc2" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura2"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
        	 	</div>
         	</div>
		</td>
        <td style="width: 15%; padding:0px 4px 4px 8px;" align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
		</td>    
		<td style="width: 5%; padding:0px 4px 4px 8px;" align="center">
			<input name="subordinados" id="subordinados" type="checkbox" value="1"/>
		</td>
		<td style="width: 20%; padding:0px 4px 4px 8px;" align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
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