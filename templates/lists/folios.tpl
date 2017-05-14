{include file="{$DOC_ROOT}/templates/items/folio_header.tpl" clase="Off"}
{if count($folios)}
	{foreach from=$folios item=folio key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/folio_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/folio_base.tpl" clase="On"}
        {/if}
	{/foreach}
  {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$pages}
{else}
<div align="center">No existen folios en estos momentos.</div>
{/if}