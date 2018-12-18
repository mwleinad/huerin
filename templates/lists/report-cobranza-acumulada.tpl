<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th style="width:20%;">Cliente</th>
		<th style="width:20%;">Razon Social</th>
		{foreach from=$meses item=mes}
			<td align="center">{$mes}</td>
		{/foreach}
	</tr>
</thead>
	{foreach from=$contratos item=item name=contratos}
		<tr>
    		<td align="justify">{$item.customer}</td>
    		<td align="justify">{$item.razon}</td>
				{foreach from=$item.pagos item=pago name=pagos}
					<td align="center">$ {$pago.totalAmount|number_format:2:'.':','}</td>
				{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td colspan="16" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
		{/foreach}
</tbody>
</table>