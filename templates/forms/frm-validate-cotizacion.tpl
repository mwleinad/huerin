<div id="divForm">
	<form id="frmValidate" name="frmValidate" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="validateQuote"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16" style="text-align: center; font-weight: bold;">
						<p>Detalle de costos de los servicios</p>
				</div>
				<div class="grid_16">
					<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
						<thead>
						<th></th>
						<th>Servicio</th>
						<th>Costo sin iva</th>
						<th>Mejorar costo en %</th>
						</thead>
						<tbody>
						{foreach from=$services key=key item=item}
							<tr>
								<td><input type="checkbox" class="largeInput" name="list_quotes[]"
										   value="{$item.quote_id.id}" checked onclick="return false"></td>
								<td>{$item.quote_id.service_name}</td>
								<td><input class="largeInput inputPrice" name="price_{$item.quote_id.id}"
										   id="price_{$item.quote_id.id}" value="{$item.quote_id.total}"
										   readonly></td>
								<td style="font-weight: bold">
									<input type="number" name="porcent_{$item.quote_id.id}" id="porcent_{$item.quote_id.id}"
										   step="1" class="largeInput improvePrice" style="width: 30% !important;"
											data-quote-id = "{$item.quote_id.id}" data-initial-price = "{$item.quote_id.total}"
									/>%</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
					<hr>
				</div>
				<div class="grid_16">
					<div class="formLine" style=" width:100%;display: inline-block;">
						<div style="width:100%;float:left"> Comentarios</div>
						<div style="width:100%;float: left;">
							<textarea class="largeInput" name="comment" id="comment"></textarea>
						</div>
					</div>
				</div>
				<hr>
			</div>
		</fieldset>
		<div style="clear:both"></div>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loader"/>
			</div>
			<div class="actionsChild">
				<a  href="javascript:;" class="button_grey spanSendValidate">
					<span>Guardar y validar</span>
				</a>
			</div>
		</div>
	</form>

</div>
