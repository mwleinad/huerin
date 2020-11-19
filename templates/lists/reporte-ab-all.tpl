<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">No.</th>
		<th align="center" width="60">Fecha movimiento</th>
		<th align="center" width="60">Movimiento</th>
        <th align="center" width="60">Cliente</th>
        <th align="center" width="60">Razon social</th>
        <th align="center" width="60">Servicio</th>
        <th align="center" width="60">Supervisor</th>
        <th align="center" width="60">Costo mensual</th>
		<th align="center" width="60">Costo una sola ocasíon</th>
        <th align="center" width="60">Recomendado</th>
	</tr>
</thead>
<tbody>
{foreach from=$registros item=item key=key}
    <tr>
        <td align="center">{$key + 1}</td>
        <td align="center">{$item.dateMovimiento}</td>
        <td align="center">{$item.typeMovimiento}</td>
        <td align="center">{$item.nameContact}</td>
        <td align="center">{$item.name}</td>
        <td align="center">{$item.nombreServicio}</td>
        <td align="center">{$item.supervisor}</td>
        <td align="center">$ {$item.costo|number_format:'2':'.':','}</td>
        <td align="center">$ {$item.costo|number_format:'2':'.':','}</td>
        <td align="center"></td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="10" align="center">Ning&uacute;n registro encontrado</td>
    </tr>
{/foreach}
</tbody>
</table>
