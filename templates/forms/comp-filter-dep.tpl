<select name="departamentoId" id="departamentoId" {if $class neq ''}class="{$class}"{else}class="largeInput"{/if} >
    {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador' ||($User.roleId eq 28||$User.roleId eq 19)||$unlimited}
            <option value="" >Todos...</option>
    {/if}
    {foreach from=$departamentos item=depto}
        {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador' || $User.departamentoId eq $depto.departamentoId ||($depto.departamentoId eq 24 && $User.roleId eq 8)||($User.roleId eq 28||$User.roleId eq 19) ||$unlimited}
            <option value="{$depto.departamentoId}" >{$depto.departamento}</option>
        {/if}
    {/foreach}
</select>