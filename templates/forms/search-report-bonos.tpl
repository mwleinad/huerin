<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" action="#" onsubmit="return false">
		<input type="hidden" name="type" id="type" value="generateBono" />
		<input type="hidden" name="contrato" id="contrato" value="" />
		<input type="hidden" name="cliente" id="cliente" value="" />
		<table width="100%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Responsable:</td>
				<td align="center">Departamento:</td>
				<td align="center">Periodo:</td>
				<td align="center">Año:</td>
			</tr>
			<tr>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					<select name="responsableCuenta" id="responsableCuenta" class="largeInput">
						{foreach from=$personals item=personal}
							<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
						{/foreach}
					</select>
				</td>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
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
				<td align="center" colspan="5">
					<div style="display:inline-block;text-align: center;">
						<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
						<a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Generar</span></a>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
