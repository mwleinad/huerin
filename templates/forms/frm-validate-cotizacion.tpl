<div id="divForm">
	<form id="frmMultiple" name="frmMultiple" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="updateQuote"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16">
					<p>Lista de servicios cotizados</p>
				</div>
				<div class="grid_16">
					<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
						<thead>
						<tr>
							<th>Servicios en cotizacion</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$services key=key item=item}
							<tr>
								<td>{$item.quote_id.service_name}</td>
								<td><input class="medium" value="{$item.quote_id.total}"></td>
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
				<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loader"/>
			</div>
			<div class="actionsChild">
				<a  href="javascript:;" class="button_grey spanGenerate">
					<span>Generar cotizacion</span>
				</a>
			</div>
		</div>
	</form>

</div>
