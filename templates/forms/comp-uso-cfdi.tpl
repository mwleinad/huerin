<select name="{$nameId}" id="{$nameId}" class="smallInput">
    <option value="">Seleccione....</option>
    {foreach from=$usosCfdi item=item}
        <option value="{$item.c_UsoCfdi}" {if $currentUsoCfdi== $item.c_UsoCfdi} selected="selected" {/if}>{$item.descripcion}</option>
    {/foreach}
</select>
