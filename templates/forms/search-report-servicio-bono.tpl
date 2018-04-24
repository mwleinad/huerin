 <div align="center"  id="divForm">

	<form name="frmSearch" id="frmSearch" method="post" action="export/rep-servicios-bonos.php">
		<input type="hidden" name="type" id="type" value="search" />
		<input type="hidden" name="correo" id="correo" value="" />
		<input type="hidden" name="texto" id="texto" value="" />
		<input type="hidden" name="cliente" id="cliente" value="0" />
		<table width="100%" align="center">

			<tr style="background-color:#CCC">
				<td colspan="4" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">Cliente:</td>
				<td align="center">Responsable:</td>
				<td align="center">Incluir Subordinados:</td>
				<td align="center">Departamento:</td>
			</tr>
			<tr>
				<td align="center">
					<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}"  style="width: 90%;"/>
					<div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
						<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
						</div>
					</div>
				</td>
				<td align="center">
					<select name="responsableCuenta" id="responsableCuenta"  class="largeInput" style="width: 90%;">
						{* if $User.roleId=="1" *}
						<option value="0" selected="selected">Todos...</option>
						{* /if *}
						{foreach from=$personals item=personal}
						<option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
						{/foreach}
					</select>
				</td>
				<td align="center">
					<input name="deep" id="deep" type="checkbox"/>
				</td>

				<td align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-dep.tpl"}
				</td>
			</tr>

			<tr>
				<td align="center" >Periodo:</td>
				<td align="center" >Año</td>
				<td align="center" >Orden A-Z por:</td>
				<td align="center">Buscar</td>
			</tr>
			<tr>
				<td align="center">
					<select name="period" id="period"  class="largeInput"  style="width: 90%;">
						<option value="efm">Ene Feb Mar</option>
						<option value="amj">Abr May Jun</option>
						<option value="jas">Jul Ago Sep</option>
						<option value="ond">Oct Nov Dic</option>
					</select>
				</td>

				<td align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
				</td>
				<td align="center">
					<select name="ordenAZ" id="ordenAZ"  class="largeInput"  style="width: 90%;">
						<option value="C. Asignado">C. Asignado</option>
						<option value="Cliente">Cliente</option>
						<option value="Razon Social">Razon Social</option>
					</select>

				</td>

				<td align="center">
					<div >
						<a class="button_grey" id="btnBuscar" onclick="doSearch()"  style="width: 90%;margin-top: -1px;"><span>Buscar</span></a>
					</div>
				</td>
			</tr>
		</table>
	</form>
 </div>