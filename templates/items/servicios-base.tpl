{foreach from=$servicios item=item key=key}
	<tr id="1">
		<td align="center" class="id">
			<input type="checkbox" name="servs" class="checkServs" data-row='{$item.dataJson}' id="{$item.servicioId}">
		</td>
		<td align="center" class="id">{$item.name}</td>
		<td align="center" class="id">{$item.nombreServicio}</td>
		<td align="center">
        {if $item.mostrarCostoVisual}
        	${$item.costoVisual|number_format:2}
        {else}
        	N/A
        {/if}
        </td>  
		<td align="center">
        	${$item.costo|number_format:2}
    </td>  
	<td align="center">{$item.formattedInicioOperaciones}</td>
    <td align="center">{if $item.formattedInicioFactura != '00//0000' && $item.formattedInicioFactura != '//'}{$item.formattedInicioFactura}{/if}</td>
	<td align="center">
		{if $item.status == 'activo'}
		  {assign var='colorStatus' value='#02592c'}
		{elseif $item.status == 'baja'}
		  {assign var='colorStatus' value='red'}
		{elseif $item.status == 'bajaParcial'}
			{assign var='colorStatus' value='#ba8202'}
		{else}
			{assign var='colorStatus' value='grey'}
		{/if}

		<span style="background: {$colorStatus};
				color:#ffffff;
				font-weight: bold;
				padding: 3px;
				border-radius:2px;
				font-size: .65rem;
				min-width: 200px">{$item.estado}</span>

		{if $item.status == 'bajaParcial'}<span style="background: #ba8202; color:#ffffff;font-weight: bold;padding: 3px;border-radius: 2px;
		font-size: .65rem; margin-top: 3px; display:inline-block">Desde: {$item.formattedDateLastWorkflow}</span>{/if}
	</td>
    {if in_array(87,$permissions) || in_array(88,$permissions)|| in_array(89,$permissions) || in_array(90,$permissions) || $User.isRoot}
		<td align="center">
		{if $item.status == 'activo' || $item.status == 'readonly'}
			{if in_array(88,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanActivate" id="{$item.servicioId}" title="Baja definitiva" data-message="desactivar" />
				<img src="{$WEB_ROOT}/images/icons/iconDown.png" class="spanDown" id="{$item.servicioId}" title="Baja temporal" />
			{/if}
		{else}
			{if in_array(90,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/icons/activate.png" class="spanActivate" id="{$item.servicioId}" title="Activar" data-message="reactivar"/>
			{/if}
		{/if}
		{if (in_array(87,$permissions)|| $User.isRoot) && ($item.status == 'activo' || $item.status == 'readonly')}
		<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.servicioId}" title="Editar"/>
		{/if}
		{if in_array(89,$permissions) || $User.isRoot}
		<img src="{$WEB_ROOT}/images/icons/calendar.png" class="spanHistory" id="{$item.servicioId}" title="Historial de Cambios" onclick="Historial({$item.servicioId})"/>
		{/if}
		</td>
    {/if}
	</tr>
{foreachelse}
<tr><td colspan="10" align="center">No se encontr&oacute; ning&uacute;n registro. Realiza una nueva busqueda y asegurate que la Razon Social tenga servicios agregados.</td></tr>
{/foreach}
