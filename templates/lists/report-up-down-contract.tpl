<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Raz&oacute;n Social</th>
        <th align="center" width="60">Movimiento</th>
        <th align="center" width="60">Fecha de alta/baja</th>
	</tr>
</thead>
<tbody>
{foreach from=$clientes item=cliente key=key}
	{foreach from=$cliente.contracts item=contract key=keyContract}
        <tr>
            <td align="center">{$cliente.nameContact}</td>
            <td align="center">{$contract.name}</td>
            <td align="center">{if $contract.activo eq 'Si'}Alta{elseif $contract.activo eq 'No'}Baja{/if}</td>
            <td align="center">{if $contract.activo eq 'Si'}{$contract.fechaAlta}{else}{$contract.fechaBaja}{/if}</td>
        </tr>
    {/foreach}
{foreachelse}
<tr>
	<td colspan="4" align="center">Ning&uacute;n registro encontrado</td>
</tr>
{/foreach}

</tbody>
</table>