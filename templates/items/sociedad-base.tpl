{foreach from=$resSociedad.items item=item key=key}
	<tr id="1">
		<td align="center">{$item.nombreSociedad}</td>
		<td class="act" align="center">
            {if in_array(22,$permissions)}
			 <img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.sociedadId}" title="Eliminar" style="cursor:pointer"/></span>
			{/if}
            {if in_array(21,$permissions)}
			<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.sociedadId}" title="Editar" style="cursor:pointer"/></a>
			{/if}
		</td>
	</tr>
{/foreach}
