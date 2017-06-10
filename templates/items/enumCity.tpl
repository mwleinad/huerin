{foreach from=$cities item=item key=key}
	<option value="{$item.cityId}" {if $info.cityId == $item.cityId}selected{/if}>{$item.name|upper}</option>
{/foreach}
