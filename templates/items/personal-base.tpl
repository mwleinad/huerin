{foreach from=$personals item=item key=key}
	<tr id="1">
		{if in_array(243,$permissions)|| $User.isRoot}<td align="center">{$item.personalId}</td>{/if}
		<td align="center">{$item.name}</td>
		{if in_array(230,$permissions)|| $User.isRoot}<td align="center">{$item.sueldo}</td>{/if}
		{if in_array(231,$permissions)|| $User.isRoot}<td align="center">{$item.phone}</td>{/if}
		{if in_array(232,$permissions)|| $User.isRoot}<td align="center">{$item.celphone}</td>{/if}
		{if in_array(233,$permissions)|| $User.isRoot}<td align="center">{$item.email}</td>{/if}
		{if in_array(236,$permissions)|| $User.isRoot}<td align="center">{$item.horario}</td>{/if}
		{if in_array(237,$permissions)|| $User.isRoot}<td align="center">{$item.fechaIngreso}</td>{/if}
		{if in_array(247,$permissions)|| $User.isRoot}<td align="center">{$item.grupo}</td>{/if}
		{if in_array(234,$permissions)|| $User.isRoot}<td align="center">{$item.skype}</td>{/if}
		{if in_array(278,$permissions)|| $User.isRoot}<td align="center">{$item.userComputadora}</td>{/if}
		{if in_array(239,$permissions)|| $User.isRoot}<td align="center">{$item.passwordComputadora}</td>{/if}
		{if in_array(241,$permissions)|| $User.isRoot}<td align="center">{$item.username}</td>{/if}
		{if in_array(242,$permissions)|| $User.isRoot}<td align="center">{$item.passwd}</td>{/if}
		{if in_array(280,$permissions)|| $User.isRoot}<td align="center">{$item.systemAspel}</td>{/if}
		{if in_array(279,$permissions)|| $User.isRoot}<td align="center">{$item.userAspel}</td>{/if}
		{if in_array(235,$permissions)|| $User.isRoot}<td align="center">{$item.passwordAspel}</td>{/if}
		{if in_array(244,$permissions)|| $User.isRoot}<td align="center">{$item.tipoPersonal}</td>{/if}
		{if in_array(245,$permissions)|| $User.isRoot}<td align="center">{$item.departamento}</td>{/if}
		{if in_array(246,$permissions)|| $User.isRoot}<td align="center">{$item.nombreJefe}</td>{/if}
		<td>{if $item.active eq 1} <span style="background: green; color:#ffffff;font-weight: bold;padding: 2px;border-radius: 2px;">Activo</span>{else} <span style="background: red;color:#ffffff;font-weight: bold;padding: 2px;border-radius: 2px;">Inactivo</span> {/if}</td>
		<td align="center">
            {if in_array(9,$permissions) || $User.isRoot}
				{if $User.userId neq $item.personalId}
					<img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.personalId}" title="Eliminar"/>
				{/if}
			{/if}
            {if in_array(10,$permissions)|| $User.isRoot}
				{if $User.userId neq $item.personalId}
            		<img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.personalId}" title="Editar"/>
				{/if}
			{/if}
            {if in_array(11,$permissions)|| $User.isRoot}
			<img src="{$WEB_ROOT}/images/icons/file.png" class="spanShowFile" id="{$item.personalId}" title="Ver expedientes"/>
			{/if}
		</td>
	</tr>
{foreachelse}
<tr><td colspan="14" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
