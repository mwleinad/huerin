 <div align="center"  id="divForm">

<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<input type="hidden" name="departamentoId" id="departamentoId"  value="">
<table width="80%" align="center">
<tr style="background-color:#CCC">
    <td colspan="4" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Raz&oacute;n Social:</td>
    <td align="center">Responsable:</td>
    <td align="center">Incluir Subordinados:</td>
</tr>
<tr>
    <td align="center" style="padding-left: 5px;padding-right: 5px">
    	<input type="text" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%"  />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
		    <td align="center"style="padding-left: 5px;padding-right: 5px">
    	<input type="text" name="rfc2" id="rfc2" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%" />
          <div id="loadingDivDatosFactura2"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
        	 	</div>
         	</div>
		</td>
        <td align="center" style="padding-left: 5px;padding-right: 5px">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
		</td>
		<td align="center" style="padding-left: 5px;padding-right: 5px">
			<input name="subordinados" id="subordinados" type="checkbox" value="1" style="width: auto"/>
		</td>
</tr>
<tr>
    <td align="center" colspan="4">
        <div style="margin-left:{if $infoUser.tipoPersonal == ""}400px{else}320px{/if}">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>
