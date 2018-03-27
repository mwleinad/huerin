{foreach from=$archivos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.name}</td>
		<td align="center">{$item.descargar}</td>  
		<td align="center">        	
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.docBasicId}" title="Eliminar"/>   
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.docBasicId}" title="Editarsssssss"/>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
