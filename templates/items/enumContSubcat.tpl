{foreach from=$subcategories item=item key=key}
	<option value="{$item.contSubcatId}" {if $info.contSubcatId == $item.contSubcatId}selected{/if}>{$item.name}</option>
{/foreach}
