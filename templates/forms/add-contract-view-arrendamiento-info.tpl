<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<tbody id="tbInfoProyAR">
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha de env&iacute;o de la primera versi&oacute;n</td>
		<td align="left" class="tdPad"><i>{$infA.fechaEnvio}</i></td>
	</tr>    
    <tr>
		<td align="left" class="tdPad" width="40%">Propiedad acreditada</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.propAcreditada == 1} Si
        {elseif $infA.propAcreditada == 0} No
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">T&iacute;tulo de propiedad con datos de inscripci&oacute;n</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.titPropiedad == 1} Si
        {elseif $infA.titPropiedad == 0} No
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Dictamen</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.dictamen == "noElaborar"} Aun no se puede elaborar
        {elseif $infA.dictamen == "enProceso"} En proceso
        {elseif $infA.dictamen == "concluido"} Con dictamen concluido
        {/if}
        </i>
        </td>
	</tr>
    
    <tr>
		<td align="left" class="tdPad" width="40%">Plazo del arrendamiento + promesa (en a&ntilde;os)</td>
		<td align="left" class="tdPad">
        	<b>Arrendamiento:</b> <i>{$infA.plazoArrendamiento}</i>
            <br />
            <b>Promesa:</b> <i>{$infA.plazoPromesa}</i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Acta de entrega del inmueble</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.actaEntrega == 1} Enviada
        {elseif $infA.actaEntrega == 0} Pendiente
        {/if}
        </i>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Inmueble entregado</td>
		<td align="left" class="tdPad">
        <i>
        {if $infA.inmuebleEntregado == 1} Si
        {elseif $infA.inmuebleEntregado == 0} No
        {/if}
        
        {if $infA.inmuebleEntregado == 1}
        <br /><br />
        {$infA.fechaInmEnt}
        {/if}
        </i>
        </td>
	</tr>
</tbody>
</table>