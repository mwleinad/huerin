<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">Responsable</th>
		<th align="center" width="50">Ene</th>
		<th align="center" width="50">Feb</th>
		<th align="center" width="50">Mar</th>
		<th align="center" width="50">Abr</th>
		<th align="center" width="50">May</th>
		<th align="center" width="50">Jun</th>
		<th align="center" width="50">Jul</th>
		<th align="center" width="50">Ago</th>
		<th align="center" width="50">Sep</th>
		<th align="center" width="50">Oct</th>
		<th align="center" width="50">Nov</th>
		<th align="center" width="50">Dic</th>
	</tr>
</thead>
<tbody>
<pre>
	</pre>
{foreach from=$cleanedArray item=item key=key}
		<tr>
			<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
			<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
			{foreach from=$item.instanciasServicio item=instanciaServicio}
                <td align="center" style="background-color:{$instanciaServicio.class}; {if $instanciaServicio.class == '#00ff00'} color: #000; {else} color: #fff; {/if} font-weight: bold">
				{if $instanciaServicio.class != '#000000'}
                	${$instanciaServicio.total|number_format:2:".":","}

					<br>
					{if $instanciaServicio.status == 1}
						<img src="{$WEB_ROOT}/images/icons/details.png" class="spanDetails{if $instanciaServicio.efectivo} spanEfectivo{/if}" id="{$instanciaServicio.comprobanteId}" border="0" alt="Ver Detalle de Pagos" title="Ver Detalle de Pagos" style="cursor:pointer" />

						{if $instanciaServicio.class == "#ff0000"}
							<a href="{$WEB_ROOT}/add-payment/{if !$instanciaServicio.efectivo}id{else}isid{/if}/{$instanciaServicio.comprobanteId}" target="_blank"><img src="{$WEB_ROOT}/images/dollar.png" class="" id="{$instanciaServicio.comprobanteId}" border="0" alt="Agregar Pago" title="Agregar Pago" /></a>
						{/if}

						<a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$instanciaServicio.comprobanteId}" target="_blank"><img src="{$WEB_ROOT}/images/icons/ver_factura.png" id="{$instanciaServicio.comprobanteId}" border="0" alt="Ver Factura" width="16" title="Ver Factura" /></a>
					{else}
						Cancelada
					{/if}
				{/if}
                </td>
        {/foreach}
	 	</tr>
{foreachelse}
<tr>
	<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}


</tbody>
</table>