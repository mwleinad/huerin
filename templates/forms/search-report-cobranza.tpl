<div align="center" id="divForm">
	<form name="frmSearch" id="frmSearch" action="" method="post">
		<input type="hidden" name="type" id="type" value="searchAcumulada">
		<input type="hidden" name="cliente" id="cliente" value="0" />
		<table class="tableFull " align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
			</tr>
			<tr>
				<td align="center">Cliente</td>
				<td align="center">Responsable</td>
				<td align="center">Incluir Subordinados</td>
				<td align="center">Mes inicial</td>
				<td align="center">Mes final</td>
				<td align="center">Año</td>
				<td align="center">Detallar por</td>
				<td align="center">Incluir iva</td>
			</tr>
			<tr>
				<td style="width:auto; padding:0px 4px 4px 8px;" align="center">
					<input type="text" size="35" name="rfc" id="rfc" class="largeInput medium2" autocomplete="off" value="{$search.rfc}"  style="width: 90%;"/>
					<div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
						<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
						</div>
					</div>
				</td>
				<td style="width:auto; padding:0px 4px 4px 8px;" align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
				</td>
				<td align="center">
					<input name="subordinados" id="subordinados" type="checkbox"/>
				</td>
				<td style="width: auto; padding:0px 4px 4px 8px;" align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-month.tpl" nameField='monthInicial' clase='largeInput medium2'}
				</td>
				<td style="width: auto; padding:0px 4px 4px 8px;" align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-month.tpl" nameField='monthFinal' clase='largeInput medium2' month={$smarty.now|date_format:'%m'}}
				</td>
				<td style="width: auto; padding:0px 4px 4px 8px;" align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
				</td>
				<td style="width: auto; padding:0px 4px 4px 8px;" align="center">
					<select id="groupBy" name="groupBy" class="largeInput medium2">
						<option value="contrato">Por razon social</option>
					</select>
				</td>
				<td align="center">
					<input name="whitiva" id="whitiva" type="checkbox"/>
				</td>
			</tr>
			<tr align="center">
				<td colspan="7" align="center">
					<div style="display:inline-block;text-align: center;">
						<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
						<a class="button_grey" id="btnSearch"><span>Buscar</span></a>
					</div>

				</td>
			</tr>
		</table>
</div>