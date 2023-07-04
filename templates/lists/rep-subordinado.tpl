<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
{assign var='columnas' value="{$categorias|count}"}
<thead>
    <tr rowspan="10">
		<th colspan="{$columnas}" class="divInside" align="center">
               DESGLOSE DE SUBORDINADOS
		</th>
	</tr>
	<tr>
		{foreach from=$categorias item=item2 key=key2}
		<th align="center"  class="cabeceraTabla">{$item2.name}</th>
		{/foreach}

	</tr>
</thead>
<tbody>
{foreach from=$registros item=item key=key}
		<tr>
			{foreach from=$categorias item=item2 key=key2}
				<td align="center">{if $item[{$item2.name}] eq ''}--{else}{$item[{$item2.name}]} {/if}</td>
			{/foreach}
	 	</tr>
{foreachelse}
<tr>
	<td colspan="{$columnas}" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>
