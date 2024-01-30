<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" action="#" onsubmit="return false">
		<input type="hidden" name="type" id="type" value="generateBonoConsolidado" />
		<table width="100%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Seleccione un año fiscal:</td>
			</tr>
			<tr>
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
