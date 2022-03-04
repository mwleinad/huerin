<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" style="font-size:11px">
<thead>
	<tr>
		<th align="center" width="60"></th>
		<th align="center" width="60">RFC</th>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Nombre</th>
		<th align="center" width="60">Saldo inicial</th>
		<th align="center" width="60">Importe</th>
		<th align="center" width="60">Pagos</th>
		<th align="center" width="60">Saldo actual</th>
	</tr>
</thead>
<tbody>
{foreach from=$totales item=cxc key=key name=totales}
<tr >
    <td style="background: #9fd39c; text-align: center;" class="" title="{$cxc.nameContact}" >
		<div onclick="ToggleSpecifiedDiv('{$smarty.foreach.totales.index}')" style="cursor:pointer" >
			<span style="color: blue;" id="color_{$smarty.foreach.totales.iteration}">[+]</span>
		</div>
    </td>
	<td style="background: #9fd39c; text-align: center;" class="" title="{$cxc.rfc}">{$cxc.rfc}</td>
    <td style="background: #9fd39c; text-align: center;" class="" title="{$cxc.nameContact}">{$cxc.nameContact}</td>
    <td  style="background: #9fd39c; text-align: center;" class="" title="{$cxc.nombre}">{$key}</td>
	<td  style="background: #9fd39c; text-align: center;" class="" title="{$cxc.responsable.name}">${$cxc.saldoAnterior|number_format:2}</td>
    <td  style="background: #9fd39c; text-align: center;" class="" title="{$cxc.responsable.name}">${$cxc.total|number_format:2}</td>
    <td  style="background: #9fd39c; text-align: center;" class="" title="{$cxc.responsable.name}">${$cxc.payment|number_format:2}</td>
    <td  style="background: #9fd39c; text-align: center;" class="" title="{$cxc.responsable.name}">${$cxc.saldo|number_format:2}</td>
</tr>
<tr class="class-{$smarty.foreach.totales.index}" id="folio-{$smarty.foreach.totales.index}" style="display:none">
	<td style="text-align:center;width: 1%;font-weight: bold" colspan="1">&nbsp</td>
	<td style="background: #06b8f6;text-align:center;width: 3%;font-weight: bold">Folio</td>
	<td style="background: #06b8f6;text-align:center;width: 3%;font-weight: bold">Facturador</td>
	<td style="background: #06b8f6;text-align:center;font-weight: bold">Fecha</td>
	<td style="background: #06b8f6;text-align:right;font-weight: bold">Importe</td>
	<td style="background: #eae527;text-align:right;font-weight: bold">Pagos</td>
	<td style="background: #06b8f6;text-align:right;font-weight: bold">Saldo</td>
	<td>&nbsp</td>
</tr>

	{foreach from=$totales.$key.facturas item=factura key=kf name=facturas}
	<tr class="class-{$smarty.foreach.totales.index}" id="{$smarty.foreach.totales.index}-{$smarty.foreach.facturas.index}"  style="display:none">
		<td colspan="1" title="pagos de factura {$factura.serie}{$factura.folio}" style="width: 1%">
			<div id="{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" class="showPayment" style="cursor:pointer" >
				<span style="color: blue;" id="color_py_{$smarty.foreach.facturas.iteration}">[+]</span>
			</div>
		</td>
		<td align="center" style="background: #3df5ff;padding-left:15px;">{$factura.serie}{$factura.folio}</td>
		<td align="center" style="background: #3df5ff;padding-left:15px;">{$factura.facturador}</td>
		<td align="center" style="background: #3df5ff;padding-right:15px">{$factura.fecha}</td>
		<td align="right" style="background: #3df5ff" >${$factura.total_formato|number_format:2}</td>
		<td align="right" style="background: #eae527" >${$factura.payment|number_format:2}</td>
		<td align="right" style="background: #3df5ff" >${$factura.saldo|number_format:2}</td>
		<td>&nbsp</td>
	</tr>
		<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="pagos-{$smarty.foreach.facturas.index}" style="display:none">
			<td colspan="2"></td>
			<td colspan="5"  style="background: #eae527;text-align:center;font-weight: bold">Pagos de {$factura.serie}{$factura.folio}</td>
		</tr>
		<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="pagos-{$smarty.foreach.facturas.index}" style="display:none">
			<td colspan="2">&nbsp</td>
			<td style="background: #eae527;text-align: left;font-weight: bold;">Folio</td>
			<td style="background: #eae527;text-align: left;font-weight: bold;">Fecha</td>
			<td style="background: #eae527;text-align: left;font-weight: bold;">Metodo de pago</td>
			<td style="background: #eae527;text-align: left;font-weight: bold;">Importe</td>
			<td style="background: #eae527;text-align: left;font-weight: bold;">Deposito</td>
		</tr>
		{foreach from=$factura.payments item=payment  name=payments}
			<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="{$smarty.foreach.facturas.index}-{$smarty.foreach.payments.index}"  style="display:none">
				<td colspan="2">&nbsp</td>
				<td align="left">{$payment.folioPago}</td>
				<td align="left">{$payment.paymentDate}</td>
				<td align="left" >{$payment.mpago}</td>
				<td align="left" >${$payment.amount|number_format:2}</td>
				<td align="left" >${$payment.deposito|number_format:2}</td>
			</tr>
		{foreachelse}
			<tr class="showPayment-{$smarty.foreach.facturas.index}-{$smarty.foreach.totales.index}" id="{$smarty.foreach.facturas.index}-{$smarty.foreach.payments.index}"  style="display:none">
				<td colspan="2" align="center"></td>
				<td colspan="5" align="center">No existe movimientos de pagos para esta factura.</td>
			</tr>
		{/foreach}
	{/foreach}
{foreachelse}
<tr>
	<td colspan="9" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>