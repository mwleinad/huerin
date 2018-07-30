{foreach from=$expedientes item=item key=key}
	<tr id="1">
		<td align="center">{$item.name}</td>
		<td class="act" align="center">
            {if in_array(185,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.expedienteId}" title="Eliminar"/></span>
			{/if}
            {if in_array(184,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.expedienteId}" title="Editar"/></a>
			{/if}
		</td>
	</tr>
{/foreach}
