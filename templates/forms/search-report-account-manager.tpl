<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" onsubmit="return false">
		<input type="hidden" name="type" id="type" value="accountByManager" />
		<table width="75%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Responsable</td>
				<td align="center">Departamento</td>
			</tr>
			<tr>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
				</td>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
				</td>
			</tr>
			<tr>
				<td align="center" colspan="5">
					<div style="display:inline-block;text-align: center;">
						<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
						<a class="button_grey" id="btnBuscar"><span>Generar</span></a>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
