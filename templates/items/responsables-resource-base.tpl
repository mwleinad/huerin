{if $res.status eq 'Activo'}
<tr class="{$clase}">
    <td  style="width:35%;">{$res.nombre}</td>
    <td  style="width:20%;">{$res.tipo_responsable}</td>
    <td  style="width:20%;">{$res.fecha_entrega_responsable}</td>
    <td  style="width:10%;">{$res.status}</td>
    <td  style="width:25%;">{if $res.status eq "Baja"}{$res.fecha_fin_uso_responsable}{else}Vigente{/if}</td>
    <td  style="width: 15%">
        <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDeleteResponsable" title="Eliminar" id="{$key}"/>
        {if $res.responsiva_root}
            <a href="{$WEB_ROOT}{$res.responsiva_root}" title="Ver responsiva" target="_blank" class="spanAll" id="{$key}">
                <img src="{$WEB_ROOT}/images/icons/pdf.png">
            </a>
        {/if}
    </td>
</tr>
{/if}
