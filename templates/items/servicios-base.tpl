{foreach from=$servicios item=item key=key}
	<tr id="1">
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
    <td align="center">{$item.formattedInicioFactura}</td>
	<td align="center">{$item.status}</td>
    {if in_array(87,$permissions) || in_array(88,$permissions)|| in_array(89,$permissions) || in_array(90,$permissions) || $User.isRoot}
		<td align="center">
		{if $item.status == 'activo'}
			{if in_array(88,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.servicioId}" title="Desactivar"/>
			{/if}
		{else}
			{if in_array(90,$permissions) || $User.isRoot}
				<img src="{$WEB_ROOT}/images/icons/activate.png" class="spanDelete" id="{$item.servicioId}" title="Activar"/>
			{/if}
		{/if}
		{if (in_array(87,$permissions)|| $User.isRoot) &&$item.status == 'activo'}
		<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.servicioId}" title="Editar"/>
		{/if}
		{if in_array(89,$permissions) || $User.isRoot}
		<img src="{$WEB_ROOT}/images/icons/calendar.png" class="" id="{$item.servicioId}" title="Historial de Cambios" onclick="Historial({$item.servicioId})"/>
		{/if}
		</td>
    {/if}
	</tr>
{foreachelse}
<tr><td colspan="10" align="center">No se encontr&oacute; ning&uacute;n registro. Realiza una nueva busqueda y asegurate que la Razon Social tenga servicios agregados.</td></tr>
{/foreach}
