<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px;">
<thead>
	<tr>
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">Encargado de servicio</th>
		<th align="center" width="60">Resp. de comunicación</th>
		<th align="center" width="60">Asociado</th>
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
		{if in_array(248,$permissions)||$User.isRoot}
		<th align="center" width="10"></th>
		{/if}
	</tr>
</thead>
<tbody>
		{foreach from=$cleanedArray item=item key=key}
		{if $item.isRowCobranza && (in_array(210,$permissions) ||$User.isRoot)}
			<tr>
				<td colspan="5" align="center"><b>Total cobranza mensual</b></td>
				{foreach from=$item.instanciasServicio item=instanciaServicio}
                    {if $instanciaServicio.class == '#000000'}
                        <td style="text-align: center;">
                            No se emitieron facturas
                        </td>
                    {else}
					<td align="center"
						style="	{if $instanciaServicio.class == '#000000'}
								{else}
										background:{$instanciaServicio.class};
								{/if}
								{if $instanciaServicio.class == '#00ff00' || $instanciaServicio.class == '#FFCC00' || $instanciaServicio.class == '#EFEFEF'}
								color:#000000; {else}
								color:#ffffff;
								{/if}
								font-weight: bold;
								">
						{if $instanciaServicio.class != '#000000'}
							<b>$ {$instanciaServicio.total|number_format:2:".":","}</b>
							<br>
								<a href="javascript:;"  title="Ver detalles" class="spanAll detailCobranza" data-datos='{ "contractId":{$item.contractId},"mes":{$instanciaServicio.mes},"year":{$instanciaServicio.anio} }'>
								<img src="{$WEB_ROOT}/images/icons/search-plus-green-18.png" border="0" />
								</a>
							{if $instanciaServicio.status ==0}
								<br>
								<small>Canceladas</small>
							{/if}
						{/if}
					</td>
                    {/if}
				{/foreach}
				{if in_array(248,$permissions)||$User.isRoot}
					<td></td>
				{/if}
			</tr>
		{else}
		<tr>
    		<td align="center" class="" title="">{$item.nameContact}</td>
    		<td align="center" class="" title="">{$item.name}</td>
			<td align="center" class="" title="{$item.responsable}">{if $item.responsable} {$item.responsable} {else} Responsable no configurado {/if}</td>
			<td align="center" class="" title="{$item.resComunicacion}">{if $item.resComunicacion} {$item.resComunicacion} {else} Responsable no configurado {/if}</td>
			<td align="center" class="" title="{$item.resAsociado}">{if $item.resAsociado} {$item.resAsociado} {else} Responsable no configurado {/if}</td>
				{foreach from=$item.instanciasServicio item=instanciaServicio name=instancias}
					<td align="center"
					  class="
						 {if $instanciaServicio.status neq 'inactiva'}
							{if $instanciaServicio.class eq 'CompletoTardio'}
							  st{'Completo'} txtSt{'Completo'}
							{else}
							  {if $instanciaServicio.class eq 'Iniciado'}
			 					st{'PorCompletar'} txtSt{'PorCompletar'}
							  {elseif $instanciaServicio.class eq 'Parcial'}
								st{'Parcial'} txtSt{'Parcial'}
							  {else}
							  	st{$instanciaServicio.class} txtSt{$instanciaServicio.class}
							  {/if}
							{/if}
						  {/if}
						"
					  title="{$item.nombreServicio} {if $instanciaServicio.status neq 'inactiva'}{if $instanciaServicio.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.class}{/if}{/if}{/if}">
					<div  {if $instanciaServicio.instanciaServicioId && (in_array(100,$permissions)||$User.isRoot)}style="cursor:pointer;" style="cursor:pointer;" onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')"{/if}>
					{$item.nombreServicio|truncate:5:""}
						{if $instanciaServicio.instanciaServicioId && $instanciaServicio.status eq 'inactiva'}<span style="color:#DA9696;"><b>(Inactivo)</b></span>{/if}
					</div>
						{if $instanciaServicio.instanciaServicioId && (in_array(99,$permissions)||$User.isRoot)}
							<a href="javascript:;" class="spanDownloadFiles spanAll" data-id="{$instanciaServicio.instanciaServicioId}" data-month="{$instanciaServicio.mes}" data-type="downloadFilesMonth" data-contrato="{$item.contractId}" data-year="{$item.anio}" style="color:#FFF;font-weight:700;" title="Descargar archivos del mes">Archivos</a>
						{/if}
					</td>
				{/foreach}
			{if $item.instanciasServicio && (in_array(248,$permissions)||$User.isRoot)}
				<td>
					<a href="javascript:;" class="spanDownloadFiles spanAll" title="Descargar archivos anual" data-id="{$item.servicioId}" data-month="" data-type="downloadFilesYear" data-contrato="{$item.contractId}" data-year="{$item.anio}">
						<img src="{$WEB_ROOT}/images/icons/downFile.png" class="no-clickable">
					</a>
				</td>
			{/if}
		</tr>
		{/if}
		{foreachelse}
		<tr>
			<td colspan="{if in_array(248,$permissions)||$User.isRoot}18{else}16{/if}" style="text-align: center;">Ning&uacuten registro encontrado</td>
		</tr>
		{/foreach}
</tbody>
</table>
