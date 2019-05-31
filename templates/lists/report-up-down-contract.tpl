<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Raz&oacute;n Social</th>
        <th align="center" width="60">Fecha de alta</th>
        <th align="center" width="60">Fecha de baja</th>
        <th align="center" width="60">Movimiento</th>
        <th align="center" width="60">Fecha movimiento</th>
	</tr>
</thead>
<tbody>

	{foreach from=$contratos item=contract key=keyContract}
        <tr>
            <td align="center">{$contract.nameContact}</td>
            <td align="center">{$contract.name}</td>
            <td align="center">{$contract.fechaAlta}</td>
            <td align="center">{if $contract.movimiento eq 'Baja'}{$contract.fechaBaja}{else}--{/if}</td>
            <td align="center">{$contract.movimiento}</td>
            <td align="center">{$contract.fecha}</td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="4" align="center">Ning&uacute;n registro encontrado</td>
        </tr>
    {/foreach}

</tbody>
</table>