<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
    {include file="{$DOC_ROOT}/templates/items/responsables-resource-header.tpl" clase="Off"}
    <tbody>
    {foreach from=$responsables item=res key=key}
        {if $key%2 == 0}
            {include file="{$DOC_ROOT}/templates/items/responsables-resource-base.tpl" clase="Off"}
        {else}
            {include file="{$DOC_ROOT}/templates/items/responsables-resource-base.tpl" clase="On"}
        {/if}
    {foreachelse}
        <tr>
            <td colspan="6" style="text-align: center">No existen responsables</td>
        </tr>
    {/foreach}
    </tbody>
</table>
