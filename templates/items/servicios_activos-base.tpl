{foreach from=$servicios item=item key=key}
	<tr id="1">
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
    	<select id="instanciaServicio{$item.servicioId}" name="instanciaServicio{$item.servicioId}" onchange="GoToWorkflow({$item.servicioId})" style="padding:2px">
      	<option value="0">Seleccione...</option>
        {foreach from=$item.instancias item=instancia}
        <option value="{$instancia.instanciaServicioId}">{$instancia.monthShow} 
          {if $instancia.status eq "activa"} 
            Activo
          {elseif $instancia.status eq "inactiva"}
            Inactivo
          {elseif $instancia.status eq "completa"}
            Completo
          {elseif $instancia.status eq "baja"}
            Baja
          {/if}
          </option>
        {/foreach}
      </select>        	
		</td>
	</tr>
{foreachelse}
<tr><td colspan="10" align="center">No se encontr&oacute; ning&uacute;n registro. Realiza una nueva busqueda y asegurate que la Razon Social tenga servicios agregados.</td></tr>
{/foreach}
