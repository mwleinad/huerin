{if $page != "product-add" && $page != "obs-add"}
<div id="container">
	{include file="templates/{$includedTpl}.tpl"}	
</div> 
{else}
	{include file="templates/{$includedTpl}.tpl"}
{/if}
