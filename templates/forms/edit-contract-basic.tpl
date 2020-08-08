<table width="100%" cellpadding="0" cellspacing="0" class="box-table-a" id="infoGral">
<thead>
	<tr>
		<th align="center" colspan="2">
            <div style="float:left; margin-left:330px">INFORMACION BASICA DE LA RAZON SOCIAL</div>
            <div class="iconSH" id="tbInfoProyS" onclick="toggleSection('tbInfoProy',1)" style="display:none">[+]</div>
            <div class="iconSH" id="tbInfoProyH" onclick="toggleSection('tbInfoProy',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbInfoProy">
    <tr>
		<td align="left" width="40%">* Tipo</td>
		<td align="left">
            <select name="type" id="type" class="smallInput medium" onchange="ChangeTipo()">
                <option value="">Seleccione...</option>
                <option value="Persona Fisica" {if $contractInfo.type == "Persona Fisica"} selected="selected" {/if} >Persona Fisica</option>
                <option value="Persona Moral" {if $contractInfo.type == "Persona Moral"} selected="selected" {/if}>Persona Moral</option>
            </select>
        </td>
	</tr>
    <tr>
		<td align="left" width="40%">* Facturador</td>
		<td align="left">
            <select name="facturador" id="facturador" class="smallInput medium">
                <option value="">Seleccionar...</option>
                <option value="Efectivo" {if $contractInfo.facturador == "Efectivo"} selected="selected" {/if} >Efectivo</option>
                {foreach from=$emisores  item=item key=key}
                    <option value="{$item.claveFacturador}" {if $item.claveFacturador == $contractInfo.facturador} selected="selected" {/if} >{$item.razonSocial}</option>
                {/foreach}
            </select>

      </td>
	</tr>
    <tr>
		<td align="left" width="40%">* Raz&oacute;n Social</td>
		<td align="left"><input name="name" id="name" type="text" value="{$contractInfo.name}" class="smallInput medium" size="50"/></td>
	</tr>
    <tr>
		<td align="left" width="40%">* RFC</td>
		<td align="left"><input name="rfc" id="rfc" type="text" value="{$contractInfo.rfc}" class="smallInput medium" size="50"/></td>
	</tr>
    <tr id="tipoDeSociedad" {if $contractInfo.type == "Persona Fisica"}style="display:none"{/if}>
		<td align="left" width="40%">* Tipo de Sociedad</td>
		<td align="left">
            	<select class="smallInput" name="sociedadId" id="sociedadId">
              <option value="">Seleccione</option>
            {foreach from=$sociedades item=item}
            <option value="{$item.sociedadId}" {if $contractInfo.sociedadId == $item.sociedadId} selected="selected" {/if}>{$item.nombreSociedad}</option>
            {/foreach}
            </select>
			</td>
		</tr>
    <tr  id="regimenesFisicos" {if $contractInfo.type == "Persona Moral"}style="display:none"{/if}>
		<td align="left" width="40%">* Regimen Fiscal</td>
		<td align="left">
            <select class="smallInput" name="regimenId" id="regimenId" onchange="LoadSubcontracts()">
            <option value="">Seleccione</option>
                {foreach from=$regimenes item=item}
                <option value="{$item.regimenId}" {if $contractInfo.regimenId == $item.regimenId} selected="selected" {/if}>{$item.tipoDePersona} | {$item.nombreRegimen}</option>
                {/foreach}
            </select>
        </td>
	</tr>
    <tr  id="regimenesMorales" {if $contractInfo.type == "Persona Fisica"}style="display:none"{/if}>
		<td align="left" width="40%">* Regimen Fiscal</td>
		<td align="left">
            <select class="smallInput" name="regimenIdMoral" id="regimenIdMoral" onchange="LoadSubcontracts()">
                <option value="">Seleccione</option>
                {foreach from=$regimenesMoral item=item}
                <option value="{$item.regimenId}" {if $contractInfo.regimenId == $item.regimenId} selected="selected" {/if}>{$item.tipoDePersona} | {$item.nombreRegimen}</option>
                {/foreach}
            </select>
        </td>
	</tr>
    <tr>
        <td align="left" width="40%"> Clasificacion</td>
        <td align="left">
            <select name="qualification" id="qualification" class="smallInput">
                <option value="">Seleccione....</option>
                <option value="AAA" {if $contractInfo.qualification eq 'AAA'}selected{/if}>Buena</option>
                <option value="AA"  {if $contractInfo.qualification eq 'AA'}selected{/if}>Regular</option>
                <option value="A"   {if $contractInfo.qualification eq 'A'}selected{/if}>Mala</option>
            </select>
        </td>
    </tr>
    {if in_array(223,$permissions) || $User.isRoot}
        <tr>
            <td align="left" width="40%">Nombre representante legal</td>
            <td align="left">
                <input name="nameRepresentanteLegal" id="nameRepresentanteLegal" type="text" value="{$contractInfo.nameRepresentanteLegal}" class="smallInput medium" size="50"/>
            </td>
        </tr>

    {/if}
    <tr>
        <td align="left" width="40%"> Facturar con datos alternativos</td>
        <td align="left">
            <div class="container_12">
                <div class="grid_12">
                    <select class="smallInput" name="use_alternative_rz_for_invoice" id="use_alternative_rz_for_invoice">
                        <option value="0" {if $contractInfo.useAlternativeRzForInvoice eq '0'}selected{/if}>No</option>
                        <option value="1" {if $contractInfo.useAlternativeRzForInvoice eq '1'}selected{/if}>Si</option>
                    </select>
                </div>
                <div class="grid_12">
                    <label>&nbsp;</label>
                    <input type="hidden" class="smallInput" name="alternative_rz_id" id="alternative_rz_id" value="{if $contractInfo.alternativeRzId neq null}{$contractInfo.alternativeRzId}{/if}" />
                </div>
                <div class="grid_12" id ="div_other_data" style="display: {if $contractInfo.alternativeRzId === '0' && $contractInfo.useAlternativeRzForInvoice eq '1'}block{else}none{/if}">
                    <div class="container_12">
                        <div class="grid_12">
                            <label>* Nombre o razon social alternativa</label>
                            <input type="text"  name="alternativeRz" id="alternativeRz" class="smallInput" value="{$contractInfo.alternativeRz}">
                        </div>
                        <div class="grid_12">
                            <label>* Rfc alternativo</label>
                            <input type="text" name="alternativeRfc" id="alternativeRfc" class="smallInput" value="{$contractInfo.alternativeRfc}">
                        </div>
                        <div class="grid_12">
                            <label>* Codigo postal alternativo</label>
                            <input type="text" name="alternativeCp" id="alternativeCp" class="smallInput" value="{$contractInfo.alternativeCp}">
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    {if in_array(222,$permissions) || $User.isRoot}
        <tr>

            <td align="left" width="40%">* Actividad Económica</td>
            <td align="left">
                <div class="container_12">
                    <div class="grid_12">
                        <label>Sector</label>
                        <select class="smallInput select2" name="sector" id="sector">
                            <option value="">Seleccionar..</option>
                           {foreach from=$sectores  item=sector key=key}
                               <option value="{$sector.id}" {if $contractInfo.sector_id eq $sector.id}selected{/if}>{$sector.name}</option>
                           {/foreach}
                        </select>
                    </div>
                    <div class="grid_12">
                        <label>Subsector</label>
                        <select class="smallInput select2" name="subsector" id="subsector">
                            <option value="">Seleccionar..</option>
                            {foreach from=$subsectores  item=subsector key=key}
                                <option value="{$subsector.id}" {if $contractInfo.subsector_id eq $subsector.id}selected{/if}>{$subsector.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="grid_12">
                        <label>Rubro</label>
                        <select class="smallInput select2" name="actividad_comercial" id="actividad_comercial">
                            <option value="">Seleccionar..</option>
                            {foreach from=$actividades_comerciales  item=actividad key=key}
                                <option value="{$actividad.id}" {if $contractInfo.ac_id eq $actividad.id}selected{/if}>{$actividad.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </td>
        </tr>
    {/if}
    <tr>
        <td align="left" width="100%" class="tdPad" colspan="2" style="text-align:center">Direccion Fiscal</td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Calle</td>
        <td align="left" class="tdPad">
        <input type="text" name="address" id="address" class="smallInput" style="width:350px" value="{$contractInfo.address}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">No. Exterior:</td>
        <td align="left" class="tdPad">
            <input type="text" name="noExtAddress" id="noExtAddress" class="smallInput" style="width:350px" value="{$contractInfo.noExtAddress}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">No. Interior:</td>
        <td align="left" class="tdPad">
            <input type="text" name="noIntAddress" id="noIntAddress" class="smallInput" style="width:350px" value="{$contractInfo.noIntAddress}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Colonia:</td>
        <td align="left" class="tdPad">
            <input type="text" name="coloniaAddress" id="coloniaAddress" class="smallInput" style="width:350px" value="{$contractInfo.coloniaAddress}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Municipio:</td>
        <td align="left" class="tdPad">
            <input type="text" name="municipioAddress" id="municipioAddress" class="smallInput" style="width:350px" value="{$contractInfo.municipioAddress}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Estado:</td>
        <td align="left" class="tdPad">
            <input type="text" name="estadoAddress" id="estadoAddress" class="smallInput" style="width:350px" value="{$contractInfo.estadoAddress}"/>
        </td>
    </tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Pais:</td>
        <td align="left" class="tdPad">
            <input type="text" name="paisAddress" id="paisAddress" class="smallInput" style="width:350px" value="{$contractInfo.paisAddress}"/>
        </td>
    </tr>

  <tr>
		<td align="left" width="40%" class="tdPad">* Codigo Postal:</td>
		<td align="left" class="tdPad">
        <input type="text" name="cpAddress" id="cpAddress" class="smallInput" style="width:350px" value="{$contractInfo.cpAddress}"/>
    </td>
	</tr>

    <tr>
        <td align="left" width="40%" class="tdPad">* Metodo de Pago:</td>
        <td align="left" class="tdPad">
            <select name="metodoDePago" id="metodoDePago" class="largeInput">
                <option value="01" {if $contractInfo.metodoDePago == "01"} selected {/if}>Efectivo</option>
                <option value="02" {if $contractInfo.metodoDePago == "02"} selected {/if}>Cheque</option>
                <option value="03" {if $contractInfo.metodoDePago == "03"} selected {/if}>Transferencia</option>
                <option value="04" {if $contractInfo.metodoDePago == "04"} selected {/if}>Tarjetas de Credito</option>
                <option value="05" {if $contractInfo.metodoDePago == "05"} selected {/if}>Monederos electrónicos</option>
                <option value="06" {if $contractInfo.metodoDePago == "06"} selected {/if}>Dinero electrónico</option>
                <option value="08" {if $contractInfo.metodoDePago == "08"} selected {/if}>Vales de despensa</option>
                <option value="28" {if $contractInfo.metodoDePago == "28"} selected {/if}>Tarjeta de Debito </option>
                <option value="29" {if $contractInfo.metodoDePago == "29"} selected {/if}>Tarjeta de Servicio </option>
                <option value="99" {if $contractInfo.metodoDePago == "99"} selected {/if}>Otros</option>
                <option value="NA" {if $contractInfo.metodoDePago == "NA"} selected {/if}>NA</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="left" width="40%" class="tdPad"># Cuenta:</td>
        <td align="left" class="tdPad">
            <input type="text" name="noCuenta" id="noCuenta" class="smallInput" style="width:350px" value="{$contractInfo.noCuenta}"/>
        </td>
    </tr>
    <tr>
		<td align="left" width="40%" class="tdPad">* Direccion de Recoleccion de Papeleria</td>
		<td align="left" class="tdPad">
            <textarea name="direccionComercial" id="direccionComercial" class="smallInput" style="width:350px" rows="5">{$contractInfo.direccionComercial}</textarea>
        </td>
	</tr>
    <tr>
		<td align="left" width="40%" class="tdPad">* Responsable contabilidad e impuestos:</td>
		<td align="left" class="tdPad">
          <select name="responsableCuenta" id="responsableCuenta" class="smallInput medium">
          <option value="">Seleccionar.......</option>
          {foreach from=$empleados item=item}
            {if $item.name neq '.' && $item.departamentoId eq 1}
          	    <option value="{$item.personalId}" {if $permisos[1]== $item.personalId} selected="selected"{/if}>{$item.name}</option>
            {/if}
          {/foreach}
          </select>
    </td>
	</tr>

{foreach from=$departamentos item=depto}
  {assign var="deps" value=[]}
  {append var="deps"  value=$depto.departamentoId index=$depto.departamentoId}
  {if $depto.departamentoId!=1}
  <tr>
    <td align="left" width="40%" class="tdPad">* Responsable {$depto.departamento}:</td>
    <td align="left" class="tdPad">
        <select name="permisos[]" id="permisos[]" class="smallInput medium">
            <option value="">Seleccionar......</option>
            {foreach from=$empleados item=item}
                {if $item.name neq '.' && in_array($item.departamentoId,$deps)}
                    <option value="{$depto.departamentoId},{$item.personalId}" {if $permisos.{$depto.departamentoId} == $item.personalId} selected="selected"{/if}>{$item.name}</option>
                {/if}
            {/foreach}
        </select>
    </td>
	</tr>
	{/if}
{/foreach}
</tbody>
</table>
