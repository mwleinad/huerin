{foreach from=$resRegimen.items item=item key=key}
	<tr>
		<td align="center">{$item.nombreRegimen}</td>
		<td align="center">{$item.tipoDePersona}</td>
		<td class="act" align="center">
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.regimenId}" title="Eliminar"/></span>
             <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.regimenId}" title="Editar"/></a>
		</td>
	</tr>
{/foreach}
