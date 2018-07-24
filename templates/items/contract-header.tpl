<thead>
	<tr>
		<th width="">Id</th>
		<th width="">Nombre de la Razon Social</th>
		{if $User.roleId eq 5 || $User.roleId eq 1}
		<th width="60">Tipo</th>
    	<th width="60">RFC</th>       
		<th width="350">Responsable</th>
		<th width="40">Activo</th>
		{/if}
        {if (in_array(86,$permissions) or in_array(85,$permissions)) || $User.isRoot ||($User.roleId eq 4&&$showServices)}
		<th width="80">Servicios</th>
		{/if}
		<th width="80">Acciones</th>
	</tr>
</thead>
