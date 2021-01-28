<tr class="{$clase}">
    <td  style="width:10%;">{$res.nombre}</td>
    <td  style="width:15%; text-align: justify">{$res.descripcion|truncate:255:"...":true}<br>
        <b>Cuenta con nobreak:</b> {if $res.con_nobreak eq '1'}Si<br>{else}No<br>{/if}
        <b>Cuenta con hub usb:</b> {if $res.con_hubusb eq '1'}Si<br>{else}No<br>{/if}
        {if $res.no_licencia}<b>No. licencia:</b> {$res.no_licencia}<br>{/if}
        {if $res.no_serie}<b>No. serie:</b> {$res.no_serie}<br>{/if}
        {if $res.codigo_activacion}<b>Codigo activación:</b> {$res.codigo_activacion}<br>{/if}
    </td>
    <td  style="width:15%;">
        {foreach from=$res.upkeeps key=kkep item=itemKep}
            - <b>Fecha:</b> {$itemKep.upkeep_date}<br>
              <p style="text-align: justify"><b>Descripcion: </b>{$itemKep.upkeep_description}</p>
        {/foreach}
    </td>
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
    <td>
        <div style="min-width: 80px;float: left">
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
            {if in_array(258,$permissions)|| $User.isRoot}
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
