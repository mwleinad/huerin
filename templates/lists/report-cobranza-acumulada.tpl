<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th style="width:20%;font-size:12px;font-weight: bold;">Cliente</th>
		<th style="width:20%;font-size:12px;font-weight: bold;">Razon Social</th>
		{foreach from=$meses item=mes}
			<th style="font-size:12px;font-weight: bold;">{$mes}</th>
		{/foreach}
		<th style="font-size:12px;font-weight: bold;">Total</th>
	</tr>
</thead>
	{foreach from=$contratos item=item name=contratos}
		<tr>
    		<td align="justify">{$item.customer}</td>
    		<td align="justify">{$item.razon}</td>
				{foreach from=$item.pagos item=pago name=pagos}
					{if $pago.isColTotal}
						<td align="center">$ {$pago.total|number_format:2:'.':','}</td>
					{else}
						<td align="center">$ {$pago.amount|number_format:2:'.':','}</td>
					{/if}
				{/foreach}
		</tr>
		{foreachelse}
		<tr>
			<td colspan="16" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
		{/foreach}
</tbody>
</table>