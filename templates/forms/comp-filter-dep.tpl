<select name="departamentoId" id="departamentoId" {if $class neq ''}class="{$class}"{else}class="largeInput"{/if} >
    {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador'}
            <option value="" >Todos...</option>
    {/if}
    {foreach from=$departamentos item=depto}
        {if $User.tipoPers eq 'Admin' || $User.tipoPers eq 'Socio' || $User.tipoPers eq 'Coordinador' || $User.departamentoId eq $depto.departamentoId}
            <option value="{$depto.departamentoId}" >{$depto.departamento}</option>
        {/if}
    {/foreach}
</select>