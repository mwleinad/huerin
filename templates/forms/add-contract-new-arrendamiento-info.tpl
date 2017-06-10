<table width="100%" cellpadding="0" cellspacing="0" id="boxTable">
<tbody id="tbInfoProyAR">
    <tr>
		<td align="left" class="tdPad" width="40%">Fecha de env&iacute;o de la primera versi&oacute;n</td>
		<td align="left" class="tdPad">
        	<table width="180" cellspacing="0" cellpadding="0">
            <tr>
            	<td align="left">
                <input name="fechaEnvio" id="fechaEnvio" type="text" value="{$infA.fechaEnvio}" class="smallInput small" size="50" readonly="readonly" />                
                </td>
                <td align="left">
                <img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calendar-trigger2"  />
                <script type="text/javascript">
                Calendar.setup({
                    inputField : "fechaEnvio",
                    trigger    : "calendar-trigger2",
					dateFormat : "%d-%m-%Y",
					min: {$cal.min},
    				max: {$cal.max},
                    onSelect   : function() { this.hide() }
                });
                </script>
                </td>
            </tr>
            </table>
        </td>
	</tr>      
    <tr>
		<td align="left" class="tdPad" width="40%">Propiedad acreditada</td>
		<td align="left" class="tdPad">
        <select name="propAcreditada" id="propAcreditada" class="smallInput">
        <option value="1" {if $infA.propAcreditada == 1}selected{/if}>Si</option>
        <option value="0" {if $infA.propAcreditada == 0}selected{/if}>No</option>
        </select>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">T&iacute;tulo de propiedad con datos de inscripci&oacute;n</td>
		<td align="left" class="tdPad">
        <select name="titPropiedad" id="titPropiedad" class="smallInput">
        <option value="1" {if $infA.titPropiedad == 1}selected{/if}>Si</option>
        <option value="0" {if $infA.titPropiedad == 0}selected{/if}>No</option>
        </select>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Dictamen</td>
		<td align="left" class="tdPad">
        <select name="dictamen" id="dictamen" class="smallInput">
        <option value="">Seleccione</option>
        <option value="noElaborar" {if $infA.dictamen == "noElaborar"}selected{/if}>Aun no se puede elaborar</option>
        <option value="enProceso" {if $infA.dictamen == "enProceso"}selected{/if}>En proceso</option>
        <option value="concluido" {if $infA.dictamen == "concluido"}selected{/if}>Con dictamen concluido</option>
        </select>
        </td>
	</tr>
    
    <tr>
		<td align="left" class="tdPad" width="40%">Plazo del arrendamiento + promesa (en a&ntilde;os)</td>
		<td align="left" class="tdPad">
        Arrendamiento
        <input name="plazoArrendamiento" id="plazoArrendamiento" type="text" value="{$infA.plazoArrendamiento}" class="smallInput small" size="50"/>
        Promesa <input name="plazoPromesa" id="plazoPromesa" type="text" value="{$infA.plazoPromesa}" class="smallInput small" size="50"/>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Acta de entrega del inmueble</td>
		<td align="left" class="tdPad">
        <select name="actaEntrega" id="actaEntrega" class="smallInput">
        <option value="1" {if $infA.actaEntrega == 1}selected{/if}>Enviada</option>
        <option value="0" {if $infA.actaEntrega == 0}selected{/if}>Pendiente</option>
        </select>
        </td>
	</tr>
    <tr>
		<td align="left" class="tdPad" width="40%">Inmueble entregado</td>
		<td align="left" class="tdPad">
        <select name="inmuebleEntregado" id="inmuebleEntregado" class="smallInput" onchange="showDateInmEnt()">
        <option value="1" {if $infA.inmuebleEntregado == 1}selected{/if}>Si</option>
        <option value="0" {if $infA.inmuebleEntregado == 0}selected{/if}>No</option>
        </select>
        <div id="divFechaInmEnt" {if $infA.inmuebleEntregado == 0}style="display:none"{/if}>
        	<table width="180" cellspacing="0" cellpadding="0">
            <tr>
                <td align="left">
                <input name="fechaInmEnt" id="fechaInmEnt" type="text" value="{$infA.fechaInmEnt}" class="smallInput small" size="50" readonly="readonly" />
                </td>
                <td align="left">
                <img src="{$WEB_ROOT}/images/icons/calendar.png" width="16" height="16" id="calendar-triggerFI"  />
                <script type="text/javascript">
                Calendar.setup({
                    inputField : "fechaInmEnt",
                    trigger    : "calendar-triggerFI",
					dateFormat : "%d-%m-%Y",
                    onSelect   : function() { this.hide() }
                });
                </script>
                </td>
            </tr>
            </table>
        </div>
        </td>
	</tr>
</tbody>
</table>