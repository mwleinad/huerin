<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Usuario</th>
		<th align="center" width="60">Acciones</th>
	</tr>
</thead>
<tbody>

{if $filasLog}
{foreach from=$filasLog item=fila key=key}
  
<tr >
    <td align="center" class="" title="{$contract.responsable.name}">{$fila.name}</td>
    <td align="center" class="" title="{$contract.responsable.name}"><a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$fila.comprobanteId}"><img title="Ver factura" src="{$WEB_ROOT}/images/icons/view.png"/></a></td>
</tr>
{/foreach}
{/if}

</tbody>
</table>