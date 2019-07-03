<thead>
	<tr>

		{if in_array(243,$permissions)|| $User.isRoot}<th width=""># Empleado</th>{/if}
		<th width="">Nombre</th>
		{if in_array(232,$permissions)|| $User.isRoot}<th width="">Tel. Cel.</th>{/if}
        {if in_array(233,$permissions)|| $User.isRoot}<th width="150">Correo</th>{/if}
        {if in_array(234,$permissions)|| $User.isRoot}<th width="150">Num. Equipo</th>{/if}
        {if in_array(235,$permissions)|| $User.isRoot}<th width="150">Clave Aspel</th>{/if}
		{if in_array(237,$permissions)|| $User.isRoot}<th width="150">Fecha de ingreso</th>{/if}
		{if in_array(239,$permissions)|| $User.isRoot}<th width="150">Clave de computadora</th>{/if}
		{if in_array(241,$permissions)|| $User.isRoot}<th width="150">Usuario</th>{/if}
		{if in_array(242,$permissions)|| $User.isRoot}<th width="150">Contrase&ntilde;a</th>{/if}
		{if in_array(244,$permissions)|| $User.isRoot}<th width="150">Tipo de usuario</th>{/if}
		{if in_array(245,$permissions)|| $User.isRoot}<th width="150">Departamento</th>{/if}
		{if in_array(246,$permissions)|| $User.isRoot}<th width="150">Jefe inmediato</th>{/if}
		<th>Acciones</th>
	</tr>
</thead>
