<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">Responsable</th>
		{foreach from=$mesesComplete item=mes}
			<th align="center" width="60">{$mes}</th>
		{/foreach}
	</tr>
</thead>
<tbody>
{foreach from=$cleanedArray item=item key=key}
		<tr>
			<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
			<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
			{foreach from=$item.instanciasServicio item=instanciaServicio}
				{if $instanciaServicio.class == '#000000'}
					<td style="text-align: center">
						No se emitieron facturas
					</td>
				{else}
					<td align="center" style="background: {$instanciaServicio.class};color: #{if $instanciaServicio.class == '#00ff00' || $instanciaServicio.class == '#FC0' || $instanciaServicio.class == '#EFEFEF'}000000{else}ffffff{/if}; font-weight: bold">
						${$instanciaServicio.total|number_format:2:".":","}
						<br>
							<a href="javascript:;"  title="Ver detalles" class="spanAll detailCobranza" data-datos='{ "contractId":{$item.contractId},"mes":{$instanciaServicio.mes},"year":{$instanciaServicio.anio} }'>
								<img src="{$WEB_ROOT}/images/icons/search-plus-green-18.png" border="0"/>
							</a>
						{if $instanciaServicio.status == 0}<br>Canceladas{/if}
					</td>
				{/if}
        {/foreach}
	 	</tr>
{foreachelse}
<tr>
	<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
</tr>
{/foreach}
</tbody>
</table>
