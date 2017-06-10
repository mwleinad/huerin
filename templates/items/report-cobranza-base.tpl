{foreach from=$contracts item=item key=key}
	<tr id="1">
		<td align="center" class="id">{$item.contractId}</td>
        <td align="center">{$item.name}</td>
        <td align="center">{$item.tipo}</td>
        <td align="center">
        {if $item.stOblig == 1} Cumplidas
        {else if $item.stOblig == 2} Por Cumplir
        {else if $item.stOblig == 3} Sin Obligaciones
        {/if}
        </td>      
        <td align="center">{$item.status}</td>        
        <td align="center">
        <a href="{$WEB_ROOT}/contract-docs/contId/{$item.contractId}">
        <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Documentaci&oacute;n" />
        </a>
        </td>
		<td align="center">
            <a href="{$WEB_ROOT}/contract-view/contId/{$item.contractId}">
            <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Detalles" />
            </a>
		</td>
	</tr>
{foreachelse}
<tr><td colspan="7" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}