 <div align="center"  id="divForm">

<form name="frmSearch" id="frmSearch" action="" method="post">
<input type="hidden" name="type" id="type" value="search" />
<input type="hidden" name="customerId" id="customerId" value="0" />
<input type="hidden" name="contractId" id="contractId" value="0" />
<table width="70%" align="center">
<tr style="background-color:#CCC">
    <td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
</tr>
<tr>
	<td align="center">Mes</td>
	<td align="center">Año</td>
	<td align="center">Movimiento</td>
</tr>
<tr>
	<td align="center" style="padding-left: 5px; padding-right: 5px">
		{include file="{$DOC_ROOT}/templates/forms/comp-filter-month.tpl" clase="largeInput" nameField="month" all=true}
	</td>
	<td align="center" style="padding-left: 5px; padding-right: 5px">
		{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl" all=true}
	</td>
	<td align="center">
		<select name="statusSearch" id="statusSearch"  class="largeInput"  style="width: 90%;">
			<option value="">Todas</option>
			<option value="activo">Altas</option>
			<option value="bajaParcial">Baja temporal</option>
			<option value="baja">Baja definitiva</option>
		</select>
	</td>
</tr>
<tr>
    <td align="center" colspan="3">
        <div style="margin: 0 500px 0 600px">
        <a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
        </div>
    </td>
</tr>
</table>
</form>
</div>
