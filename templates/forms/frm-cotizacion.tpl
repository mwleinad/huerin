<div id="divForm">
	<form id="frmMultiple" name="frmMultiple" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="saveGenerate"/>
		<input type="hidden" id="id" name="id" value="{$post.id}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16">
				<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
					<thead>
					<tr>
						<th colspan="3" style="font-weight: bold; text-align: center">Servicios a cotizar</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$services key=key item=item}
						<tr>
							<td><input type="checkbox" name="selected_service[]" value="{$item.id}"></td>
							<td>{$item.name}</td>
							<td>
								{if $item.quote_id}
									<a href="javascript:;" class="spanDownloadQuote" data-service="{$item.service_id}" data-quote="{$item.quote_id}" data-company="{$item.company_id}" data-type="download_normal_quote">
										<img src="{$WEB_ROOT}/images/icons/downFile.png" title="Descargar cotizacion"/>
									</a>
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
					<hr>
				</div>
				<div class="grid_16">
					<div class="formLine" style=" width:100%;display: inline-block;">
						<div style="width:30%;float:left"> Comentarios</div>
						<div style="width:70%;float: left;">
							<textarea class="largeInput" name="comment" id="comment"></textarea>
						</div>
					</div>
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
