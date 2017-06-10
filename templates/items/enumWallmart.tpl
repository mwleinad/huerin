{foreach from=$wallmarts item=item key=key}
	<option value="{$item.wallmartId}" {if $info.wallmartId == $item.wallmartId}selected{/if}>{$item.name}</option>
{/foreach}
