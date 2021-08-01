<div id="divForm">
	<form id="frmAcceptQuote" name="frmAcceptQuote" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="sendToMain"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16">
					<div class="grid_16">
						<p>Seleccione los servicios que fueron aceptados.</p>
					</div>
				</div>
				<div class="grid_16">
					<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
						<tbody>
						{foreach from=$services key=key item=item}
							<tr>
								<td><input type="checkbox" class="largeInput" name="quotes[]"   value="{$item.quote_id.id}"></td>
								<td>{$item.quote_id.service_name}</td>
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
				<a  href="javascript:;" class="button_grey spanSaveSendToMain">
					<span>Aceptar y finalizar cotizacion</span>
				</a>
			</div>
		</div>
	</form>

</div>
