{foreach from=$contracts item=item key=key}
    {if $User.roleId < 4}
	<tr id="1">
        <td align="center" class="id">{$item.contractId}</td>
		<td align="center" class="id">{$item.name}</td>
		<td align="center" class="id">{$item.type}</td>
    <td align="center">{$item.rfc}</td>
    <td align="left">
    {foreach from=$departamentos item=depto}
    	{assign var="idDepto" value="{$depto.departamentoId}"}
        
        {if $item.responsables2.$idDepto}
    		<b>{$depto.departamento}:</b> {$item.responsables.$idDepto}
        	<br />
        {/if}
    {/foreach}
    </td>
    {if $User.roleId < 4 && (in_array(86,$permissions) or in_array(85,$permissions))} <!-- inicio col servicios -->
    <td align="center">
    {if $item.activo == 'Si'}
        {$item.noServicios}
        {if $item.noServicios > 0}
            {if in_array(86,$permissions)}
                <a href="{$WEB_ROOT}/services/id/{$item.contractId}" onclick="return parent.GB_show('Servicios de Razon Social', this.href,500,970) ">
                    <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Servicios"/>
                </a>
            {/if}
        {/if}
        {if in_array(85,$permissions)}
            <img class="spanAddService" id="{$item.contractId}" src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Servicios"/>
        {/if}
    {else}
        N/A
    {/if}
    </td>
    {/if} <!--fin col servicios-->
	<td align="center">
     {if $User.roleId < 3}
        {if $item.activo == 'Si'}
            {if in_array(65,$permissions)}
                <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.contractId}" title="Desactivar"/>
            {/if}
        {else}
            {if in_array(65,$permissions)}
                <img src="{$WEB_ROOT}/images/icons/activate.png" class="spanDelete" id="{$item.contractId}" title="Activar"/>
            {/if}
        {/if}
        {if in_array(64,$permissions) && $item.activo == 'Si'}
            <a href="{$WEB_ROOT}/contract-edit/contId/{$item.contractId}">
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.contractId}" title="Editar" border="0"/></a>
        {/if}
     {/if}
        {if in_array(66,$permissions)}
           <a href="{$WEB_ROOT}/contract-view/contId/{$item.contractId}">
            <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Detalles" />
           </a>
        {/if}

	</td>
	</tr>
    {else} <!-- else if $User.roleId<4 -->
        {if $item.activo == 'Si'}
        <tr id="1">
            <td align="center" class="id">{$item.name}</td>
            <td align="center" class="id">{$item.type}</td>
            <td align="center">{$item.rfc}</td>
            <td align="left">
                {foreach from=$departamentos item=depto}
                    {assign var="idDepto" value="{$depto.departamentoId}"}

                    {if $item.responsables2.$idDepto}
                        <b>{$depto.departamento}:</b> {$item.responsables.$idDepto}
                        <br />
                    {/if}
                {/foreach}
            </td>
            {if $User.roleId < 4 && (in_array(86,$permissions) or in_array(85,$permissions))}
            <td align="center">
                {if $item.activo == 'Si'}
                    {$item.noServicios}
                    {if $item.noServicios > 0}
                        {if in_array(86,$permissions)}
                            <a href="{$WEB_ROOT}/services/id/{$item.contractId}" onclick="return parent.GB_show('Servicios de Razon Social', this.href,500,970) ">
                            <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Servicios"/></a>
                        {/if}
                    {/if}
                    {if in_array(85,$permissions)}
                        <img class="spanAddService" id="{$item.contractId}" src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Servicios"/>
                    {/if}
                {else}
                    N/A
                {/if}
            </td>
            {/if}
            </td>
            <td align="center">
            {if $User.roleId < 3}
                {if $item.activo == 'Si'}
                    {if in_array(65,$permissions)}
                        <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.contractId}" title="Desactivar"/>
                    {/if}
                {else}
                    {if in_array(65,$permissions)}
                        <img src="{$WEB_ROOT}/images/icons/activate.png" class="spanDelete" id="{$item.contractId}" title="Activar"/>
                    {/if}
                {/if}
                {if in_array(64,$permissions) && $item.activo == 'Si'}
                    <a href="{$WEB_ROOT}/contract-edit/contId/{$item.contractId}">
                        <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.contractId}" title="Editar" border="0"/>
                    </a>
                {/if}
            {/if}
            {if in_array(66,$permissions)}
                <a href="{$WEB_ROOT}/contract-view/contId/{$item.contractId}">
                    <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Detalles" />
                </a>
            {/if}
            </td>
        </tr>
        {/if}
    {/if}
{foreachelse}
<tr><td colspan="6" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
