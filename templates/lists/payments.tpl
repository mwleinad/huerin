{include file="{$DOC_ROOT}/templates/items/payments_header.tpl" clase="Off"}
{if count($post.payments)}
	{foreach from=$post.payments item=fact key=key}
    	{if $key%2 == 0}
			{include file="{$DOC_ROOT}/templates/items/payments_base.tpl" clase="Off"}
        {else}
			{include file="{$DOC_ROOT}/templates/items/payments_base.tpl" clase="On"}
        {/if}
	{/foreach}

               
 	{if count($comprobantes.pages)}
    {include file="{$DOC_ROOT}/templates/lists/pages_new.tpl" pages=$comprobantes.pages}
  {/if}
  
{else}
<div align="center">No existen pagos en estos momentos.</div>
{/if}