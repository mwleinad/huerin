<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Raz&oacute;n Social</th>
		<th align="center" width="60">Servicio</th>
        <th align="center" width="60">Inicio de operaciones</th>
        <th align="center" width="60">Inicio de facturacion</th>
        <th align="center" width="60">Periodicidad</th>
        <th align="center" width="60">Fecha de alta</th>
        <th align="center" width="60">Fecha de baja</th>
		<th align="center" width="60">Costo</th>
        <th align="center" width="60">Status actual del servicio</th>
        <th align="center" width="60">Responsable</th>
        <th align="center" width="60">Supervisor</th>
        <th align="center" width="60">Movimiento</th>
        <th align="center" width="60">Fecha de movimiento</th>
	</tr>
</thead>
<tbody>
{foreach from=$registros item=servicio key=key}
    <tr>
        <td align="center">{$servicio.nameContact}</td>
        <td align="center">{$servicio.name}</td>
        <td align="center">{$servicio.nombreServicio}</td>
        <td align="center">{$servicio.inicioOperaciones}</td>
        <td align="center">{$servicio.inicioFactura}</td>
        <td align="center">{$servicio.periodicidad}</td>
        <td align="center">{$servicio.fechaAlta}</td>
        <td align="center">{if $servicio.movimiento eq 'Baja'}{$servicio.fechaBaja}{else}--{/if} </td>
        <td align="center">${$servicio.costo|number_format:2}</td>
        <td align="center">{$servicio.currentState}</td>
        <td align="center">{if $servicio.responsable eq ''}--{else}{$servicio.responsable}{/if}</td>
        <td align="center">{if $servicio.supervisor eq ''}--{else}{$servicio.supervisor}{/if}</td>
        <td align="center">{$servicio.movimiento}</td>
        <td align="center">{$servicio.fecha}</td>
    </tr>
{foreachelse}
    <tr>
        <td colspan="10" align="center">Ning&uacute;n registro encontrado</td>
    </tr>
{/foreach}
</tbody>
</table>