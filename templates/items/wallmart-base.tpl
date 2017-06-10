{foreach from=$wallmarts item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.wallmartId}</td>
		<td align="center">{$item.name}</td>
        <td align="center">{$item.phone}</td>
        <td align="center">{$item.email}</td>        
        <td align="center">{if $item.active}Si{else}No{/if}</td>
		<td align="center">        	
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.wallmartId}" title="Eliminar"/>   
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.wallmartId}" title="Editar"/>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="6" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
