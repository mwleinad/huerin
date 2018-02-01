<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	<thead>
		<tr>
			<th class="cabeceraTabla" align="center" >Cliente</th>
			<th class="cabeceraTabla" align="center" >C. Asignado</th>
			<th class="cabeceraTabla" align="center" >Razon Social</th>
			<th class="cabeceraTabla" align="center" >Servicio</th>
			{foreach from=$nombreMeses item=mes}
				<th class="cabeceraTabla" align="center" width="50px" >{$mes}</th>
			{/foreach}
			<th class="cabeceraTabla" align="center" >Total</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$abcdario item=Letra key=keyLetra}
			{foreach from=$clientes item=cliente key=key}
				{foreach from=$cliente.contracts item=contract key=keyContract}
					{foreach from=$contract.instanciasServicio item=servicio key=keyServicio}
						{if $Letra == $servicio.LETRA && $keyLetra == $servicio.POCICION}
						<tr>
							<td align="left" class="" title="{$contract.responsable.name}">{$cliente.nameContact}</td>
							<td align="left" class="" title="{$contract.responsable.name}">{$servicio.responsable|upper}</td>
							<td align="left" class="" title="{$contract.responsable.name}">{$contract.name}</td>
							<td align="center" class="" title="{$contract.responsable.name}">{$servicio.nombreServicio}</td>
							{foreach from=$servicio.instancias item=instanciaServicio}

								<td align="center"
								{if $EXEL == "SI"}
									{if $instanciaServicio.status neq 'inactiva'}
										{if $instanciaServicio.class eq 'CompletoTardio' || $instanciaServicio.class eq 'Completo'}
											style="background-color:#009900 !important;color:#FFF"
										{else}
											{if $instanciaServicio.class eq 'Iniciado' || $instanciaServicio.class eq 'PorCompletar'}
												style="background-color:#FC0 !important;color:#FFF"
											{else}
												{if $instanciaServicio.class == "PorIniciar"}
													style="background-color:#F00 !important;color:#FFF"
												{/if}
											{/if}
										{/if}
									{/if}

								{else}
									class="
									{if $instanciaServicio.status neq 'inactiva'}
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
								{/if}

								title="{$servicio.nombreServicio} {if $instanciaServicio.status neq 'inactiva'}{if $instanciaServicio.class eq 'CompletoTardio'}{'Completo'}{else}{if $instanciaServicio.class eq 'Iniciado'}{'PorCompletar'}{else}{$instanciaServicio.class}{/if}{/if}{/if}">
									{if $NODIV eq 'SI'}
                                        {if $instanciaServicio.class eq 'Completo' || $instanciaServicio.class eq 'CompletoTardio'}
											${$servicio.costo|number_format:2:".":","}
                                        {else}
											-
                                        {/if}
                                        {if $instanciaServicio.status eq 'inactiva'}
											<span style="color:#DA9696">(Inactivo)</span>
                                        {/if}
									{else}
										<div style="cursor:pointer" onclick="GoToWorkflow('report-servicios', '{$instanciaServicio.instanciaServicioId}')">
                                            {if $instanciaServicio.class eq 'Completo' || $instanciaServicio.class eq 'CompletoTardio'}
												${$servicio.costo|number_format:2:".":","}
                                            {else}
												-
                                            {/if}
                                            {if $instanciaServicio.status eq 'inactiva'}
												<span style="color:#DA9696">(Inactivo)</span>
                                            {/if}
										</div>
									{/if}
								</td>
							{/foreach}
							<td align="center">${$servicio.sumatotal|number_format:2}</td>
						</tr>
						{/if}
					{/foreach}
				{/foreach}
			{foreachelse}
				{if $keyLetra == 0}
				<tr>
					<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
				</tr>
				{/if}
			{/foreach}
		{/foreach}

	</tbody>
</table>