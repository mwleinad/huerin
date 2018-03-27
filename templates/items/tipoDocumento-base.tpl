{foreach from=$resTipoDocumento item=item key=key}
	<tr id="1">
		<td align="center">{$item.nombre}</td>
		<td class="act" align="center">
            {if in_array(40,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.tipoDocumentoId}" title="Eliminar"/></span>
			{/if}
            {if in_array(39,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.tipoDocumentoId}" title="Editar"/></a>
			{/if}
		</td>
	</tr>
{/foreach}
