{foreach from=$personals item=item key=key}
	<option value="{$item.personalId}" {if $info.personalId == $item.personalId}selected{/if}>{$item.name}</option>
{/foreach}
