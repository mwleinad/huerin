{foreach from=$result item=item key=key}
	<tr id="1">
         <td align="center"  width="20%">{$item.descripcion}</td>
        <td align="center"  width="20%">{$item.modulo}</td>
        <td align="center"  width="20%">{$item.fechaSolicitud}</td>
        <td align="center"  width="20%">{$item.fechaEntrega}</td>
        <td align="center"  width="20%">{$item.fechaRevision}</td>
        <td align="center"  width="20%"><span style="{if $item.status eq 'pendiente'}color:red;{elseif $item.status eq 'finalizado'}color:green;{elseif $item.status eq 'revision'}color:#F59B25;{/if}">{$item.status|ucfirst}</span></td>
        <td align="justify" width="10%">
            {if $item.fileExist}
                <a href="{$WEB_ROOT}/download.php?file={$item.url}" target="_blank">
                   <img src="{$WEB_ROOT}/images/icons/down.png"  border="0" title="Descargar archivo" />
                </a>
             {/if}
         </td>
        <td align="justify" width="10%">

            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanChangeStatus">
                <img src="{$WEB_ROOT}/images/icons/change.png"  border="0" title="Cambiar estatus" />
            </a>
            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanComment">
                {if $item.whitComment}
                    <img src="{$WEB_ROOT}/images/icons/comment-blue.png"  border="0" title="Ver comentarios" />
                {else}
                    <img src="{$WEB_ROOT}/images/icons/comment-empty.png"  border="0" title="Agregar comentario" />
                {/if}
            </a>
            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanEdit">
                <img src="{$WEB_ROOT}/images/icons/edit.gif"  border="0" title="Editar" />
            </a>
            {if $item.status eq 'pendiente'}
                <a href="javascript:;" id="{$item.changeId}" class="spanAll spanDelete">
                    <img src="{$WEB_ROOT}/images/icons/delete.png"  border="0" title="Eliminar" />
                </a>
            {/if}
        </td>
	</tr>
{foreachelse}
	<tr><td align="center" colspan="6">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}