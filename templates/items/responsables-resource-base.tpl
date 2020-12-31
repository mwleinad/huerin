<tr class="{$clase}">
    <td  style="width:35%;">{$res.nombre}</td>
    <td  style="width:20%;">{$res.tipo_responsable}</td>
    <td  style="width:20%;">{$res.fecha_entrega_responsable|date_format:'%d-%m-%Y'}</td>
    <td  style="width:10%;">{$res.status}</td>
    <td  style="width:25%;">{if $res.status eq "Baja"}{$res.fecha_liberacion_responsable|date_format:'%d-%m-%Y'}{else}Vigente{/if}</td>
    <td>
        <div style="min-width: 40px">
            {if $res.status eq 'Activo'}
                {if in_array(260,$permissions)|| $User.isRoot}
                    <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanAll spanEdit" title="Editar" id="{$res.responsable_resource_id}" data-resource="{$res.office_resource_id}" />
                {/if}
                {if in_array(261,$permissions)|| $User.isRoot}
                    <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanAll spanDelete" title="Eliminar" id="{$res.responsable_resource_id}" data-resource="{$res.office_resource_id}" />
                {/if}
            {/if}
            {if in_array(262,$permissions)|| $User.isRoot}
                {if $res.responsiva_root && $res.status eq 'Activo'}
                        <a href="{$WEB_ROOT}{$res.responsiva_root}" title="Ver responsiva" target="_blank" class="spanAll">
                            <img src="{$WEB_ROOT}/images/icons/pdf.png">
                        </a>
                {/if}
                {if $res.status eq 'Baja' && $res.responsiva_baja_root neq ''}
                    <a href="{$WEB_ROOT}{$res.responsiva_baja_root}" title="Ver responsiva de baja" target="_blank" class="spanAll" >
                        <img src="{$WEB_ROOT}/images/icons/pdf.png">
                    </a>
                {/if}
            {/if}
        </div>
    </td>
</tr>

