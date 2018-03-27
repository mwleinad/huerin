{foreach from=$documentos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.documentoId}</td>
		<td align="center">{$item.nombre}</td>
		<td align="center">
            {if in_array(75,$permissions)||$User.isRoot}
			<a href="{$WEB_ROOT}/download.php?file={$item.filePath}">{$item.path}</a>
			{/if}
		</td>
		<td class="act">
        {if in_array(74,$permissions)||$User.isRoot}
		<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.documentoId}"/></span> 
        {/if}
      {*}<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEditDocumento" id="{$item.documentoId}"/>{*}
		</td>
	</tr>
{/foreach}
