<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
    <tr rowspan="10">
		<th colspan="7" class="divInside" align="center">
               DESGLOSE DE SUBORDINADOS
		</th>
	</tr>
	<tr>
		<th align="center"  class="cabeceraTabla">AUXILIAR</th>
		<th align="center" class="cabeceraTabla">CONTADOR</th>
		<th align="center"  class="cabeceraTabla">SUPERVISOR</th>
		<th align="center" class="cabeceraTabla">GERENTE</th>
		<th align="center"  class="cabeceraTabla">DIRECTOR</th>

	</tr>
</thead>
<tbody>
{assign var='totalDeposito' value="0"}
{foreach from=$registros item=item key=key}
		<tr>
			<td align="center">{if $item.auxiliar eq ''}--{else}{$item.auxiliar}{/if}</td>
			<td align="center">{if $item.contador eq ''}--{else}{$item.contador}{/if}</td>
			<td align="center">{if $item.supervisor eq ''}--{else}{$item.supervisor}{/if}</td>
			<td align="center">{if $item.gerente eq ''}--{else}{$item.gerente}{/if}</td>
			<td align="center">{if $item.jefeMax eq ''}--{else}{$item.jefeMax}{/if}</td>
	 	</tr>
{foreachelse}
<tr>
	<td colspan="5" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>