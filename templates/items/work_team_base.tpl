{foreach from=$work_teams item=item key=key}
	<tr id="1">
		<td>{$key + 1}</td>
		<td>{$item.name}</td>
		<td>
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelWorkTeam" id="{$item.id}" title="Eliminar"/>
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEditWorkTeam" id="{$item.id}" title="Editar"/>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="3"  style="text-align: center;">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
