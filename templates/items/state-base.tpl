{foreach from=$states item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.stateId}</td>
		<td align="center">{$item.name}</td>  
        <td align="center">
         <a href="{$WEB_ROOT}/city/stateId/{$item.stateId}">
        	<img src="{$WEB_ROOT}/images/icons/add.png" border="0" width="16" height="16" title="Agregar Municipios" />
        </a>
        </td>
		<td align="center">        	
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.stateId}" title="Eliminar"/>   
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.stateId}" title="Editar"/>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
