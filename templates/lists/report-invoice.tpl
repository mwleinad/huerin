{include file="{$DOC_ROOT}/templates/items/report_invoice_header.tpl" clase="Off"}
<tbody>
{foreach from=$comprobantes.items item=fact key=key}
    {if $key%2 == 0}
        {include file="{$DOC_ROOT}/templates/items/report_invoice_base.tpl" clase="Off"}
    {else}
        {include file="{$DOC_ROOT}/templates/items/report_invoice_base.tpl" clase="On"}
    {/if}
{foreachelse}
    <tr>
        <td colspan="12">No se encontraron resultados</td>
    </tr>
{/foreach}
</tbody>
</table>