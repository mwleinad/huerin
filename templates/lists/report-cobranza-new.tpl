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
{foreach from=$cleanedArray item=item key=key}
		<tr>
			<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
			<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
			{foreach from=$item.instanciasServicio item=instanciaServicio}
                <td align="center" style="background-color:{$instanciaServicio.class}; {if $instanciaServicio.class == '#00ff00' || $instanciaServicio.class == '#FC0' || $instanciaServicio.class == '#EFEFEF'} color: #000; {else} color: #fff; {/if} font-weight: bold">
				{if $instanciaServicio.class != '#000000'}
                	${$instanciaServicio.total|number_format:2:".":","}
					<br>
						<a href="javascript:;"  title="Ver detalles" class="spanAll detailCobranza" data-datos='{ "contractId":{$item.contractId},"mes":{$instanciaServicio.mes},"year":{$instanciaServicio.anio} }'>
							<img src="{$WEB_ROOT}/images/icons/search-plus-green-18.png" border="0"/>
						</a>
						{*if $instanciaServicio.class == "#ff0000"}
							<a href="{$WEB_ROOT}/add-payment/{if !$instanciaServicio.efectivo}id{else}isid{/if}/{$instanciaServicio.comprobanteId}" target="_blank">
								<img src="{$WEB_ROOT}/images/dollar.png" class="" id="{$instanciaServicio.comprobanteId}" border="0" alt="Agregar Pago" title="Agregar Pago" />
							</a>
						{/if}
						{if $instanciaServicio.version == '3.3'}
							<a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=SIGN_{$instanciaServicio.xml}&type=view" title="Ver PDF">
								<img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
							</a>
						{else}
							<a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$instanciaServicio.comprobanteId}" target="_blank" title="Ver Factura">
								<img src="{$WEB_ROOT}/images/icons/ver_factura.png" border="0" width="16" />
							</a>
						{/if*}

					{if $instanciaServicio.status == 0}<br>Cancelada{/if}
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