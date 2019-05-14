<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th style="width:20%;font-size:12px;font-weight: bold;">Cliente</th>
		<th style="width:20%;font-size:12px;font-weight: bold;">Razon Social</th>
		<th style="width:20%;font-size:12px;font-weight: bold;">Responsable de cxc</th>
		{foreach from=$meses item=mes}
			<th style="font-size:12px;font-weight: bold;">{$mes}</th>
		{/foreach}
		<th style="font-size:12px;font-weight: bold;">Total devengando</th>
		<th style="font-size:12px;font-weight: bold;">Total cobrado</th>
		<th style="font-size:12px;font-weight: bold;">Diferencia</th>
	</tr>
</thead>
	{foreach from=$items item=item key=key}
		{foreach from=$item.contratos item=contrato}
			<tr>
				<td align="justify">{$contrato.customer}</td>
				<td align="justify">{$contrato.razon}</td>
				<td align="justify">{$item.responsable}</td>
					{foreach from=$contrato.facturas item=factura}
						{if $pago.isColTotal}
							<td style="text-align: center">$ {$factura.total|number_format:2:'.':','}</td>
						{else}
							<td	style="
									{if $factura.class eq 'pagado'}
											background-color:#009900 !important;color:#FFF;
									{else}
										{if $factura.class eq 'pendiente'}
												background-color:#FC0 !important;color:#FFF;
										{else}
											{if $factura.class == "sinabonos"}
													background-color:#F00 !important;color:#FFF
											{/if}
										{/if}
									{/if}">
								$ {$factura.total|number_format:2:'.':','}
							</td>
						{/if}
					{/foreach}
			</tr>
		{/foreach}
		<tr>
			<td colspan="2"></td>
			<td><b>Total cobrado</b></td>
			{foreach from=$rowCobTotal[$key] item=tot}
				<td>$ {$tot|number_format:2:'.':','}</td>
			{/foreach}
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td><b>Total devengado</b></td>
			{foreach from=$rowDevTotal[$key] item=tot}
				<td>$ {$tot|number_format:2:'.':','}</td>
			{/foreach}
			<td></td>
			<td></td>
			<td></td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="16" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
	{/foreach}
</tbody>
</table>