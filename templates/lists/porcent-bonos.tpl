
<table width="30%" cellpadding="0" cellspacing="0" id="box-table-a">
        {include file="{$DOC_ROOT}/templates/items/porcent-bonos-header.tpl" clase="Off"}
<tbody>
	{foreach from=$porcentajes item=porcent key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/porcent-bonos-base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/porcent-bonos-base.tpl" clase="On"}
        {/if}
	{foreachelse}
		<tr>
			<td colspan="3"><div align="center">No existen porcentajes dados de alta.</div></td>
		</tr>
    {/foreach}
</tbody>
</table>

