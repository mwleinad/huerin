<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
    <tr rowspan="10">
		<th colspan="5" class="divInside" align="center">
               REPORTE DE COBRANZA DE BRAUN HUERIN SC CORRESPONDIENTE AL MES DE {$mes|upper}
		</th>
	</tr>
	<tr>
		<th align="center" width="60" class="cabeceraTabla">FECHA</th>
		<th align="center" width="60" class="cabeceraTabla">NOMBRE DEL CLIENTE</th>
		<th align="center" width="60" class="cabeceraTabla">RAZON SOCIAL</th>
		<th align="center" width="60" class="cabeceraTabla">NUMERO DE FACTURA</th>
		<th align="center" width="60" class="cabeceraTabla">DEPOSITO</th>

	</tr>
</thead>
<tbody>
{assign var='totalDeposito' value="0"}
{foreach from=$registros item=item key=key}
		<tr>
			<td align="center">{$item.paymentDate}</td>
			<td align="center">{$item.nameContact}</td>
			<td align="center">{$item.name}</td>
			<td align="center">{$item.factura}</td>
			<td align="center">${$item.deposito|number_format:2:'.':','}</td>
	 	</tr>
    {math equation='x+y' x=$totalDeposito y=$item.deposito assign="totalDeposito"}
{foreachelse}
<tr>
	<td colspan="5" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
<tr>
	<td colspan="3"></td>
	<td align="right"><b>TOTAL</b></td>
	<td align="center">{$totalDeposito|number_format:2:'.':','}</td>
</tr>



</tbody>
</table>