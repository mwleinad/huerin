<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" style="font-size:11px">
<thead>
	<tr>
		<th align="center" width="60"></th>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Nombre</th>
		<th align="center" width="60">Facturador</th>
		<th align="center" width="60">Importe</th>
		<th align="center" width="60">Pagos</th>
		<th align="center" width="60">Saldo</th>

	</tr>
</thead>
<tbody>
{foreach from=$totales item=cxc key=key name=totales}
<tr >
    <td  align="center" class="" title="{$cxc.nameContact}" >
		<div onclick="ToggleSpecifiedDiv('{$smarty.foreach.totales.index}')" style="cursor:pointer" >
			<span style="color: blue;" id="color_{$smarty.foreach.totales.iteration}">[+]</span>
		</div>
    </td>
    <td  align="center" class="" title="{$cxc.nameContact}">{$cxc.nameContact}</td>
    <td  align="center" class="" title="{$cxc.nombre}">{$key}</td>
    <td  align="center" class="" title="{$cxc.responsable.name}">{$cxc.facturador}</td>
    <td  align="center" class="" title="{$cxc.responsable.name}">${$cxc.total|number_format:2}</td>
    <td  align="center" class="" title="{$cxc.responsable.name}">${$cxc.payment|number_format:2}</td>
    <td  align="center" class="" title="{$cxc.responsable.name}">${$cxc.saldo|number_format:2}</td>
</tr>

		<tr class="class-{$smarty.foreach.totales.index}" id="folio-{$smarty.foreach.payments.index}" style="display:none">
			<td colspan="2">&nbsp;</td>
			<td>Folio</td>
			<td>Fecha</td>
			<td>Importe</td>
			<td>Pagos</td>
			<td>Saldo</td>
		</tr>

			{foreach from=$totales.$key.payments item=payment  name=payments}
			<tr class="class-{$smarty.foreach.totales.index}" id="{$smarty.foreach.totales.index}-{$smarty.foreach.payments.index}"  style="display:none">
			<td colspan="2">&nbsp;</td>
				<td style="padding-left:15px">{$payment.serie}{$payment.folio}</td>
				<td align="center" style="padding-right:15px">{$payment.fecha}</td>
				<td align="right" style="padding-right:15px">${$payment.total_formato}</td>
				<td align="right" style="padding-right:15px">${$payment.payment|number_format:2}</td>
				<td align="right" style="padding-right:15px">${$payment.saldo|number_format:2}</td>
			</tr>
			{/foreach}

{foreachelse}
<tr>
	<td colspan="7" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>