<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
    {include file="{$DOC_ROOT}/templates/items/resource-office-header.tpl" clase="Off"}
    <tbody>
    {foreach from=$registros.items item=res key=key}
        {if $key%2 == 0}
            {include file="{$DOC_ROOT}/templates/items/resource-office-base.tpl" clase="Off"}
        {else}
            {include file="{$DOC_ROOT}/templates/items/resource-office-base.tpl" clase="On"}
        {/if}
    {foreachelse}
        <tr>
            <td colspan="" align="center">No existen responsables</td>
        </tr>
    {/foreach}
    </tbody>
</table>
