{if count($pages.numbers)}
{assign var="linktpl" value="{$DOC_ROOT}/templates/links/ajax.tpl"}
<div class="pages">
	{if $pages.first}{include file=$linktpl link=$pages.first name=$language.first}{/if}
	{if $pages.prev}{include file=$linktpl link=$pages.prev name=$language.prev}{/if}
	{foreach from=$pages.numbers item=page key=key}
	{if $pages.current == $key}<span class="p">{$key}</span>{else}{include file=$linktpl link=$page name=$key}{/if}
	{/foreach}
	{if $pages.next}{include file=$linktpl link=$pages.next name=$language.next}{/if}
	{if $pages.last}{include file=$linktpl link=$pages.last name=$language.last}{/if}
</div>
{/if}