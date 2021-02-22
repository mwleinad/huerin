{foreach from=$results.items item=item key=key}
    <tr>
        <td align="center">{$item.name}</td>
        <td align="center">{if $item.is_new_company}Si{else}No{/if}</td>
        <td align="center">{$item.rfc}</td>
        <td align="center">{$item.email}</td>
        <td align="center">{$item.legal_representative}</td>
        <td align="center">{$item.observation}</td>
        <td align="center">
            <div style="text-align: center;">
                <a href="javascript:;" data-id="{$item.id}" data-type="openEditCompany" class="spanControlCompany" title="Editar">
                    <img src="{$WEB_ROOT}/images/icons/edit.gif"/>
                </a>
                <a href="javascript:;" title="Resolver encuesta">
                    <img src="{$WEB_ROOT}/images/icons/task.png"/>
                </a>
            </div>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="7" align="center">No se encontr&oacute; ning&uacute;n registro.</td>
    </tr>
{/foreach}
