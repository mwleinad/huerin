<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
<thead>
	<tr>
		<!--<th align="center" width="60">Comentario</th>-->
		<th align="center" width="60">Cliente</th>
		<th align="center" width="60">Razon Social</th>
		<th align="center" width="60">C. Asignado</th>
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
		{foreach from=$cleanedArray item=item key=key}
		{if $item.isRowCobranza && (in_array(210,$permissions) ||$User.isRoot)}
			<tr>
				<td colspan="3" align="center"><b>Total cobranza mensual</b></td>
				{foreach from=$item.instanciasServicio item=instanciaServicio}
                    {if $instanciaServicio.class == '#000000'}
                        <td>
                            Sin factura
                        </td>
                    {else}
					<td align="center"
						style="{if $instanciaServicio.class == '#00ff00'}
								color: #28a119; {else}
								color: #d94438; {/if}
								font-weight: bold
								">
						{if $instanciaServicio.class != '#000000'}
							$ {$instanciaServicio.total|number_format:2:".":","}
							<br>
							{if $instanciaServicio.status == 1}
								{if $instanciaServicio.version == '3.3'}
									<a target="_blank" href="{$WEB_ROOT}/cfdi33-generate-pdf&filename=SIGN_{$instanciaServicio.xml}&type=view" title="Ver factura">
										<img src="{$WEB_ROOT}/images/icons/ver_factura.png" height="16" width="16" border="0"/>
									</a>
								{else}
									<a href="{$WEB_ROOT}/sistema/ver-pdf/item/{$instanciaServicio.comprobanteId}" target="_blank" title="Ver factura">
										<img src="{$WEB_ROOT}/images/icons/ver_factura.png" border="0" width="16" />
									</a>
								{/if}
							    {else}
								Cancelada
							{/if}
						{/if}
					</td>
                    {/if}
				{/foreach}
			</tr>
		{else}
		<tr>
			<!--
			<td align="center" class="" title="{$item.nameContact}">
				<span id="comentario-{$item.servicioId}">{$item.comentario}</span>
                    {if in_array(117,$permissions)||$User.isRoot}
						<img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" id="{$item.servicioId}" onclick="ModifyComment({$item.servicioId})"  title="Editar"/>
					{/if}
					{if in_array(99,$permissions)||$User.isRoot}
						<a href="{$WEB_ROOT}/download_all_tasks.php?id={$item.servicioId}" style="color:#FFF;font-weight:bold"><img src="{$WEB_ROOT}/images/b_disc.png" class="spanEdit" id="{$item.servicioId}" title="Descargar todos los archivos"/></a>
					{/if}
			</td>-->
    		<td align="center" class="" title="{$item.nameContact}">{$item.nameContact}</td>
    		<td align="center" class="" title="{$item.name}">{$item.name}</td>
			<td align="center" class="" title="{$item.responsable}">{$item.responsable}</td>
				{foreach from=$item.instanciasServicio item=instanciaServicio name=instancias}
					<td align="center"
					  class="{if $instanciaServicio.status neq 'inactiva'}
							{if $instanciaServicio.class eq 'CompletoTardio'}
							  st{'Completo'} txtSt{'Completo'}
							{else}
							  {if $instanciaServicio.class eq 'Iniciado'}
			 					st{'PorCompletar'} txtSt{'PorCompletar'}
							  {else}
								st{$instanciaServicio.class} txtSt{$instanciaServicio.class}
							  {/if}
							{/if}
						  {/if}"
					  title="{$item.nombreServicio} {if $instanciaServicio.status neq 'inactiva'}{if $instanciaServicio.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.class}{/if}{/if}{/if}">
					<div style="cursor:pointer" {if in_array(100,$permissions)||$User.isRoot}onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')"{/if}>
					{$item.nombreServicio|truncate:5:""}
					{if $instanciaServicio.status eq 'inactiva'}<span style="color:#DA9696">(Inactivo)</span>{/if}
						{if in_array(99,$permissions)||$User.isRoot}
						 <a href="{$WEB_ROOT}/download_tasks.php?id={$instanciaServicio.instanciaServicioId}" style="color:#FFF;font-weight:bold">Archivos</a>
						{/if}
					</div>
					</td>
				{/foreach}
		</tr>
		{/if}
		{foreachelse}
		<tr>
			<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
		</tr>
		{/foreach}
</tbody>
</table>