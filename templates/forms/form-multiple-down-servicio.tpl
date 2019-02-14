<div id="divForm">
	<form id="frmMultiple" name="frmMultiple" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="saveMultipleService"/>
	<input type="hidden" id="contractId" name="contractId" value="{$contractId}"/>
	<fieldset>
	<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
		<thead>
		<tr>
			<th></th>
			<th>Servicio</th>
			<th>Costo</th>
			<th>Inicio operaciones</th>
			<th>Inicio factura</th>
			<th>status</th>
			<th>Fecha ultimo workflow</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$servicios key=key item=item}
			<tr>
				<td><input type="checkbox" name="servsMod[]" value="{$item.servicioId}" checked></td>
				<td>{$item.nombreServicio}</td>
				<td><input type="text" class="xsmallIn" name="costo_{$item.servicioId}" value="{$item.costo}"></td>
				<td><input type="text" class="xsmallIn" name="io_{$item.servicioId}" id="io_{$item.servicioId}" value="{$item.inicioOperaciones|date_format:'%d-%m-%Y'}" onclick="CalendarioSimple(this)"></td>
				<td><input type="text" class="xsmallIn" name="if_{$item.servicioId}" id="if_{$item.servicioId}" value="{if $item.inicioFactura eq '0000-00-00'||$item.inicioFactura eq ''}{else}{$item.inicioFactura|date_format:'%d-%m-%Y'}{/if}" onclick="CalendarioSimple(this)"></td>
				<td>
					<input type="hidden" name="beforeStatus_{$item.servicioId}" id="beforeStatus_{$item.servicioId}" value="{$item.status}">
					<select name="status_{$item.servicioId}" id="status_{$item.servicioId}" class="smallIn2 servModStatus" data-serv = {$item.servicioId}>
						{if $item.status eq 'bajaParcial' || $item.status eq 'activo'}
							<option value="activo" {if $item.status eq 'activo'}selected{/if}>Activo</option>
						{/if}
						{if $item.status eq 'bajaParcial' || $item.status eq 'activo' || $item.status eq 'baja' || $item.status eq 'readonly'}
							<option value="baja" {if $item.status eq 'baja'}selected{/if}>Baja</option>
						{/if}
						{if $item.status eq 'activo' || $item.status eq 'bajaParcial' || $item.status eq 'readonly'}
						<option value="bajaParcial" {if $item.status eq 'bajaParcial'}selected{/if}>Baja temporal</option>
						{/if}
						{if $item.status eq 'baja' || $item.status eq 'readonly'}
							<option value="readonly" {if $item.status eq 'readonly'}selected{/if}>Activo solo lectura</option>
						{/if}
					</select></td>
				<td>
					<div {if $item.status neq 'bajaParcial'}style="display: none;"{/if} id="divLast_{$item.servicioId}">
						<input type="text" class="xsmallIn" name="lastDateWorkflow_{$item.servicioId}" id="lastDateWorkflow_{$item.servicioId}" onclick="CalendarioSimple(this)" value="{if $item.lastDateWorkflow eq '0000-00-00'||$item.lastDateWorkflow eq ''}{else}{$item.lastDateWorkflow|date_format:'%d-%m-%Y'}{/if}">
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	</fieldset>
	<div style="clear:both"></div>
	<div class="actionPopup">
		<span class="msjRequired">* Campos requeridos </span><br>
		<div class="actionsChild">
			<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img-mul"/>
		</div>
		<div class="actionsChild">
			<a  href="javascript:;" id="btnSaveMultServ" name="btnSaveMultServ" class="button_grey">
				<span>Guardar</span>
			</a>
		</div>
	</div>
	</form>

</div>