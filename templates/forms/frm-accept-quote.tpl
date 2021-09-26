<div id="divForm">
	<form id="frmAcceptQuote" name="frmAcceptQuote" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="sendToMain"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<div class="">
		<button class="accordion">Datos {if $prospect.customer}existentes{/if} del cliente</button>
		<div class="panel">
			{include file="{$DOC_ROOT}/templates/forms/frm-customer-from-prospect.tpl"}
		</div>
		<button class="accordion">Datos  {if $post.contract}existentes{/if} de la empresa</button>
		<div class="panel">
			{include file="{$DOC_ROOT}/templates/forms/frm-data-basic-contract.tpl"}
		</div>
		<button class="accordion">Listado de servicios</button>
		<div class="panel">

					<div class="container_16">
						<div class="grid_16" style="text-align: center; font-weight: bold;">
							<p>Lista de servicios validados</p>
						</div>
						<div class="grid_16">
							<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
								<thead>
								<th></th>
								<th>Servicio</th>
								<th>Costo cotizado</th>
								<th>Inicio de operaciones</th>
								<th>Inicio de facturacion</th>
								</thead>
								<tbody>
								{foreach from=$services key=key item=item}
									<tr>
										<td><input type="checkbox" name="quotes[]"   value="{$item.quote_id.id}" checked onclick="return false;" /></td>
										<td><input type="hidden" id="service_id_{$item.quote_id.id}"
												   name="service_id_{$item.quote_id.id}"
												   value="{$item.service_id}"/>
											<input type="hidden" id="name_{$item.quote_id.id}"
												   name="name_{$item.quote_id.id}"
												   value="{$item.quote_id.service_name}"/>
											{$item.quote_id.service_name}</td>
										<td><input type="text" class="largeInput" id="price_{$item.quote_id.id}"
												   name="price_{$item.quote_id.id}" readonly
												   value="{$item.quote_id.total|number_format:2:'.':''}"/></td>
										<td><input type="text" class="largeInput" name="date_init_operation_{$item.quote_id.id}"
												   id="date_init_operation_{$item.quote_id.id}"
												   onclick="CalendarioSimple(this)" /></td>
										<td><input type="text" class="largeInput" name="date_init_invoice_{$item.quote_id.id}"
												   id="date_init_invoice_{$item.quote_id.id}"
												   onclick="CalendarioSimple(this)"/>
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					</div>
		</div>

	</div>

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
