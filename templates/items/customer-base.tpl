{foreach from=$customers item=item key=key}
	<tr>
        {if in_array(190,$permissions) || $User.isRoot}
		    <td align="center">{$item.customerId}</td>
        {/if}
        {if in_array(191,$permissions) || $User.isRoot}
		    <td align="center">{$item.nameContact|wordwrap:20:"<br />\n":TRUE}</td>
        {/if}
        {if in_array(192,$permissions) || $User.isRoot}
            <td align="center">{$item.phone|wordwrap:20:"<br />\n":TRUE}</td>
        {/if}
        {if in_array(193,$permissions) || $User.isRoot}
            <td align="center">{$item.email|wordwrap:20:"<br />\n":TRUE}</td>
        {/if}
        {if in_array(194,$permissions) || $User.isRoot}
            <td align="center">{$item.password|wordwrap:20:"<br />\n":TRUE}</td>
        {/if}
        {if in_array(195,$permissions) || $User.isRoot}
            <td align="center" class="id">
            {if $item.active == 1 || $item.active ==0}
                {if $item.contracts.0.fake == 1}
                            0
                {else}
                    {$item.totalContracts}
                {/if}
                {if $item.contracts|count > 0 && (in_array(62,$permissions)|| $User.isRoot)}
                    <a href="{$WEB_ROOT}/contract/id/{$item.customerId}">
                    <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Razones Sociales"/>
                    </a>
                {/if}
                {if in_array(61,$permissions) || $User.isRoot}
                    <a href="{$WEB_ROOT}/contract-new/id/{$item.customerId}">
                    <img src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Razon Social"/>
                    </a>
                {/if}
                {if $item.contracts|count > 0 && (in_array(62,$permissions)|| $User.isRoot)}
                <br />
                    <a href="{$WEB_ROOT}/contract/id/{$item.customerId}-activos">{$item.contractsActivos} Act.</a>
                    <br />
                    <a href="{$WEB_ROOT}/contract/id/{$item.customerId}-inactivos">{$item.contractsInactivos} Inact.</a>
                {/if}
            {else}
                N/A
            {/if}
            </td>
        {/if}
        {if in_array(196,$permissions) || $User.isRoot}
            <td align="center">{if $item.active}Si{else}No{/if}</td>
        {/if}
        {if in_array(197,$permissions) || $User.isRoot}
            <td align="center">{$item.fechaAlta|date_format:"d-m-Y"}</td>
        {/if}
        {if in_array(198,$permissions) || $User.isRoot}
            <td align="center">
            {if $item.active == 1}
                {if in_array(59,$permissions)|| $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.customerId}" title="Desactivar"/>
                {/if}
                {if (in_array(58,$permissions)|| $User.isRoot)}
                    <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.customerId}" title="Editar"/>
                {/if}
                {if (in_array(211,$permissions)|| $User.isRoot) && $item.doBajaTemporal}
                    <img src="{$WEB_ROOT}/images/icons/iconDown.png" class="spanDown bajaTemporal" id="{$item.customerId}" title="Baja temporal de servicios"/>
                {/if}
                {if (in_array(212,$permissions)|| $User.isRoot) && $item.haveTemporal}
                    <img src="{$WEB_ROOT}/images/icons/iconUp.png" class="spanDown reactiveTemp" id="{$item.customerId}" title="Reactivar servicios"/>
                {/if}
            {else}
                {if (in_array(93,$permissions)&& in_array(91,$permissions))|| $User.isRoot}
                    <img src="{$WEB_ROOT}/images/icons/activate.png" class="spanDelete" id="{$item.customerId}" title="Activar"/>
                {/if}
             {/if}
            </td>
        {/if}
	</tr>
{foreachelse}
<tr><td colspan="9" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach} 
