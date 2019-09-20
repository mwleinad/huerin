<div align="center" id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" action="export/report-exp-employe.php">
		<input type="hidden" name="type" id="type" value="generateReportExp">
		<table style="width: 50%"align="center">
			<tr style="background-color:#CCC">
				<td colspan="2" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
			</tr>
			<tr>
				<td align="center" style="width: 25%">Empleados</td>
				<td align="center" style="width: 25%">Status de expedientes</td>
			</tr>
			<tr>
				<td style="width:auto; padding:0px 4px 4px 8px;" align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
				</td>
				<td style="width: auto; padding:0px 4px 4px 8px;" align="center">
					<select name="status" id="status"  class="largeInput"  style="width: 90%;">
						<option value="">Todos</option>
						<option value="complete">Completos</option>
						<option value="incomplete">Incompletos</option>
					</select>
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