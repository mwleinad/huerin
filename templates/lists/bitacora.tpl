{include file="{$DOC_ROOT}/templates/items/bitacora_header.tpl" clase="Off"}
<tbody>
{foreach from=$registros item=item key=key}
    {if $key%2 == 0}
        {include file="{$DOC_ROOT}/templates/items/bitacora_base.tpl" clase="Off"}
    {else}
        {include file="{$DOC_ROOT}/templates/items/bitacora_base.tpl" clase="On"}
    {/if}
    {foreachelse}
    <tr>
        <td colspan="10">No se encontraron resultados</td>
    </tr>
{/foreach}
</tbody>
</table>