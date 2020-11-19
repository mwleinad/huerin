<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Gerente</th>
		<th align="center" width="60">Supervisor</th>
		<th align="center" width="60">Razon social</th>
        <th align="center" width="60">Sector</th>
        <th align="center" width="60">Subsector</th>
        <th align="center" width="60">Actividad</th>
	</tr>
</thead>
<tbody>
{foreach from=$registros item=item key=key}
    <tr>
        <td align="center">{$item.gerente}</td>
        <td align="center">{$item.supervisor}</td>
        <td align="center">{$item.name}</td>
        <td align="center">{$item.nameSector}</td>
        <td align="center">{$item.nameSubsector}</td>
        <td align="center">{$item.actividad}</td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="6" align="center">Ning&uacute;n registro encontrado</td>
    </tr>
{/foreach}
</tbody>
</table>
