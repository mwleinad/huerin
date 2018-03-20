{foreach from=$personals item=item key=key}
	<tr id="1">
		<td align="center">{$item.personalId}</td>
		<td align="center">{$item.name}</td>
		<td align="center">{$item.celphone}</td>
        <td align="center">{$item.email}</td>
		<td align="center">{$item.skype}</td>
		<td align="center">{$item.computadora}</td>
		<td align="center">{$item.aspel}</td>
		<td align="center">{$item.username}</td>
		<td align="center">{$item.passwd}</td>
		<td align="center">{$item.fechaIngreso}</td>
		<td align="center">{$item.tipoPersonal}</td>
		<td align="center">{$item.departamento}</td>
		<td align="center">{$item.puesto}</td>
		<td align="center">{$item.nombreJefe}</td>
		<td align="center">
            {if in_array(9,$permissions)}
			<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.personalId}" title="Eliminar"/>
			{/if}
            {if in_array(10,$permissions)}
            <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.personalId}" title="Editar"/>
			{/if}
            {if in_array(11,$permissions)}
			<img src="{$WEB_ROOT}/images/icons/file.png" class="spanShowFile" id="{$item.personalId}" title="Ver expedientes"/>
			{/if}
		</td>
	</tr>
{foreachelse}
<tr><td colspan="15" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
