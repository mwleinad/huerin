<tr class="{$clase}">
    <td  style="width:10%;">{if $res.no_inventario eq ''}En bodega{else}{$res.no_inventario}{/if}</td>
    <td  style="width:5%;">{if $res.tipo_recurso eq "equipo_computo"}Equipo de computo / {$res.tipo_equipo|ucfirst}{else}{$res.tipo_recurso|ucfirst}{/if}
    </td>
    <td  style="width:10%;">{if $res.tipo_recurso eq 'Computadora'}{$res.tipo_equipo}{elseif $res.tipo_recurso eq 'Accesorios'}{$res.tipo_dispositivo}{else}{$res.tipo_software}{/if}</td>
    <td  style="width:10%;">{$res.marca}</td>
    <td  style="width:10%;">{$res.modelo}</td>
    <td  style="width:10%;">{$res.fecha_compra|date_format:'%d-%m-%Y'}</td>
    <td  style="width:10%;">{$res.fecha_alta|date_format:'%d-%m-%Y'}</td>
    <td  style="width:10%;">{if $res.status == 'null'}{else}{$res.status}{/if}</td>
    <td  style="width:10%;">{$res.usuario_alta}</td>
    <td style="width:10%;">
        <div style="min-width: 80px;float: left">
            {if $res.tipo_recurso eq "Computadora"}
                <a href="javascript:;" class="spanDownloadAcuse" data-id="{$res.office_resource_id}" data-type="generateResponsiva" title="Descargar responsiva">
                    <img src="{$WEB_ROOT}/images/icons/doc.png"/>
                </a>
            {/if}
            {if in_array(256,$permissions)|| $User.isRoot}
                <a target="_blank" href="{$WEB_ROOT}/resource-office-pdf&id={$res.office_resource_id}&type=view">
                    <img src="{$WEB_ROOT}/images/pdf_icon.png" class="" id="{$res.office_resource_id}" border="0" title="Ver reporte" width="16"/>
                </a>
            {/if}
            {if in_array(253,$permissions)|| $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" title="Editar" id="{$res.office_resource_id}"/>
            {/if}
            {if in_array(254,$permissions)|| $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" title="Eliminar" id="{$res.office_resource_id}"/>
            {/if}
            {if (in_array(258,$permissions)|| $User.isRoot) && $res.tipo_recurso eq "Computadora"}
                <a href="{$WEB_ROOT}/responsables-resource/id/{$res.office_resource_id}" onclick="return parent.GB_show('Responsables activos e inactivos', this.href,500,970) "  class="spanAll">
                    <img src="{$WEB_ROOT}/images/icon_users.png" title="Ver responsables" width="16"/>
                </a>
            {/if}
            {if in_array(263,$permissions)|| $User.isRoot}
                <a href="{$WEB_ROOT}/upkeeps-resource/id/{$res.office_resource_id}" onclick="return parent.GB_show('Mantenimientos realizados', this.href,500,970) " title="Mantenimientos" class="spanAll">
                    <img src="{$WEB_ROOT}/images/icons/config.gif"/>
                </a>
            {/if}
        </div>

    </td>
</tr>
