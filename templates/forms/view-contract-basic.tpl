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
    	{$infoRazonSocial.type}</td>
	</tr>    

    <tr>
		<td align="left" width="40%">* Raz&oacute;n Social</td>
		<td align="left">{$infoRazonSocial.name}</td>
	</tr>    

    <tr>
		<td align="left" width="40%">* RFC</td>
		<td align="left">{$infoRazonSocial.rfc}</td>
	</tr>
		
    {if $infoRazonSocial.type == "Persona Moral"}
    <tr id="tipoDeSociedad" style="display:block">
		<td align="left" width="40%">* Tipo de Sociedad</td>
		<td align="left">
            {$infoRazonSocial.nombreSociedad}
			</td>
		</tr>
    {/if}
    <tr>
		<td align="left" width="40%">* Regimen Fiscal</td>
		<td align="left">{$infoRazonSocial.nombreRegimen}</td>
	</tr>
	{if in_array(223,$permissions) || $User.isRoot}
		<tr>
			<td align="left" width="40%">Nombre representante legal</td>
			<td align="left">{$infoRazonSocial.nameRepresentanteLegal}</td>
		</tr>
	{/if}
	{if in_array(222,$permissions) || $User.isRoot}
    <tr>
		<td align="left" width="40%">Actividad Económica</td>
		<td align="left">{$infoRazonSocial.nombreComercial}</td>
	</tr>
	{/if}
  	<tr>
		<td align="left" width="100%" class="tdPad" colspan="2" style="text-align:center">Direccion Fiscal</td>
	</tr>
  	<tr>
		<td align="left" width="40%" class="tdPad">Calle</td>
		<td align="left" class="tdPad">{$infoRazonSocial.address}</td>
	</tr>
 	 <tr>
		<td align="left" width="40%" class="tdPad">No Exterior</td>
		<td align="left" class="tdPad">{$infoRazonSocial.noExtAddress}</td>
	</tr>
  	<tr>
		<td align="left" width="40%" class="tdPad">No Interior</td>
		<td align="left" class="tdPad">{$infoRazonSocial.noIntAddress}</td>
	</tr>
  	<tr>
		<td align="left" width="40%" class="tdPad">Colonia</td>
		<td align="left" class="tdPad">{$infoRazonSocial.coloniaAddress}</td>
	</tr>
  	<tr>
		<td align="left" width="40%" class="tdPad">Municipio</td>
		<td align="left" class="tdPad">{$infoRazonSocial.municipioAddress}</td>
	</tr>

  <tr>
		<td align="left" width="40%" class="tdPad">Estado</td>
		<td align="left" class="tdPad">{$infoRazonSocial.estadoAddress}</td>
	</tr>

	<tr>
		<td align="left" width="40%" class="tdPad">Pais</td>
		<td align="left" class="tdPad">{$infoRazonSocial.paisAddress}</td>
	</tr>

  <tr>
		<td align="left" width="40%" class="tdPad">* Codigo Postal:</td>
		<td align="left" class="tdPad">{$infoRazonSocial.cpAddress}</td>
	</tr>

	<tr>
		<td align="left" width="40%" class="tdPad">* Metodo de Pago:</td>
		<td align="left" class="tdPad">{$infoRazonSocial.metodoDePago}</td>
	</tr>

	<tr>
		<td align="left" width="40%" class="tdPad"># Cuenta:</td>
		<td align="left" class="tdPad">{$infoRazonSocial.noCuenta}</td>
	</tr>
	<tr>
		<td align="left" width="40%" class="tdPad">* Direccion de Recoleccion de Papeleria:</td>
		<td align="left" class="tdPad">{$infoRazonSocial.direccionComercial}</td>
	</tr>
	{foreach from=$departamentos item=depto}
	<tr>
		<td align="left" width="40%" class="tdPad">
			{$depto.departamento}
		</td>
		<td>
			{foreach from=$empleados item=item}
				{if $permisos[$depto.departamentoId] == $item.personalId}{$item.name}{/if}
			{/foreach} 
		</td>
	</tr>
    {/foreach}
</tbody>
</table>