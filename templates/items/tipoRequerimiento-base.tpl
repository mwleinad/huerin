{foreach from=$resTipoRequerimiento item=item key=key}
	<tr id="1">
		<td align="center">{$item.nombre}</td>
		<td class="act" align="center">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.tipoRequerimientoId}" title="Eliminar"/></span> <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.tipoRequerimientoId}" title="Editar"/></a>
		</td>
	</tr>
{/foreach}
