{if count($roles)}
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
        {include file="{$DOC_ROOT}/templates/items/rol_header.tpl" clase="Off"}
<tbody>
	{foreach from=$roles item=rol key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/rol_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/rol_base.tpl" clase="On"}
        {/if}
	{/foreach}
</tbody>
</table>
  {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$pages}
{else}
<div align="center">No existen roles en estos momentos.</div>
{/if}