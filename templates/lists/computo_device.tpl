{if count($listDevices)}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	<thead>
		<tr>
			<th>Dispositivo</th>
			<th>No. inventario</th>
			<th></th>
		</tr>
	</thead>
<tbody>
	{foreach from=$listDevices item=item key=key}
		{if !$item.deleteAction}
		<tr>
			<td>{$item.tipo_dispositivo|upper} {$item.marca} {$item.modelo} {$item.no_serie}</td>
			<td>{$item.no_inventario}</td>
			<td>
				<a href="javascript:;" title="Eliminar de equipo" class="spanDeleteFromResource" data-type="deleteFromResource" data-key="{$key}">
					<img src="{$WEB_ROOT}/images/icons/softdelete.png">
				</a>
				{if $item.no_inventario neq ''}
					<a href="javascript:;" title="Baja definitiva de inventario" class="spanDeleteFromStock" data-type="deleteFromStock" data-key="{$key}">
						<img src="{$WEB_ROOT}/images/icons/harddelete.png">
					</a>
				{/if}
			</td>
		</tr>
		{/if}
	{/foreach}
</tbody>
</table>
{else}
<div align="center">No existen dispositivos relacionados</div>
{/if}
