<thead>
	<tr>
		<th width="">Id</th>
		<th width="">Nombre de la Razon Social</th>
		<th width="60">Tipo</th>
    	<th width="60">RFC</th>       
		<th width="350">Responsable</th>
        {if (in_array(86,$permissions) or in_array(85,$permissions)) || $User.isRoot}
		<th width="80">Servicios</th>
		{/if}
		<th width="80">Acciones</th>
	</tr>
</thead>
