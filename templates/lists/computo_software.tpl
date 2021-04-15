{if count($listSoftware)}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
	<thead>
		<tr>
			<th>Software</th>
			<th></th>
		</tr>
	</thead>
<tbody>
	{foreach from=$listSoftware item=item key=key}
		{if !$item.deleteAction}
		<tr>
			<td>{$item.tipo_sofware|upper} {$item.marca} {$item.modelo} {$item.no_serie} {$item.no_licencia} {$item.codigo_activacion}</td>
			<td>
				<a href="javascript:;" title="Eliminar de equipo" class="spanDeleteSoftwareFromResource" data-type="deleteSoftwareFromResource" data-key="{$key}">
					<img src="{$WEB_ROOT}/images/icons/action_remove.gif">
				</a>
				{if $item.no_inventario neq ''}
					<a href="javascript:;" title="Baja definitiva de inventario" class="spanDeleteSoftwareFromStock" data-type="deleteSoftwareFromStock" data-key="{$key}">
						<img src="{$WEB_ROOT}/images/icons/delete.png">
					</a>
				{/if}
			</td>
		</tr>
		{/if}
	{/foreach}
</tbody>
</table>
{else}
<div align="center">No existen softwares relacionados</div>
{/if}
