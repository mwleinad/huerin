<div id="divForm">
	<form id="frmAcceptQuote" name="frmAcceptQuote" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="sendToMain"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16" style="text-align: center; font-weight: bold;">
					<p>Lista de servicios validados</p>
				</div>
				<div class="grid_16">
					<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
						<thead>
							<th></th>
							<th>Servicio</th>
							<th>Inicio de operaciones</th>
							<th>¿Requiere factura?</th>
							<th>Inicio de facturacion</th>
						</thead>
						<tbody>
						{foreach from=$services key=key item=item}
							<tr>
								<td><input type="checkbox" name="quotes[]"   value="{$item.quote_id.id}" checked onclick="return false;" />
									<input type="hidden" name="service_id_"   value="{$item.quote_id.id}" checked onclick="return false;" />
								</td>
								<td>{$item.quote_id.service_name}</td>
								<td><input type="text" class="largeInput" name="date_init_operation_{$item.quote_id.id}"
										   id="date_init_operation_{$item.quote_id.id}"
										   onclick="CalendarioSimple(this)" /></td>
								<td><select class="largeInput controlSelectInvoice" style="width: 15%" name="do_invoice_{$item.quote_id.id}"
											id="do_invoice_{$item_quote_id.id}" data-id="{$item.quote_id.id}">
										<option value="0">No</option>
										<option value="1">Si</option>
									</select>
								</td>
								<td style="display: none"  id="col_init_invoice_{$item.quote_id.id}"><input type="text" class="largeInput" name="date_init_invoice_{$item.quote_id.id}"
										   id="date_init_invoice_{$item.quote_id.id}"
										   onclick="CalendarioSimple(this)"
											/></td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</fieldset>
		<div style="clear:both"></div>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loader"/>
			</div>
			<div class="actionsChild">
				<a  href="javascript:;" class="button_grey spanSaveSendToMain" title="La informacion del prospecto y los servicios se enviara a la cartera de clientes.">
					<span>Enviar a cartera</span>
				</a>
			</div>
		</div>
	</form>

</div>
