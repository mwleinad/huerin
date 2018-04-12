<div align="center"  id="divForm">

	<form name="frmSearch" id="frmSearch" action="" method="post">
		<input type="hidden" name="type" id="type" value="search" />
		<input type="hidden" name="correo" id="correo" value="" />
		<input type="hidden" name="texto" id="texto" value="" />
		<input type="hidden" name="personalId" id="personalId" value="0" />
		<table width="500" align="center">
			<tr style="background-color:#CCC">
				<td colspan="6" bgcolor="#CCCCCC" align="center"><b>Filtro de B&uacute;squeda</b></td>
			</tr>
			<tr>
				<td align="center">SUPERVISOR:</td>
				<td align="center">TRIMESTRE:</td>
				<td align="center">DEPARTAMENTO:</td>
			</tr>
			<tr>
				<td align="center">
					<input type="text" size="35" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
					<div id="loadingDivDatosFactura"></div>
					<div style="position:relative">
						<div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
						</div>
					</div>
				</td>
				<td align="center">
					<select name="trimestre" id="trimestre"  class="smallInput">
						{foreach from=$TRIMESTRE item=item key=key}
						<option value="{$item.fecha}" >{$item.fechaNombre}</option>
						{/foreach}
					</select>
				</td>
				<td align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
				</td>
				<td align="center">
					<select name="departamentoId" id="departamentoId"  class="smallInput">
						<option value="" selected="selected">Todos...</option>
						{foreach from=$DEPARTAMENTOS item=depto}
						<option value="{$depto.departamentoId}" >{$depto.departamento}</option>
						{/foreach}
					</select>
				</td>


			</tr>
			<tr>
				<td colspan="6" align="center">
					<div style="margin-left:380px">
						<a class="button_grey" id="btnBuscar" onclick="doSearch()"><span>Buscar</span></a>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>