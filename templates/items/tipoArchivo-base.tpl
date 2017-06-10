{foreach from=$resTipoArchivo item=item key=key}
	<tr id="1">
		<td align="center">{$item.descripcion}</td>
		<td class="act" align="center">
        {*}
			<img src="{$WEB_ROOT}/images/b_dele.png" class="spanDelete" id="{$item.tipoArchivoId}" title="Eliminar"/>
        {*}
            
            <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.tipoArchivoId}" title="Editar"/></a>
		</td>
	</tr>
{/foreach}
