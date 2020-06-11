<tr id="1">
	<td align="center" class="id">{$item.name}</td>
	<td align="center">{$item.subsector}</td>
	<td align="center">{$item.sector}</td>
	<td align="center">
		{if in_array(274,$permissions)|| $User.isRoot}
			<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanAdd" id="{$item.id}" title="Editar"/>
		{/if}
		{if in_array(275,$permissions)|| $User.isRoot}
			<img src="{$WEB_ROOT}/images/icons/delete.png" class="spanDelete" id="{$item.id}" title="Eliminar"/>
		{/if}
	</td>
</tr>

