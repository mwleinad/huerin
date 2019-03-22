<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Raz&oacute;n Social</th>
		<th align="center" width="60">Servicio</th>
        <th align="center" width="60">Status</th>
		<th align="center" width="60">Periodicidad</th>
        <th align="center" width="60">Fecha de alta</th>
        <th align="center" width="60">Fecha de baja</th>
		<th align="center" width="60">Costo</th>
        <th align="center" width="60">Responsable</th>
        <th align="center" width="60">Supervisor</th>
	</tr>
</thead>
<tbody>
{foreach from=$clientes item=cliente key=key}
	{foreach from=$cliente.contracts item=contract key=keyContract}
    	{foreach from=$contract.instanciasServicio item=servicio key=keyServicio}
        <tr>
            <td align="center">{$cliente.nameContact}</td>
            <td align="center">{$contract.name}</td>
            <td align="center">{$servicio.nombreServicio}</td>
            <td align="center">{$servicio.status}</td>
            <td align="center">{$servicio.periodicidad}</td>
            <td align="center">{$servicio.inicioOperaciones}</td>
            <td align="center">{if $servicio.status eq 'Baja'}{$servicio.fechaBaja}{elseif $servicio.status eq 'Baja temporal'}{$servicio.lastDateWorkflow}{else}N/A{/if} </td>
            <td align="center">${$servicio.costo|number_format:2}</td>
            <td align="center">{if $servicio.responsable eq ''}--{else}{$servicio.responsable}{/if}</td>
            <td align="center">{if $servicio.supervisor eq ''}--{else}{$servicio.supervisor}{/if}</td>
        </tr> 
        {/foreach}
    {/foreach}
{foreachelse}
<tr>
	<td colspan="10" align="center">Ning&uacute;n registro encontrado</td>
</tr>
{/foreach}

{if $clientes|count > 0}
<tr>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center">${$totalPeriodo|number_format:2}</td>
    <td align="center"></td>
    <td align="center"></td>
</tr>
{/if}

</tbody>
</table>