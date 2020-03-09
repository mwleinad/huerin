<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" onsubmit="return false;">
		<input type="hidden" name="type" id="type" value="estadoResultado" />
		<table width="80%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Responsable:</td>
				<td align="center">Incluir Subordinados:</td>
				<td align="center">Departamento:</td>
				<td align="center">Tipo periodo</td>
				<td align="center">Periodo:</td>
				<td align="center">Año:</td>
			</tr>
			<tr>
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
					<select name="tipoPeriodo" id="tipoPeriodo"  class="largeInput"  style="width: 90%;">
						<option value="mensual">Mensual</option>
						<option value="trimestral">Trimestral</option>
					</select>
				</td>
				<td align="center">
					<div id="divMensual">
						<select  name="period" id="periodMensual" class="largeInput">
							<option value="" selected="selected">Todos</option>
							<option value="1" {if $month == "01"} selected="selected" {/if}>Enero</option>
							<option value="2" {if $month == "02"} selected="selected" {/if}>Febrero</option>
							<option value="3" {if $month == "03"} selected="selected" {/if}>Marzo</option>
							<option value="4" {if $month == "04"} selected="selected" {/if}>Abril</option>
							<option value="5" {if $month == "05"} selected="selected" {/if}>Mayo</option>
							<option value="6" {if $month == "06"} selected="selected" {/if}>Junio</option>
							<option value="7" {if $month == "07"} selected="selected" {/if}>Julio</option>
							<option value="8" {if $month == "08"} selected="selected" {/if}>Agosto</option>
							<option value="9" {if $month == "09"} selected="selected" {/if}>Septiembre</option>
							<option value="10" {if $month == "10"} selected="selected" {/if}>Octubre</option>
							<option value="11" {if $month == "11"} selected="selected" {/if}>Noviembre</option>
							<option value="12" {if $month == "12"} selected="selected" {/if}>Diciembre</option>
						</select>
					</div>
					<div id="divTrimestral" style="display: none;">
						<select name="period" id="periodTrimestral"  class="largeInput"  style="width: 90%;" disabled>
							<option value="efm">Ene Feb Mar</option>
							<option value="amj">Abr May Jun</option>
							<option value="jas">Jul Ago Sep</option>
							<option value="ond">Oct Nov Dic</option>
						</select>
					</div>

				</td>
				<td align="center">
					{include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
				</td>
			</tr>
			<tr>
				<td align="center" colspan="5">
					<div style="display:inline-block;text-align: center;">
						<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
						<a class="button_grey" id="btnBuscar" "><span>Buscar</span></a>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>