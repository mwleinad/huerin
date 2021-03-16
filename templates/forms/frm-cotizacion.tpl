<div id="divForm">
	<form id="frmMultiple" name="frmMultiple" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="saveGenerate"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
				<thead>
				<tr>
					<th></th>
					<th>Servicios en cotizacion</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$services key=key item=item}
					<tr>
						<td><input type="checkbox" name="selected_service[]" value="{$item.id}"></td>
						<td>{$item.name}</td>
						<td>
							{if $item.quote_id}
								<a href="javascript:;" class="spanDowloadQuote" data-service="{$item.service_id}" data-quote="{$item.quote_id}" data-company="{$item.company_id}">
									<img src="{$WEB_ROOT}/images/icons/downFile.png" title="Descargar cotizacion"/>
								</a>
							{/if}
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
				<a  href="javascript:;" class="button_grey spanGenerate">
					<span>Generar cotizacion</span>
				</a>
			</div>
		</div>
	</form>

</div>
