<thead>
	<tr>
    {if in_array(190,$permissions) || $User.isRoot}
        <th width="50">No. Cliente</th>
    {/if}
    {if in_array(191,$permissions) || $User.isRoot}
        <th width="100">Nombre Directivo</th>
    {/if}
    {if in_array(192,$permissions) || $User.isRoot}
        <th width="100">Tel. Contacto</th>
    {/if}
    {if in_array(193,$permissions) || $User.isRoot}
    <th width="250">Email Contacto</th>
    {/if}
    {if in_array(194,$permissions) || $User.isRoot}
        <th width="100">Password</th>
    {/if}
    {if in_array(195,$permissions) || $User.isRoot}
	    <th width="50">Razones Sociales</th>
    {/if}
    {if in_array(196,$permissions) || $User.isRoot}
        <th width="30">Activo</th>
    {/if}
    {if in_array(197,$permissions) || $User.isRoot}
        <th width="100">Fecha Alta</th>
    {/if}
    {if in_array(198,$permissions) || $User.isRoot}
	    <th width="50">Acciones</th>
    {/if}
	</tr>
</thead>
