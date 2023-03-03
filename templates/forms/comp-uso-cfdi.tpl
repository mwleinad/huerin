{if $contractInfo}
<select name="claveUsoCfdi" id="claveUsoCfdi" class="smallInput">
    <option value="">Seleccione....</option>
    {foreach from=$usosCfdi item=item}
        <option value="{$item.c_UsoCfdi}" {if $contractInfo.claveUsoCfdi == $item.c_UsoCfdi} selected="selected" {/if}>{$item.descripcion}</option>
    {/foreach}
</select>
{else}
    <select name="claveUsoCfdi" id="claveUsoCfdi" class="smallInput">
        <option value="">Seleccione....</option>
        {foreach from=$usosCfdi item=item}
            <option value="{$item.c_UsoCfdi}">{$item.descripcion}</option>
        {/foreach}
    </select>
{/if}
