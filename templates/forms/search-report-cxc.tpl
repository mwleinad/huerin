 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="correo" id="correo" value="" />
<input type="hidden" name="texto" id="texto" value="" />
<input type="hidden" name="cliente" id="cliente" value="0" />
<table width="80%" align="center">
<tr style="background-color:#CCC; width:700px">
    <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Filtro de Busqueda</b></td>
</tr>
<tr>
    <td align="center">Cliente o Razon social</td>
    <td align="center">Responsable</td>
    <td align="center">Incluir subordinados</td>
    <td align="center">Facturador</td>
    <td align="center">Año</td>
</tr>
<tr>	
    <td  style="width: 35%; padding:0px 4px 4px 8px;" align="center">
    	<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
        <td style="width: 35%; padding:0px 4px 4px 8px;" align="center">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
		</td>    
		<td  style="width: 5%; padding:0px 4px 4px 8px;" align="center">
			<input name="deep" id="deep" type="checkbox"/>
		</td>  
    	<td  style="width:15%; padding:0px 4px 4px 8px;" align="center">
			<select id="facturador" class="largeInput" name="facturador">
                    <option value="">Todos</option>
                    <option value="BHSC">BHSC Contadores SC</option>
                    <option value="Huerin">Braun Huerin SC</option>
                    <option value="Braun">Jacobo Braun</option>
                    <option value="Efectivo">Efectivo</option>
			</select>
		</td>
    <td style="width: 10%; padding:0px 4px 4px 8px;">
        {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl" all=true}
    </td>
</tr>
<tr>
    <td align="center" colspan="5">
        <div style="display:inline-block;text-align: center;">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>