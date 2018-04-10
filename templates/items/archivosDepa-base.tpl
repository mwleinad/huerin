{foreach from=$archivos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.name}</td>
		<td align="center">{if (in_array(149,$permissions)&&in_array(150,$permissions)) || $User.isRoot}<a href="{$WEB_ROOT}/download.php?file={$item.path}">Descargar</a>{/if}</td>
		<td class="act">
            {if (in_array(149,$permissions)&&in_array(152,$permissions)) || $User.isRoot}
			<img src="{$WEB_ROOT}/images/b_dele.png" onclick="DeleteArchivoPopup({$item.departamentosArchivosId}, {$id})" id="{$item.departamentosArchivosId}"/>
			{/if}
            {if (in_array(149,$permissions)&&in_array(151,$permissions)) || $User.isRoot}
			<img src="{$WEB_ROOT}/images/b_edit.png" onclick="EditArchivoPopup({$item.departamentosArchivosId})" title="Editar" id="{$item.departamentosArchivosId}"/>
            {/if}
		</td>    
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
