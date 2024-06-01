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
        {foreach from=$puestos item=puesto key=keyPuesto}
        <th align="center" width="60">{$puesto.name}</th>

        {/foreach}
	</tr>
</thead>
<tbody>
{foreach from=$contracts item=contract key=keyContract}
    {foreach from=$contract.servicios item=servicio key=keyServicio}
    <tr>
        <td align="center">{$contract.nameContact}</td>
        <td align="center">{$contract.name}</td>
        <td align="center">{$servicio.nombreServicio}</td>
        <td align="center">{$servicio.periodicidad}</td>
        <td align="center">{$servicio.inicioFactura}</td>
        <td align="center">{$servicio.inicioOperaciones}</td>
        <td align="center">{$servicio.nameStatusComplete}</td>
        <td align="center">{if $servicio.servicioStatus eq 'bajaParcial'}{$servicio.lastDateWorkflow}{else}N/A{/if}</td>
        <td align="center">${$servicio.costo|number_format:2}</td>
        <td align="center">${$servicio.costoVisual}</td>
        {foreach from=$puestos item=puesto key=keyPuesto}
            <th align="center" width="60">{if $servicio[$puesto.name] eq ''}--{else}{$servicio[$puesto.name]} {/if}</th>
        {/foreach}
    </tr>
    {/foreach}
{foreachelse}
<tr>
	<td colspan="15" align="center">Ning&uacute;n registro encontrado</td>
</tr>
{/foreach}

{if $contracts|count > 0}
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