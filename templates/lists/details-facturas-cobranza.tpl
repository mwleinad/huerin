{include file="{$DOC_ROOT}/templates/items/details_facturas_header.tpl" clase="Off"}
{foreach from=$results.items item=fact key=key}
	{if $key%2 == 0}
		{include file="{$DOC_ROOT}/templates/items/details_facturas_base.tpl" clase="Off"}
	{else}
		{include file="{$DOC_ROOT}/templates/items/details_facturas_base.tpl" clase="On"}
	{/if}
{foreachelse}
<div align="center">No existen facturas en estos momentos.</div>
{/foreach}
