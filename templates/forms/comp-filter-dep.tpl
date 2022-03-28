<select name="departamentoId" id="departamentoId" {if $class neq ''}class="{$class}"{else}class="largeInput"{/if} >
    {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador' ||($User.roleId eq 28||$User.roleId eq 19)||$unlimited || $User.level<=1}
            <option value="" >Todos...</option>
    {/if}
    {foreach from=$departamentos item=depto}
        {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador' || in_array($depto.departamentoId, $User.moreDepartament) ||($depto.departamentoId eq 24 && $User.roleId eq 8)||($User.roleId eq 28||$User.roleId eq 19) ||$unlimited || $User.level <= 1}
            <option value="{$depto.departamentoId}" >{$depto.departamento}</option>
        {/if}
    {/foreach}
</select>
