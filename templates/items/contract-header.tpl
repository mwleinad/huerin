<thead>
	<tr>
		{if in_array(200,$permissions) || $User.isRoot}
			<th width="">Id</th>
		{/if}
		{if in_array(201,$permissions) || $User.isRoot}
			<th width="">Nombre de la Razon Social</th>
		{/if}
		{if in_array(202,$permissions) || $User.isRoot}
			<th width="60">Tipo</th>
		{/if}
		{if in_array(203,$permissions) || $User.isRoot}
    		<th width="60">RFC</th>
		{/if}
		{if in_array(204,$permissions) || $User.isRoot}
			<th width="350">Responsable</th>
		{/if}
		{if in_array(205,$permissions) || $User.isRoot}
			<th width="40">Activo</th>
		{/if}
		{if in_array(206,$permissions) || $User.isRoot}
			<th width="80">Servicios</th>
		{/if}
		{if in_array(207,$permissions) || $User.isRoot}
			<th width="80">Acciones</th>
		{/if}
	</tr>
</thead>
