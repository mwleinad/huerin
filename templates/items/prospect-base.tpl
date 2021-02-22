{foreach from=$results.items item=item key=key}
    <tr>
        <td align="center">{$item.name}</td>
        <td align="center">{$item.phone}</td>
        <td align="center">{$item.email}</td>
        <td align="center">{$item.observation}</td>
        <td align="center">
            <div style="text-align: center;">
                <a href="javascript:;" data-id="{$item.id}" data-type="openEditProspect" class="spanControlProspect" title="Editar">
                    <img src="{$WEB_ROOT}/images/icons/edit.gif"/>
                </a>
                <a href="{$WEB_ROOT}/company/id/{$item.id}" target="_blank" title="Ir a empresas">
                    <img src="{$WEB_ROOT}/images/icons/office-building.png"/>
                </a>
            </div>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="5" align="center">No se encontr&oacute; ning&uacute;n registro.</td>
    </tr>
{/foreach}
