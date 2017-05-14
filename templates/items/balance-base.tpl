{foreach from=$data.movimientos item=item key=key}
{*} {if $item.saldo > 0} {*}
	<tr id="1">
		<td align="left">{$item.concepto}</td>
		<td align="center">{$item.fecha}</td>
		<td align="right">{if $item.cargo == 0}$--{else}${$item.cargo|number_format:2}{/if}</td>
		<td align="right">{if $item.abono == 0}$--{else}${$item.abono|number_format:2}{/if}</td>
		<td align="right">${$item.saldo|number_format:2}</td>
	</tr>
{*} {/if} {*}
{foreachelse}
<tr><td colspan="6" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
	<tr id="1">
		<td align="left">Totales</td>
		<td align="center"></td>
		<td align="right"><b>{if $data.cargos == 0}$--{else}${$data.cargos|number_format:2}{/if}</b></td>
		<td align="right"><b>{if $data.abonos == 0}$--{else}${$data.abonos|number_format:2}{/if}</b></td>
		<td align="right"><b>${$data.saldo|number_format:2}</b></td>
	</tr>
