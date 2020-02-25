<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
{include file="{$DOC_ROOT}/templates/items/emisores-header.tpl" clase="Off"}
{if count($results.items)}
	{foreach from=$results.items item=item key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/emisores-base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/emisores-base.tpl" clase="On"}
        {/if}
	{/foreach}
  {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$results.pages}
{else}
	<tr><td colspan="4"><div align="center">No existen emisores en estos momentos.</div></td></tr>

{/if}
</table>