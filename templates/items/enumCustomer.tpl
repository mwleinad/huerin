{foreach from=$customers item=item key=key}
	<option value="{$item.customerId}" {if $info.customerId == $item.customerId}selected{/if}>{$item.name}</option>
{/foreach}
