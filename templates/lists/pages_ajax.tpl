{if count($pages.numbers)}
{assign var="linktpl" value="{$DOC_ROOT}/templates/links/ajax.tpl"}
{assign var="next_page" value=$pages.next|substr:-1}
{assign var="prev_page" value=$pages.prev|substr:-1}
{assign var="total_pages" value=$pages.numbers|count}
<div class="pages">
	{if $pages.first}{include file=$linktpl link=$pages.first name="&laquo;"}{/if}
	{if $pages.prev}{include file=$linktpl page=$prev_page icon="&lt;" title='Pagina anterior'}{/if}
	{foreach from=$pages.numbers item=page key=key}
		{assign var="number_page" value=$page|substr:-1}
		{if $pages.current == $key}<span class="p">{$key}</span>{else}{include file=$linktpl page=$number_page icon=$key title=$key}{/if}
	{/foreach}
	{if $pages.next && $next_page < $total_pages}{include file=$linktpl page=$next_page icon="&gt;" title='Pagina siguiente'}{/if}
	{if $pages.last}{include file=$linktpl page=count($pages.numbers)-1 icon="&raquo;"}{/if}
</div>
{/if}