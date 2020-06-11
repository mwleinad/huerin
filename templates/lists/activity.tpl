{if count($data.items)}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
        {include file="{$DOC_ROOT}/templates/items/activity-header.tpl" clase="Off"}
<tbody>
	{foreach from=$data.items item=item key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/activity-base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/activity-base.tpl" clase="On"}
        {/if}
	{/foreach}
</tbody>
</table>
	<div class="pagination" style="text-align: right">
		{if count($data.pages)}
			{include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$data.pages}
		{/if}
	</div>

{else}
<div align="center">No existen roles en estos momentos.</div>
{/if}