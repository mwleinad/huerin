{foreach from=$item.tasks item=task key=key}
	<tr id="1">
		<td align="center" class="id">{$task.taskPosition}</td>
		<td align="center" class="id">{$task.nombreTask}</td>
		<td align="center" class="id">{if $task.effectiveDate}Desde {$task.effectiveDate|date_format:"%d-%m-%Y"}{/if}
			{if $task.finalEffectiveDate} al {$task.finalEffectiveDate|date_format:"%d-%m-%Y"}{/if}
		</td>
		<td align="center">Dia {$task.diaVencimiento}</td>
		<td align="center">{$task.prorroga} Dias</td>
    <td align="center" style="font-size: 10px;text-align: justify">
		{if $task.control !== ''}
			<span><b>Control Bueno:</b> {$task.control}</span>
		{/if}
		{if $task.control2 !== ''}
			<br><br>
			<span><b>Control Regular:</b> {$task.control2}</span>

		{/if}
		{if $task.control3 !== ''}
			<br><br>
			<span><b>Control Malo:</b> {$task.control3}</span>
		{/if}

	</td>
    <td align="center">
		<ul>
			{foreach from=$task.extensiones key=kext item=itemExt}
				<li style="font-size: 10px;text-align: left">{$itemExt.name}</li>
			{/foreach}
		</ul>
	</td>
		<td align="center">
        {if in_array(36,$permissions) || $User.isRoot}
      		<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanTaskDelete" id="{$task.taskId}" title="Desactivar"/>
		{/if}
		{if in_array(35,$permissions) || $User.isRoot}
        	<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanTaskEdit" id="{$task.taskId}" title="Editar"/>
		{/if}
		</td>
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
