{foreach from=$archivos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.name}</td>
		<td align="center"><a href="{$WEB_ROOT}/download.php?file={$item.path}">Descargar</a></td>  
		<td class="act">
        {if $canEdit}
		<img src="{$WEB_ROOT}/images/b_dele.png" onclick="DeleteArchivoPopup({$item.departamentosArchivosId}, {$id})" id="{$item.departamentosArchivosId}"/>
      	<img src="{$WEB_ROOT}/images/b_edit.png" onclick="EditArchivoPopup({$item.departamentosArchivosId})" title="Editar" id="{$item.departamentosArchivosId}"/>
        {/if}
		</td>    
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
