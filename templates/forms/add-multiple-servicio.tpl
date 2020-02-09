<div id="divForm">
	<form id="addServicioForm" name="addServicioForm" method="post" autocomplete="off">
	<input type="hidden" id="type" name="type" value="saveMultipleServicio"/>
	<input type="hidden" id="contractId" name="contractId" value="{$post.id}"/>
	<input type="hidden" id="fromEvent" name="fromEvent" value="{$post.fromEvent}"/>
		<fieldset>
			<table class="tableFullWidth" id="box-table-a">
				<thead>
					<tr>
						<th class="cell40">Servicio</th>
						<th class="cell20">Inicio Operaciones</th>
						<th class="cell20">Inicio Factura</th>
						<th class="cell10">Costo</th>
						<th class="cell10"></th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						<select name="tipoServicioId" id="tipoServicioId" class="mediumIn2" onchange="UpdateCosto()">
							<option value="0">Seleccione...</option>
							{foreach from=$tiposDeServicio item=item}
								<option {if $item.tipoServicioId == $post.tipoServicioId} selected="selected"{/if}value="{$item.tipoServicioId}">{$item.nombreServicio}</option>
							{/foreach}
						</select>
					</td>
					<td><input class="smallIn2" name="inicioOperaciones" id="inicioOperaciones"  onclick="CalendarioSimple(this)" type="text" value="{$post.inicioOperacionesMysql}"/></td>
					<td><input class="smallIn2" onclick="CalendarioSimple(this)" name="inicioFactura" id="inicioFactura" type="text" value="{$post.inicioFacturaMysql}" size="27"/></td>
					<td><input class="smallInput medium" name="costo" id="costo" type="text" value="{$post.costo}" size="50"/></td>
					<td><a href="javascript:;" id="addItemService" tittle="Agregar servicio" class="spanAll">
					       <img src="{$WEB_ROOT}/images/icons/plus.png">		
						</a>
					</td>
				</tr>
				</tbody>
			</table>
			<hr />
			<div id="contenidoItems">
			</div>
			<div style="clear:both"></div>
			<div class="actionPopup">
				<span class="msjRequired">* Campos requeridos </span><br>
				<div class="actionsChild">
					<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				</div>
				<div class="actionsChild">
					<a class="button_grey" id="addServiceButton"><span>Agregar</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
