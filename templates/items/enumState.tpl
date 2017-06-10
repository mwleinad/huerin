{foreach from=$states item=item key=key}
	<option value="{$item.stateId}" {if $info.stateId == $item.stateId}selected{/if}>{$item.name|upper}</option>
{/foreach}
