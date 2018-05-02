<select name="responsableCuenta" id="responsableCuenta"  {if $class neq ''}class="{$class}"{else}class="largeInput"{/if}>
    {if $User.tipoPersonal=="Socio" ||  $User.tipoPersonal=="Coordinador" ||  $User.tipoPersonal=="Admin"}
            <option value="0" selected="selected">Todos...</option>
    {/if}
    {foreach from=$personals item=personal}
        <option value="{$personal.personalId}" {if $search.responsableCuenta == $personal.personalId} selected="selected" {/if} >{$personal.name}</option>
{/foreach}
</select>