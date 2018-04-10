{foreach from=$archivos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.archivoId}</td>
		<td align="center">{$item.descripcion}</td>
		<td align="center">{$item.date}</td>
		<td align="center">
            {if in_array(71,$permissions)||$User.isRoot}
				<a href="{$WEB_ROOT}/download.php?file={$item.filePath}">{$item.path}</a>
            {/if}
		</td>
		<td class="act">
        {if in_array(70,$permissions)||$User.isRoot}
		<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.archivoId}"/>
        {/if}
        {if in_array(69,$permissions)||$User.isRoot}
      	<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" title="Editar fecha" id="{$item.archivoId}"/>
        {/if}
		</td>
	</tr>
{/foreach}
