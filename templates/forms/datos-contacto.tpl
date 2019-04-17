<table width="100%" cellpadding="0" cellspacing="0" class="box-table-a" id="infoGral">
<thead>
	<tr>
		<th align="center" colspan="2">
        <div style="float:left; margin-left:390px">DATOS DE CONTACTO</div>
        <div class="iconSH" id="tbDatosContactoS" onclick="toggleSection('tbDatosContacto',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbDatosContactoH" onclick="toggleSection('tbDatosContacto',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbDatosContacto">
    {if in_array(186,$permissions) || $User.isRoot}
    <tr>
			<td align="left" width="40%">* Nombre Contacto Administrativo (Pagos)</td>
			<td align="left"><input name="nameContactoAdministrativo" id="nameContactoAdministrativo" type="text" value="{$bse.nameContactoAdministrativo}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Email Contacto Administrativo (Pagos)</td>
			<td align="left"><input name="emailContactoAdministrativo" id="emailContactoAdministrativo" type="text" value="{$bse.emailContactoAdministrativo}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Telefono Contacto Administrativo (Pagos)</td>
			<td align="left"><input name="telefonoContactoAdministrativo" id="telefonoContactoAdministrativo" type="text" value="{$bse.telefonoContactoAdministrativo}" class="smallInput medium" size="50"/></td>
	</tr>
	{/if}
	{if in_array(187,$permissions) || $User.isRoot}
    <tr>
			<td align="left" width="40%">* Nombre Contacto Contabilidad (Documentacion)</td>
			<td align="left"><input name="nameContactoContabilidad" id="nameContactoContabilidad" type="text" value="{$bse.nameContactoContabilidad}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Email Contacto Contabilidad (Documentacion)</td>
			<td align="left"><input name="emailContactoContabilidad" id="emailContactoContabilidad" type="text" value="{$bse.emailContactoContabilidad}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Telefono Contacto Contabilidad (Documentacion)</td>
			<td align="left"><input name="telefonoContactoContabilidad" id="telefonoContactoContabilidad" type="text" value="{$bse.telefonoContactoContabilidad}" class="smallInput medium" size="50"/></td>
	</tr>
	{/if}
	{if in_array(187,$permissions) || $User.isRoot}
		<tr>
			<td align="left" width="40%">Nombre representante legal</td>
			<td align="left"><input name="nameRepresentanteLegal" id="nameRepresentanteLegal" type="text" value="{$bse.nameRepresentanteLegal}" class="smallInput medium" size="50"/></td>
		</tr>

		<tr>
			<td align="left" width="40%">Email representante legal</td>
			<td align="left"><input name="emailRepresentanteLegal" id="emailRepresentanteLegal" type="text" value="{$bse.emailRepresentanteLegal}" class="smallInput medium" size="50"/></td>
		</tr>

		<tr>
			<td align="left" width="40%">Telefono representante legal</td>
			<td align="left"><input name="telefonoRepresentanteLegal" id="telefonoRepresentanteLegal" type="text" value="{$bse.telefonoRepresentanteLegal}" class="smallInput medium" size="50"/></td>
		</tr>
	{/if}
	{if in_array(188,$permissions) || $User.isRoot}
    <tr>
			<td align="left" width="40%">* Nombre Contacto Directivo</td>
			<td align="left"><input name="nameContactoDirectivo" id="nameContactoDirectivo" type="text" value="{$bse.nameContactoDirectivo}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Email Contacto Directivo</td>
			<td align="left"><input name="emailContactoDirectivo" id="emailContactoDirectivo" type="text" value="{$bse.emailContactoDirectivo}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Telefono de Oficina Contacto Directivo</td>
			<td align="left"><input name="telefonoContactoDirectivo" id="telefonoContactoDirectivo" type="text" value="{$bse.telefonoContactoDirectivo}" class="smallInput medium" size="50"/></td>
		</tr>    

    <tr>
			<td align="left" width="40%">* Telefono Celular Contacto Directivo</td>
			<td align="left"><input name="telefonoCelularDirectivo" id="telefonoCelularDirectivo" type="text" value="{$bse.telefonoCelularDirectivo}" class="smallInput medium" size="50"/></td>
	</tr>
	{/if}

</tbody>
</table>