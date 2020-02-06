<tr class="{$clase}">
    <td  style="width:10%;">{$res.nombre}</td>
    <td  style="width:30%;">{$res.descripcion|truncate:255:"...":true}</td>
    <td  style="width:5%;">{if $res.tipo_recurso eq "equipo_computo"}Equipo de computo{else}{$res.tipo_recurso|ucfirst}{/if}</td>
    <td  style="width:10%;">
        {foreach from=$res.responsables key=kr item=itemr}
           {$kr+1}.{$itemr.nombre}<br>
        {/foreach}
    </td>
    <td  style="width:10%;">{if $res.tipo_equipo eq ""}N/A{else}{$res.tipo_equipo|ucfirst}{/if}</td>
    <td  style="width:10%;">{$res.fecha_compra|date_format:'%d-%m-%Y'}</td>
    <td  style="width:10%;">{$res.fecha_alta|date_format:'%d-%m-%Y'}</td>
    <td  style="width:10%;">{$res.usuario_alta}</td>
    <td  style="width: 10%">
        <a target="_blank" href="{$WEB_ROOT}/resource-office-pdf&id={$res.office_resource_id}&type=view">
            <img src="{$WEB_ROOT}/images/pdf_icon.png" class="" id="{$res.office_resource_id}" border="0" title="Ver reporte" width="16"/>
        </a>
        <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" title="Editar" id="{$res.office_resource_id}"/>

        <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" title="Eliminar" id="{$res.office_resource_id}"/>
    </td>
</tr>
