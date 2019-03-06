<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	<thead>
	    <tr>
			<th style="width:10%;"><b>Total de razones sociales en plataforma<b></th>
			<th style="width:10%;">{$data.totalContratos|number_format:2:'.':','}</th>
		</tr>
		<tr>
			<th style="width:10%;"><b>{if $data.responsable eq ""}Todas las razones sociales{else}Razones sociales del responsable {$data.responsable}{/if}{if $data.subordinados}(incluyendo sus subordinados){/if}</b></th>
			<th style="width:10%;">{$data.contratosAsignados|number_format:2:'.':','}</th>
		</tr>
		<tr>
			<th style="width:10%;"><b>Porcentaje de razones sociales</b></th>
			<th style="width:10%;">{$data.porcentajeAsignado|string_format:'%.2f'}%</th>
		</tr>
		<tr>
			<th colspan="2"></th>
		</tr>
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
		{foreach from=$data.contratos item=item key=key}
			{foreach from=$item.servicios item=servicio key=ks}
				{if $servicio.isRowCobranza}
					<tr>
						<td align="center" colspan="4">Total cobranza</td>
						{foreach from=$servicio.instancias item=instanciaServicio}
							{if $instanciaServicio.class == '#000000'}
								<td style="text-align: center">
									No se emitieron facturas
								</td>
							{else}
								<td align="center"
									style="
									{if $instanciaServicio.class == '#000000'}
									{else}
										background-color:{$instanciaServicio.class};
									{/if}
									{if $instanciaServicio.class == '#00ff00' || $instanciaServicio.class == '#FC0' || $instanciaServicio.class == '#EFEFEF'}
										color: #000000; {else}
										color: #ffffff;
									{/if}
									font-weight: bold">

									{if $instanciaServicio.class != '#000000'}
										$ {$instanciaServicio.total|number_format:2:".":","}
										{if $EXCEL !='SI'}
										<br>
										<a href="javascript:;"  title="Ver detalles" class="spanAll detailCobranza" data-datos='{ "contractId":{$item.contractId},"mes":{$instanciaServicio.mes},"year":{$instanciaServicio.anio} }'>
											<img src="{$WEB_ROOT}/images/icons/search-plus-green-18.png" border="0" />
										</a>
										{/if}
										{if $instanciaServicio.status ==0}
											<br>
											<small>Canceladas</small>
										{/if}
									{/if}
								</td>
							{/if}
						{/foreach}
						<td align="center">${$servicio.sumatotal|number_format:2}</td>
					</tr>
				{else}
				<tr>
					<td align="left" class="" title="">{$item.nameContact}</td>
					<td align="left" class="" title="">{$servicio.responsable}</td>
					<td align="left" class="" title="">{$item.name}</td>
					<td align="center" class="" title="">{$servicio.nombreServicio}</td>
					{foreach from=$servicio.instancias item=instanciaServicio}
					<td align="center"
				        {*start if EXCEL*}
						{if $EXCEL eq 'SI'}
							style="{if $instanciaServicio.class eq 'CompletoTardio' || $instanciaServicio.class eq 'Completo'}
									background-color:#009900 !important;color:#FFF;
									{else}
										{if $instanciaServicio.class eq 'Iniciado' || $instanciaServicio.class eq 'PorCompletar'}
											background-color:#FC0 !important;color:#FFF;
										{else}
											{if $instanciaServicio.class == "PorIniciar"}
												background-color:#F00 !important;color:#FFF
											{/if}
										{/if}
								   {/if}"
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
					{*end if EXCEL*}
					>
					<div style="cursor:pointer" >
						{*if $instanciaServicio.class eq 'Completo' || $instanciaServicio.class eq 'CompletoTardio'}

						{else}
							-
						{/if*}
						${$instanciaServicio.costo|number_format:2:".":","}
						{if $instanciaServicio.status eq 'baja'}
							<span style="color:#DA9696">(Inactivo)</span>
						{/if}
					</div>
					</td>
					{/foreach}
					<td align="center">${$servicio.sumatotal|number_format:2}</td>
				 </tr>
				{/if}
			{/foreach}
		{foreachelse}
				<tr>
					<td colspan="8" align="center">Ning&uacute;n registro encontrado.</td>
				</tr>
		{/foreach}
	</tbody>
</table>
<div style="display: table;width: 100%; border-spacing: 10px">
	<div style="display: table-cell;width: 20%">
		<table width="100%" cellpadding="0" cellspacing="0" style="font-size:10px">
			<thead>
				<th colspan="2">Total general</th>
			</thead>
			<tbody>
				<tr>
					<td><b>TOTAL DEVENGADO</b></td>
					<td>{$data.granTotalContabilidad|number_format:2:'.':','}</td>
				</tr>
				<tr>
					<td><b>TOTAL COBRANZA</b></td>
					<td>{$data.granTotalCobranza|number_format:2:'.':','}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="display: table-cell;width: 25%">
		<table width="100%" cellpadding="0" cellspacing="0"  style="font-size:10px">
			<thead>
				<th colspan="2">Total trabajado por departamento</th>
			</thead>
			<tbody>
			{foreach from=$data.totalesXdepartamentos item=txd key=ktxd}
				<tr>
					<td><b>{$txd.departamento}</b></td>
					<td>{$txd.total|number_format:2:'.':','}</b></td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<div style="display: table-cell;width: 25%">
		<table width="100%" cellpadding="0" cellspacing="0" style="font-size:10px">
			<thead>
				<th colspan="2">Total por encargado</th>
			</thead>
			<tbody>
			{foreach from=$data.totalesXencargados item=txe key=ktxe}
				<tr>
					<td><b>{$txe.name}</b></td>
					<td>{$txe.total|number_format:2:'.':','}</b></td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<div style="display: table-cell;width: 25%">
		<table width="100%" cellpadding="0" cellspacing="0" style="font-size:10px">
			<thead>
			<th colspan="2">Total cobranza por departamento</th>
			</thead>
			<tbody>
			{foreach from=$data.totalesCobranzaXdepartamento item=txdc key=kxdc}
				<tr>
					<td><b>{$txdc.name}</b></td>
					<td>{$txdc.total|number_format:2:'.':','}</b></td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>

</div>


