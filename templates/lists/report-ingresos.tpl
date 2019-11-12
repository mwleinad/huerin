<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Raz&oacute;n Social</th>
		<th align="center" width="60">Servicio</th>
		<th align="center" width="60">Periodo</th>
        <th align="center" width="60">Inicio facturacion</th>
        <th align="center" width="60">Inicio Operacion</th>
        <th align="center" width="60">Status</th>
        <th align="center" width="60">Ultimo Workflow</th>
		<th align="center" width="60">Costo por Periodo</th>
        <th align="center" width="60">Costo Informativo</th>
        <th align="center" width="60">Auxiliar</th>
        <th align="center" width="60">Contador</th>
        <th align="center" width="60">Supervisor</th>
        <th align="center" width="60">Subgerente</th>
        <th align="center" width="60">Gerente</th>
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
            <td align="center">{$servicio.periodicidad}</td>
            <td align="center">{$servicio.inicioFactura}</td>
            <td align="center">{$servicio.inicioOperaciones}</td>
            <td align="center">{$servicio.nameStatusComplete}</td>
            <td align="center">{if $servicio.servicioStatus eq 'bajaParcial'}{$servicio.lastDateWorkflow}{else}N/A{/if}</td>
            <td align="center">${$servicio.costo|number_format:2}</td>
            <td align="center">${$servicio.costoVisual}</td>
            <td align="center">{if $servicio.auxiliar eq ''}--{else}{$servicio.auxiliar}{/if}</td>
            <td align="center">{if $servicio.contador eq ''}--{else}{$servicio.contador}{/if}</td>
            <td align="center">{if $servicio.supervisor eq ''}--{else}{$servicio.supervisor}{/if}</td>
            <td align="center">{if $servicio.subgerente eq ''}--{else}{$servicio.subgerente}{/if}</td>
            <td align="center">{if $servicio.gerente eq ''}--{else}{$servicio.gerente}{/if}</td>
        </tr> 
        {/foreach}
    {/foreach}
{foreachelse}
<tr>
	<td colspan="15" align="center">Ning&uacute;n registro encontrado</td>
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
    <td align="center"></td>
    <td align="center">${$totalPeriodo|number_format:2}</td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
    <td align="center"></td>
</tr>
{/if}

</tbody>
</table>