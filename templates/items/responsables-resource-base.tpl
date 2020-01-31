{if $res.status eq 'Activo'}
<tr class="{$clase}">
    <td  style="width:35%;">{$res.nombre}</td>
    <td  style="width:20%;">{$res.tipo_responsable}</td>
    <td  style="width:20%;">{$res.fecha_entrega_responsable}</td>
    <td  style="width:10%;">{$res.status}</td>
    <td  style="width:25%;">{if $res.status eq "Baja"}{$res.fecha_fin_uso_responsable}{else}Vigente{/if}</td>
    <td  style="width: 10%">
        <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDeleteResponsable" title="Eliminar" id="{$key}"/>
    </td>
</tr>
{/if}
