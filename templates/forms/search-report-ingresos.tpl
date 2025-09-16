 <div align="center"  id="divForm">
 
<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="reporteIngresosToExcel" />
<input type="hidden" name="customerId" id="customerId" value="0" />
<input type="hidden" name="contractId" id="contractId" value="0" />
<table width="90%" align="center">
<tr style="background-color:#CCC">
    <td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
    <td align="center">Cliente:</td>
    <td align="center">Raz&oacute;n Social:</td>
    <td align="center">Responsable:</td>
    <td align="center">Departamento:</td>
    <td align="center">Mes:</td>
    <td align="center">Año:</td>
</tr>
<tr>	
    <td align="center" style="padding-left: 5px; padding-right: 5px">
    	<input type="text" name="like_customer_name" id="like_customer_name" class="largeInput" autocomplete="off" value="{$search.like_customer_name}" style="width: 90%;" />
          <div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
        	 	</div>
         	</div>
		</td>
		    <td align="center" style="padding-left: 5px; padding-right: 5px">
    	<input type="text" name="like_contract_name" id="like_contract_name" class="largeInput" autoscomplete="off" value="{$search.like_contract_name}" style="width: 90%;" />
          <div id="loadingDivDatosFactura2"></div>
					<div style="position:relative">
         		<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
        	 	</div>
         	</div>
		</td>
        <td align="center" style="padding-left: 5px; padding-right: 5px">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
		</td>       	
		<td align="center" style="padding-left: 5px; padding-right: 5px">
            {include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
		</td>
		<td align="center">
			<select name="period" id="period"  class="largeInput"  style="width: 90%;">
				<option value="">Todos</option>
				<option value="1">Enero</option>
				<option value="2">Febrero</option>
				<option value="3">Marzo</option>
				<option value="4">Abril</option>
				<option value="5">Mayo</option>
				<option value="6">Junio</option>
				<option value="7">Julio</option>
				<option value="8">Agosto</option>
				<option value="9">Septiembre</option>
				<option value="10">Octubre</option>
				<option value="11">Noviembre</option>
				<option value="12">Diciembre</option>
			</select>
		</td>
		<td align="center">
			{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
		</td>
</tr>        
<tr>
    <td style="text-align: center;" colspan="6">
        <div style="margin: 0 500px 0 500px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>