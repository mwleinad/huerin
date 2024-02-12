<thead>
	<tr>

		{if in_array(243,$permissions)|| $User.isRoot}<th width=""># Empleado</th>{/if}
		<th width="">Nombre</th>
		{if in_array(230,$permissions)|| $User.isRoot}<th width="">Sueldo</th>{/if}
		{if in_array(231,$permissions)|| $User.isRoot}<th width="">Tel. Fijo</th>{/if}
		{if in_array(232,$permissions)|| $User.isRoot}<th width="">Tel. Cel.</th>{/if}
        {if in_array(233,$permissions)|| $User.isRoot}<th width="">Correo</th>{/if}
		{if in_array(236,$permissions)|| $User.isRoot}<th width="150">Horario</th>{/if}
		{if in_array(237,$permissions)|| $User.isRoot}<th width="150">Fecha de ingreso</th>{/if}
		{if in_array(247,$permissions)|| $User.isRoot}<th width="">Departamento</th>{/if}
        {if in_array(234,$permissions)|| $User.isRoot}<th width="">Num. Equipo</th>{/if}
		{if in_array(278,$permissions)|| $User.isRoot}<th width="">Usuario computadora</th>{/if}
		{if in_array(239,$permissions)|| $User.isRoot}<th width="">Contraseña computadora</th>{/if}
		{if in_array(241,$permissions)|| $User.isRoot}<th width="">Usuario plataforma</th>{/if}
		{if in_array(242,$permissions)|| $User.isRoot}<th width="">Contrase&ntilde;a plataforma</th>{/if}
		{if in_array(280,$permissions)|| $User.isRoot}<th width="">Sistema Aspel</th>{/if}
		{if in_array(279,$permissions)|| $User.isRoot}<th width="">Usuario Aspel</th>{/if}
        {if in_array(235,$permissions)|| $User.isRoot}<th width="">Contraseña Aspel</th>{/if}
		{if in_array(244,$permissions)|| $User.isRoot}<th width="">Nombre del puesto</th>{/if}
		{if in_array(245,$permissions)|| $User.isRoot}<th width="">Area</th>{/if}
		{if in_array(246,$permissions)|| $User.isRoot}<th width="">Jefe inmediato</th>{/if}
		<th>Status</th>
		<th>Acciones</th>
	</tr>
</thead>
