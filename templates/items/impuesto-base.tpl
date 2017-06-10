{foreach from=$resImpuesto item=item key=key}
	<tr id="1">
		<td align="center">{$item.impuestoNombre}</td>
		<td class="act" align="center">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.impuestoId}" title="Eliminar"/></span> <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.impuestoId}" title="Editar"/></a>
		</td>
	</tr>
{/foreach}
