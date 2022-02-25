<div id="divForm">
	<form id="frm-update-relation" name="frm-update-relation" method="post"  onsubmit="return false">
		<input type="hidden" id="type" name="type" value="updateRelationToContract"/>
		<input type="hidden" id="key_concepto" name="key_concepto" value="{$key_concepto}"/>
		<fieldset>
			<div class="container_16">
				<div class="grid_16">
				<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
					<thead>
					<tr>
						<th colspan="6" style="font-weight: bold; text-align: center">Seleccione a que empresa debe estar vinculado el concepto.</th>
					</tr>
					<tr>
						<th></th>
						<th><strong>Empresa</strong></th>
						<th><strong>Nombre servicio</strong></th>
						<th><strong>Inicio de operaciones</strong></th>
						<th><strong>Inicio de facturación</strong></th>
						<th><strong>Estatus actual</strong></th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$servicios key=key item=item}
						<tr>
							<td>
								<input type="radio"
									   {if $item.servicioId eq $current.servicioId}
										 checked
									   {/if}
									   name="correct_service_id"
									   value="{$item.servicioId}">

							</td>
							<td>{$item.name}</td>
							<td>{$item.nombreServicio}</td>
							<td>{$item.fio}</td>
							<td>{$item.fif}</td>
							<td>{$item.estatus}
								{if $item.status eq 'bajaParcial'}
									<br><strong>Fecha de ultimo workflow: </strong>{$item.lastDateWorkflow}
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
					<hr>
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
				<a  href="javascript:;" class="button_grey spanUpdateRelation">
					<span>Actualizar</span>
				</a>
			</div>
		</div>
	</form>

</div>
