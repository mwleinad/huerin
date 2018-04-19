<table width="100%" cellpadding="0" cellspacing="0" class="box-table-a" id="infoGral">
<thead>
	<tr>
		<th align="center" colspan="2">
        <div style="float:left; margin-left:400px">CONTRASE&Ntilde;AS</div>
        <div class="iconSH" id="tbFielS" onclick="toggleSection('tbFiel',1)" style="display:none">[+]</div>
        <div class="iconSH" id="tbFielH" onclick="toggleSection('tbFiel',0)">[-]</div>
        </th>
	</tr>
</thead>
<tbody id="tbFiel">
    <tr>
		<td align="left" width="40%">* Clave FIEL</td>
		<td align="left"><input name="claveFiel" id="claveFiel" type="text" value="{$contractInfo.claveFiel}" class="smallInput medium" size="50"/></td>
	</tr>

    <tr>
		<td align="left" width="40%">* Clave CIEC</td>
		<td align="left"><input name="claveCiec" id="claveCiec" type="text" value="{$contractInfo.claveCiec}" class="smallInput medium" size="50"/></td>
	</tr>

    <tr>
		<td align="left" width="40%">* Clave IDSE</td>
		<td align="left"><input name="claveIdse" id="claveIdse" type="text" value="{$contractInfo.claveIdse}" class="smallInput medium" size="50"/></td>
	</tr>

    <tr>
		<td align="left" width="40%">* Clave Impuesto Sobre Nomina</td>
		<td align="left"><input name="claveIsn" id="claveIsn" type="text" value="{$contractInfo.claveIsn}" class="smallInput medium" size="50"/></td>
	</tr>
	<tr>
		<td align="left" width="40%">* Clave Sipare</td>
		<td align="left"><input name="claveSip" id="claveSip" type="text" value="{$contractInfo.claveSip}" class="smallInput medium" size="50"/></td>
	</tr>


</tbody>
</table>