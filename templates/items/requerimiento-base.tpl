{foreach from=$requerimientos item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.requerimientoId}</td>
		<td align="center">{$item.nombre}</td>
		<td align="center">
            {if in_array(84,$permissions)||$User.isRoot}
				<a href="{$WEB_ROOT}/download.php?file={$item.filePath}">{$item.path}</a>
			{/if}
		</td>
		<td class="act">
			{if in_array(83,$permissions)||$User.isRoot}
				<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.requerimientoId}"/></span>
			{/if}
      	{*}
      		<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEditRequirimiento" id="{$item.requerimientoId}"/>
        {*}
		</td>
	</tr>
{/foreach}
