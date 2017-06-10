{foreach from=$steps item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.nombreStep}</td>
		<td align="center">{$item.descripcion}</td>  
		<td align="center">{$item.countTasks}
    {if $item.countTasks > 0}
	    <span style="cursor:pointer" onclick="ToogleTasks({$item.stepId})" id="spanStepId-{$item.stepId}">[+]</span>
    {/if}
    </td>  
		<td align="center">        	
      	<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.stepId}" title="Desactivar"/> 
        <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.stepId}" title="Editar"/>
        <img src="{$WEB_ROOT}/images/icons/task.png" class="spanAddTask" id="{$item.stepId}" title="Agregar Tarea"/>
		</td>
	</tr>
    {if $item.countTasks > 0}
	<tr id="tasks-{$item.stepId}" style="display:none">
		<td align="center" class="id" valign="middle">Tareas</td>
		<td align="center" colspan="3">
    	<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
				{include file="{$DOC_ROOT}/templates/items/tasks-header.tpl"}
				<tbody>
				{include file="{$DOC_ROOT}/templates/items/tasks-base.tpl"}
				</tbody>
			</table>
    </td>  
	</tr>
    {/if}
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
