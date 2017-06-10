{foreach from=$resObligacion item=item key=key}
	<tr id="1">
		<td align="center">{$item.obligacionNombre}</td>
		<td class="act" align="center">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.obligacionId}" title="Eliminar"/></span> <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.obligacionId}" title="Editar"/></a>
		</td>
	</tr>
{/foreach}
