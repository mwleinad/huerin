<thead>
	<tr>
    <th width="50">No. Cliente</th>
    <th width="100">Nombre Directivo</th>
    {if $User.roleId eq 5 || $User.roleId eq 1}
    <th width="100">Tel. Contacto</th>       
    <th width="250">Email Contacto</th>
    <th width="100">Password</th>
    {/if}
	<th width="50">Razones Sociales</th>
    {if $User.roleId eq 5 || $User.roleId eq 1}
     <th width="30">Activo</th>
    {/if}
    <th width="100">Fecha Alta</th>
	<th>Acciones</th>

	</tr>
</thead>
