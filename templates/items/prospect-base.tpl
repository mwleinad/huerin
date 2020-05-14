{foreach from=$results.items item=item key=key}
    <tr>
        <td align="center">{$item.name}</td>
        <td align="center">{$item.business_activity}</td>
        <td align="center">{$item.nombreRegimen}</td>
        <td align="center">{$item.constitution_date|date_format:'%d-%m-%Y'}</td>
        <td align="center">{if $item.is_new_company}Si{else}No{/if}</td>
        <td align="center">
            <div style="text-align: center;">
                <a href="{$WEB_ROOT}/prospect-offer-pdf/id/{$item.id}" class="spanAll spanGeneratePdf" title="Generar cotización" target="_blank">
                    <img src="{$WEB_ROOT}/images/icons/pdf.png"/>
                </a>
            </div>
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="6" align="center">No se encontr&oacute; ning&uacute;n registro.</td>
    </tr>
{/foreach}