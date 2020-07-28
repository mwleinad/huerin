<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
{assign var="totalColspan" value=($meses|count)}
<thead>
	<tr>
		<th style="width:20%;font-size:12px;font-weight: bold;">Cliente</th>
		<th style="width:20%;font-size:12px;font-weight: bold;">Razon Social</th>
		<th style="width:20%;font-size:12px;font-weight: bold;">Responsable de cxc</th>
		{foreach from=$meses item=mes}
			<th style="font-size:12px;font-weight: bold;">{$mes}</th>
		{/foreach}
		<th style="font-size:12px;font-weight: bold;">Total facturado</th>
		<th style="font-size:12px;font-weight: bold;">Total cobrado</th>
		<th style="font-size:12px;font-weight: bold;">Diferencia</th>
	</tr>
</thead>
	{foreach from=$items item=item key=key}
		{foreach from=$item.contratos item=contrato}
		<tr>
			<td align="justify">{$contrato.customer}</td>
			<td align="justify">{$contrato.razon}</td>
			<td align="justify">{$item.name}</td>s
				{foreach from=$contrato.facturas item=factura}
					{if $pago.isColTotal}
						<td style="text-align: center">$ {$factura.total|number_format:2:'.':','}</td>
					{else}
						<td	style="
								{if $factura.class eq 'pagado'}
										background-color:#009900 !important;color:#FFF;
								{else}
									{if $factura.class eq 'pendiente'}
											background-color:#FFCC00 !important;color:#FFFFFF;
									{else}
										{if $factura.class == "sinabonos"}
												background-color:#FF0000 !important;color:#FFFFFF
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
			<td><b>Total facturado</b></td>
			{foreach from=$rowDevTotal[$key] item=tot}
				<td>$ {$tot|number_format:2:'.':','}</td>
			{/foreach}
			<td>{$totDevVerXEncargado[$key]|number_format:2:'.':','}</td>
			<td>{$totCompVerXEncargado[$key]|number_format:2:'.':','}</td>
			<td>{$totDevVerXEncargado[$key]-$totCompVerXEncargado[$key]|number_format:2:'.':','}</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="{$totalColspan+6}" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
	{/foreach}
	<tr>
		<td colspan="{$totalColspan+2}"></td>
		<td><b>Grantotal cobrado</b></td>
		<td>{$granTotalDevengado|number_format:2:'.':','}</td>
		<td>{$granTotalCompletado|number_format:2:'.':','}</td>
		<td>{$granTotalDevengado-$granTotalCompletado|number_format:2:'.':','}</td>
	</tr>
	<tr>
		<td rowspan="9" colspan="{$totalColspan+6}"></td>
	</tr>
</tbody>
</table>

<div style="display: table;width: 100%; border-spacing: 10px">
	<div style="display: table-cell;width: 35%">
		<table width="50%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
			<thead>
			<tr>
				<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%">Nombre</th>
				<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">Ingresos</th>
				<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">Gastos</th>
				<th class="cabeceraTabla" align="center" style="text-align: left">Utilidad</th>
				<th class="cabeceraTabla" align="center" style="text-align: left">% BONO</th>
				<th class="cabeceraTabla" align="center" style="text-align: left">BONO</th>
				<th class="cabeceraTabla" align="center" style="text-align: left">Bono Entregado</th>
			</tr>
			</thead>
			<tbody>
			{foreach from=$totalesAcumulados item=enc key=ken}
				{math equation ='I-G' I=$enc.totalCompletado G=$enc.sueldoTotal assign=utilidad}
				<tr>
					<td>{$enc.name}</td>
					<td>{$enc.totalCompletado|number_format:2:'.':','}</td>
					<td>{$enc.sueldoTotal|number_format:2:'.':','}</td>
					<td>{$utilidad|number_format:2:'.':','}</td>
					<td>{$enc.porcentajeBono} %</td>
					<td>{if $utilidad>0}{($utilidad*({$enc.porcentajeBono}/100))|number_format:2:'.':','}{else}0.00{/if}</td>
					<td>{if $utilidad>0}{($utilidad*({$enc.porcentajeBono}/100))|round:2|number_format:2:'.':','}{else}0.00{/if}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>
