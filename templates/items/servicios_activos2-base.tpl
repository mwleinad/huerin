{foreach from=$servicios item=item key=key}
	<tr>
		<td align="center">{$item.servicioId}</td>  
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
		<td align="center">
    		
		</td>
	</tr>
{foreachelse}
<tr><td colspan="10" align="center">No se encontr&oacute; ning&uacute;n registro. Realiza una nueva busqueda y asegurate que la Razon Social tenga servicios agregados.</td></tr>
{/foreach}
