<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
    {if $User.tipoPersonal eq 'Socio'|| $User.tipoPersonal eq 'Coordinador' || $User.tipoPersonal eq 'Admin'}
	    {include file="{$DOC_ROOT}/templates/items/personal-header.tpl"}
    {else}
        {include file="{$DOC_ROOT}/templates/items/personal-limitado-header.tpl"}
    {/if}
<tbody>
    {if $User.tipoPersonal eq 'Socio'|| $User.tipoPersonal eq 'Coordinador' || $User.tipoPersonal eq 'Admin'}
	    {include file="{$DOC_ROOT}/templates/items/personal-base.tpl"}
    {else}
        {include file="{$DOC_ROOT}/templates/items/personal-limitado-base.tpl"}
    {/if}
</tbody>
</table>