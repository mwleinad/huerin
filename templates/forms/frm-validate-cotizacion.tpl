<div id="divForm">
	<form id="frmValidate" name="frmValidate" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="validateQuote"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16">
					<div class="grid_8">
						<p>Lista de servicios cotizados</p>
					</div>
					<div class="grid_8" style="text-align: center">
						<a href="javascript:;" class="button spanUnlockPrice">Editar costos</a>
					</div>
				</div>
				<div class="grid_16">
					<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
						<tbody>
						{foreach from=$services key=key item=item}
							<tr>
								<td><input type="checkbox" class="largeInput" name="list_quotes[]"   value="{$item.quote_id.id}" checked onclick="return false"></td>
								<td>{$item.quote_id.service_name}</td>
								<td><input class="largeInput inputPrice" name="price_{$item.quote_id.id}" id="price_{$item.quote_id.id}" value="{$item.quote_id.total}" readonly></td>
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
				<a  href="javascript:;" class="button_grey spanSendValidate">
					<span>Guardar y validar</span>
				</a>
			</div>
		</div>
	</form>

</div>
