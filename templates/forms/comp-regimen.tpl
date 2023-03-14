<select name="{$nameId}" id="{$nameId}" class="smallInput" {if $actionChange}onchange="{$actionChange}"{/if}>
    <option value="">Seleccione....</option>
    {foreach from=$regimenes item=item}
        <option value="{$item.regimenId}" {if $currentRegimen == $item.regimenId} selected="selected" {/if}>{$item.nombreRegimen}</option>
    {/foreach}
</select>
