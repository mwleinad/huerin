<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
    <tr rowspan="10">
		<th colspan="5" class="divInside" align="center">
               REPORTE DE  FACTURAS CANCELADAS DE BHSC CONTADORES SC CORRESPONDIENTE AL MES DE {$mes|upper} DEL {$anio|upper}
		</th>
	</tr>
	<tr>
		<th align="center" width="60" class="cabeceraTabla">FOLIO FACTURA</th>
		<th align="center" width="60" class="cabeceraTabla">FECHA DE EMISION</th>
		<th align="center" width="60" class="cabeceraTabla">NOMBRE DEL CLIENTE</th>
		<th align="center" width="60" class="cabeceraTabla">RAZON SOCIAL</th>
		<th align="center" width="60" class="cabeceraTabla">IMPORTE</th>
		<th align="center" width="60" class="cabeceraTabla">FECHA DE CANCELACION</th>
		<th align="center" width="60" class="cabeceraTabla">MOTIVO DE CANCELACION</th>

	</tr>
</thead>
<tbody>
{assign var='totalMonto' value="0"}
{foreach from=$registros item=item key=key}
		<tr>
			<td align="center">{$item.folio}</td>
			<td align="center">{$item.fecha}</td>
			<td align="center">{$item.cliente}</td>
			<td align="center">{$item.razon}</td>
			<td align="center">${$item.monto|number_format:2:'.':','}</td>
			<td align="center">{$item.fechaPedimento}</td>
			<td align="center">{$item.motivoCancelacion}</td>
	 	</tr>
    {math equation='x+y' x=$totalMonto y=$item.monto assign="totalMonto"}
{foreachelse}
<tr>
	<td colspan="6" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
<tr>s
	<td colspan="3"></td>
	<td align="right"><b>TOTAL MONTO CANCELADO</b></td>
	<td align="center">$ {$totalMonto|number_format:2:'.':','}</td>
	<td colspan=""></td>
	<td colspan=""></td>
</tr>
</tbody>
</table>