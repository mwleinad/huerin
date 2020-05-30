<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" action="export/report-bonos.php">
		<input type="hidden" name="type" id="type" value="searchBonos" />
		<input type="hidden" name="contrato" id="contrato" value="" />
		<input type="hidden" name="cliente" id="cliente" value="" />
		<table width="100%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Cliente:</td>
				<td align="center">Raz&oacute;n Social:</td>
				<td align="center">Responsable:</td>
				<td align="center">Incluir Subordinados:</td>
				<td align="center">Departamento:</td>
				<td align="center">Periodo:</td>
				<td align="center">Año:</td>
				<td align="center">Servicios sin workflow:</td>
			</tr>
			<tr>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					<input type="text" name="like_customer_name" id="like_customer_name" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%"  />
					<div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
						<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
						</div>
					</div>
				</td>
				<td align="center"style="padding-left: 5px;padding-right: 5px">
					<input type="text" name="like_contract_name" id="like_contract_name" class="largeInput" autocomplete="off" value="{$search.rfc}" style="width: 90%" />
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
					<input name="deep" id="deep" type="checkbox" value="1" style="width: auto"/>
				</td>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
				</td>
				<td align="center">
					<select name="period" id="period"  class="largeInput"  style="width: 90%;">
						<option value="">Todos</option>
						<option value="efm">Ene Feb Mar</option>
						<option value="amj">Abr May Jun</option>
						<option value="jas">Jul Ago Sep</option>
						<option value="ond">Oct Nov Dic</option>
					</select>
				</td>
				<td align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
				</td>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					<input name="whitoutWorkflow" id="whitoutWorkflow" type="checkbox" value="1" style="width: auto"/>
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