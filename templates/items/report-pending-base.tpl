{foreach from=$result item=item key=key}
	<tr id="1">
         <td align="center"  width="20%">{$item.descripcion}</td>
        <td align="center"  width="20%">{$item.modulo}</td>
        <td align="center"  width="20%">{$item.fechaSolicitud}</td>
        <td align="center"  width="20%">{$item.fechaEntrega}</td>
        <td align="center"  width="20%">{$item.fechaRevision}</td>
        <td align="center"  width="20%"><span style="{if $item.status eq 'pendiente'}color:red;{elseif $item.status}color:green{/if}">{$item.status|ucfirst}</span></td>
        <td align="justify" width="10%">
            {if $item.fileExist}
                <a href="{$WEB_ROOT}/download.php?file={$item.url}" target="_blank">
                   <img src="{$WEB_ROOT}/images/icons/down.png"  border="0" title="Descargar archivo" />
                </a>
             {/if}
         </td>
        <td align="justify" width="10%">
            {if $item.status eq 'pendiente'}
                <a href="javascript:;" id="{$item.changeId}" class="spanAll spanRealizado">
                    <img src="{$WEB_ROOT}/images/icons/action_check.gif"  border="0" title="Marcar como finalizado" />
                </a>
            {/if}
            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanComment">
                <img src="{$WEB_ROOT}/images/icons/comments.gif"  border="0" title="Agregar comentario" />
            </a>
            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanEdit">
                <img src="{$WEB_ROOT}/images/icons/edit.gif"  border="0" title="Editar" />
            </a>
            <a href="javascript:;" id="{$item.changeId}" class="spanAll spanDelete">
                <img src="{$WEB_ROOT}/images/icons/delete.png"  border="0" title="Eliminar" />
            </a>
        </td>
	</tr>
{foreachelse}
	<tr><td align="center" colspan="6">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}