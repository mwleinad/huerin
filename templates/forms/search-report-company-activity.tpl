<div align="center"  id="divForm">
	<form name="frmSearch" id="frmSearch"  method="post" onsubmit="return false;">
		<input type="hidden" name="type" id="type" value="search" />
		<table width="100%" align="center">
			<tr style="background-color:#CCC">
				<td colspan="8" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center" style="width: 20%">Gerente:</td>
				<td align="center" style="width: 20%">Supervisor:</td>
				<td align="center" style="width: 20%">Sector</td>
				<td align="center" style="width: 20%">Subsector</td>
				<td align="center" style="width: 20%">Actividad</td>
			</tr>
			<tr>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					<select name="responsableGerente" id="responsableGerente"  class="largeInput select2">
						{if $User.level eq 1 || $User.allow_visualize_any_contract}
							<option value="0" selected="selected">Todos...</option>
						{/if}
						{foreach from=$personals item=personal}
							<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
						{/foreach}
					</select>
				</td>
				<td align="center" style="padding-left: 5px;padding-right: 5px">
					<select name="responsableSupervisor" id="responsableSupervisor"  class="largeInput select2">
					</select>
				</td>
				<td align="center">
					<select class="largeInput select2" name="sector" id="sector">
						<option value="">Seleccionar..</option>
						{foreach from=$sectores  item=sector key=key}
							<option value="{$sector.id}">{$sector.name}</option>
						{/foreach}
					</select>
				</td>
				<td align="center">
					<select class="largeInput select2" name="subsector" id="subsector">
					</select>
				</td>
				<td align="center">
					<select class="largeInput select2" name="actividad_comercial" id="actividad_comercial">
					</select>
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
