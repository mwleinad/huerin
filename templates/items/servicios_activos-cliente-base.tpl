{foreach from=$servicios item=item key=key}
	{if $item.activo == "Si"}
	<tr id="1">
		<td align="center">{$item.razonSocialName}</td>  
		<td align="center">{$item.nameContact}</td>  
		<td align="center" class="id">{$item.responsableCuentaName}</td>
		<td align="center">{$item.nombreServicio}</td>
		{if $User.roleId == 1}            
		<td align="center">{$item.costo}</td>  
    {/if}
		<td align="center">{$item.formattedInicioOperaciones}</td>  
		<td align="center">{$item.periodicidad}</td>  
		<td align="center">{$item.instancias|count}</td>  
{*
		<td align="center">
    	<select id="instanciaServicio{$item.servicioId}" name="instanciaServicio{$item.servicioId}" onchange="GoToWorkflow({$item.servicioId})">
      	<option value="0">Seleccione...</option>
        {foreach from=$item.instancias item=instancia}
        <option value="{$instancia.instanciaServicioId}">{$instancia.monthShow}</option>
        {/foreach}
      </select>
		</td>
*}
		{/if}
	</tr>
{foreachelse}
<tr><td colspan="4" align="center">No se encontr&oacute; ning&uacute;n registro. Realiza una nueva busqueda y asegurate que la Razon Social tenga servicios agregados.</td></tr>
{/foreach}
