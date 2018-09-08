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

		<tr class="class-{$smarty.foreach.totales.index}" id="folio-{$smarty.foreach.totales.index}" style="display:none">
			<td colspan="2" align="center">&nbsp;</td>
			<td align="center">Folio</td>
			<td align="center">Fecha</td>
			<td align="right">Importe</td>
			<td align="right">Pagos</td>
			<td align="right">Saldo</td>
		</tr>

			{foreach from=$totales.$key.facturas item=factura key=kf name=facturas}
			<tr class="class-{$smarty.foreach.totales.index}" id="{$smarty.foreach.totales.index}-{$smarty.foreach.facturas.index}"  style="display:none">
				<td  colspan="2" align="center"  title="pagos de factura {$factura.serie}{$factura.folio}" >
					<div id="{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" class="showPayment" style="cursor:pointer" >
						<span style="color: blue;" id="color_py_{$smarty.foreach.facturas.iteration}">[+]</span>
					</div>
				</td>
				<td  align="center" style="padding-left:15px">{$factura.serie}{$factura.folio}</td>
				<td align="center" style="padding-right:15px">{$factura.fecha}</td>
				<td align="right" >${$factura.total_formato|number_format:2}</td>
				<td align="right" >${$factura.payment|number_format:2}</td>
				<td align="right" >${$factura.saldo|number_format:2}</td>
			</tr>
				<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="pagos-{$smarty.foreach.facturas.index}" style="display:none">
					<td colspan="2">&nbsp;</td>
					<td align="right">Folio</td>
					<td align="right">Fecha</td>
					<td align="right">Metodo de pago</td>
					<td align="right">Importe</td>
					<td align="right">Deposito</td>
				</tr>
				{foreach from=$factura.payments item=payment  name=payments}
					<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="{$smarty.foreach.facturas.index}-{$smarty.foreach.payments.index}"  style="display:none">
						<td colspan="2">&nbsp;</td>
						<td align="right">{$payment.folioPago}</td>
						<td align="right">{$payment.paymentDate}</td>
						<td align="right" >{$payment.mpago}</td>
						<td align="right" >${$payment.amount|number_format:2}</td>
						<td align="right" >${$payment.deposito|number_format:2}</td>
					</tr>
				{foreachelse}
					<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="{$smarty.foreach.facturas.index}-{$smarty.foreach.payments.index}"  style="display:none">
						<td colspan="2" align="center"></td>
						<td colspan="5" align="center">No existe movimientos de pagos.</td>
					</tr>

				{/foreach}
			{/foreach}

{foreachelse}
<tr>
	<td colspan="7" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>