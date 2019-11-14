<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	<thead>
	<tr>
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%">Cliente</th>
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">C. Asignado</th>
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">Razon Social</th>
		<th class="cabeceraTabla" align="center" style="text-align: left">Servicio</th>
		{foreach from=$nombreMeses item=mes}
			<th class="cabeceraTabla" align="center"  style="text-align: left">{$mes}</th>
		{/foreach}
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 4%"">Total trabajado</th>
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 4%"">Total devengado</th>
		<th class="cabeceraTabla" align="center" style="text-align: left;width: 4%"">Diferencia</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$data.serviciosEncontrados item=item key=key}
		{foreach from=$item.propios item=item2 key=key2 name=socios}<!--star socios-->
		<tr>
			<td>{$item2.cliente}</td>
			<td>{$item2.encargado}</td>
			<td>{$item2.contrato}</td>
			<td>{$item2.nombreServicio}</td>
			{foreach from=$item2.instancias item=instancia2}
				<td style="{if $instancia2.class eq 'CompletoTardio' || $instancia2.class eq 'Completo'}
								background-color:#009900 !important;color:#FFF;
							{else}
								{if $instancia2.class eq 'Iniciado' || $instancia2.class eq 'PorCompletar'}
									background-color:#FC0 !important;color:#FFF;
								{else}
									{if $instancia2.class == "PorIniciar"}
										background-color:#F00 !important;color:#FFF
									{/if}
								{/if}
							 {/if}">
					<div style="cursor:pointer" >
						${$instancia2.costo|number_format:2:".":","}
						{if $instancia2.status eq 'baja'}
							<span style="color:#DA9696">(Inactivo)</span>
						{/if}
					</div>
				</td>
			{/foreach}
			<td>{$item2.totalTrabajado|number_format:2:'.':','}</td>
			<td>{$item2.totalDevengado|number_format:2:'.':','}</td>
			<td>{$item2.totalDevengado-$item2.totalTrabajado|number_format:2:'.':','}</td>
		</tr>
		{if $smarty.foreach.socios.last}
			<tr>
				<td style="background: #0e76a8;color: #FFFFFF"></td>
				<td style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item.contratos|count}</td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
				{foreach from=$item.totalVerticalCompletado item=totalItemComp}
					<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp|number_format:2:'.':','}</td>
				{/foreach}
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item2.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			</tr>
			<tr>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000" ></td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
				{foreach from=$item.totalVerticalDevengado item=totalItemDev}
					<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev|number_format:2:'.':','}</td>
				{/foreach}
				<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item2.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item2.encargadoId]['totalDevengado']-$data.totalesEncargados[$item2.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
			</tr>
		{/if}
		{/foreach}<!--End socios-->
		{foreach from=$item.subordinados item=item22 key=key22}<!--Start gerentes -->
			{foreach from=$item22.propios item=item3 key=key3 name=gerentes}
				<tr>
					<td>{$item3.cliente}</td>
					<td>{$item3.encargado}</td>
					<td>{$item3.contrato}</td>
					<td>{$item3.nombreServicio}</td>
					{foreach from=$item3.instancias item=instancia3}
						<td style="{if $instancia3.class eq 'CompletoTardio' || $instancia3.class eq 'Completo'}
								background-color:#009900 !important;color:#FFF;
							{else}
								{if $instancia3.class eq 'Iniciado' || $instancia3.class eq 'PorCompletar'}
									background-color:#FC0 !important;color:#FFF;
								{else}
									{if $instancia3.class == "PorIniciar"}
										background-color:#F00 !important;color:#FFF
									{/if}
								{/if}
							 {/if}">
							<div style="cursor:pointer" >
								${$instancia3.costo|number_format:2:".":","}
								{if $instancia3.status eq 'baja'}
									<span style="color:#DA9696">(Inactivo)</span>
								{/if}
							</div>
						</td>
					{/foreach}
					<td>{$item3.totalTrabajado|number_format:2:'.':','}</td>
					<td>{$item3.totalDevengado|number_format:2:'.':','}</td>
					<td>{$item3.totalDevengado-$item3.totalTrabajado|number_format:2:'.':','}</td>
				</tr>
				{if $smarty.foreach.gerentes.last}
					<tr>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item22.contratos|count}</td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
						{foreach from=$item22.totalVerticalCompletado item=totalItemComp2}
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp2|number_format:2:'.':','}</td>
						{/foreach}
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item3.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
					</tr>
					<tr>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
						{foreach from=$item22.totalVerticalDevengado item=totalItemDev2}
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev2|number_format:2:'.':','}</td>
						{/foreach}
						<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item3.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
						<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item3.encargadoId]['totalDevengado']-$data.totalesEncargados[$item3.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
					</tr>
				{/if}
			{/foreach}<!-- end gerentes -->
			{foreach from=$item22.subordinados item=item33 key=key33}<!--start subgerentes-->
				{foreach from=$item33.propios item=item4 key=key4 name=subgerentes}
					<tr>
						<td>{$item4.cliente}</td>
						<td>{$item4.encargado}</td>
						<td>{$item4.contrato}</td>
						<td>{$item4.nombreServicio}</td>
						{foreach from=$item4.instancias item=instancia4}
							<td style="{if $instancia4.class eq 'CompletoTardio' || $instancia4.class eq 'Completo'}
											background-color:#009900 !important;color:#FFF;
									    {else}
											{if $instancia4.class eq 'Iniciado' || $instancia4.class eq 'PorCompletar'}
												background-color:#FC0 !important;color:#FFF;
											{else}
												{if $instancia4.class == "PorIniciar"}
													background-color:#F00 !important;color:#FFF
												{/if}
											{/if}
										{/if}">
								<div style="cursor:pointer" >
									${$instancia4.costo|number_format:2:".":","}
									{if $instancia4.status eq 'baja'}
										<span style="color:#DA9696">(Inactivo)</span>
									{/if}
								</div>
							</td>
						{/foreach}
						<td>{$item4.totalTrabajado|number_format:2:'.':','}</td>
						<td>{$item4.totalDevengado|number_format:2:'.':','}</td>
						<td>{$item4.totalDevengado-$item4.totalTrabajado|number_format:2:'.':','}</td>
					</tr>
					{if $smarty.foreach.subgerentes.last}
						<tr>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item33.contratos|count}</td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
							{foreach from=$item33.totalVerticalCompletado item=totalItemComp3}
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp3|number_format:2:'.':','}</td>
							{/foreach}
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item4.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
						</tr>
						<tr>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000" ></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
							{foreach from=$item33.totalVerticalDevengado item=totalItemDev3}
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev3|number_format:2:'.':','}</td>
							{/foreach}
							<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item4.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
							<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item4.encargadoId]['totalDevengado']-$data.totalesEncargados[$item4.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>

						</tr>
					{/if}
				{/foreach}<!-- end subgerentes -->
				{foreach from=$item33.subordinados item=item44 key=key44}<!-- start supervisores -->
					{foreach from=$item44.propios item=item5 key=key5 name=supervisores}
						<tr>
							<td>{$item5.cliente}</td>
							<td>{$item5.encargado}</td>
							<td>{$item5.contrato}</td>
							<td>{$item5.nombreServicio}</td>
							{foreach from=$item5.instancias item=instancia5 key=keyins5}
								<td style="{if $instancia5.class eq 'CompletoTardio' || $instancia5.class eq 'Completo'}
												background-color:#009900 !important;color:#FFF;
										   {else}
												{if $instancia5.class eq 'Iniciado' || $instancia5.class eq 'PorCompletar'}
													background-color:#FC0 !important;color:#FFF;
												{else}
													{if $instancia5.class == "PorIniciar"}
														background-color:#F00 !important;color:#FFF
													{/if}
												{/if}
										   {/if}">
									<div style="cursor:pointer" >
										${$instancia5.costo|number_format:2:".":","}
										{if $instancia5.status eq 'baja'}
											<span style="color:#DA9696">(Inactivo)</span>
										{/if}
									</div>
								</td>
							{/foreach}
							<td>{$item5.totalTrabajado|number_format:2:'.':','}</td>
							<td>{$item5.totalDevengado|number_format:2:'.':','}</td>
							<td>{$item5.totalDevengado-$item5.totalTrabajado|number_format:2:'.':','}</td>
						</tr>
						{if $smarty.foreach.supervisores.last}
							<tr>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item44.contratos|count}</td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
								{foreach from=$item44.totalVerticalCompletado item=totalItemComp4}
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp4|number_format:2:'.':','}</td>
								{/foreach}
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item5.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
							</tr>
							<tr>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000" ></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
								{foreach from=$item44.totalVerticalDevengado item=totalItemDev4}
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev4|number_format:2:'.':','}</td>
								{/foreach}
								<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item5.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
								<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item5.encargadoId]['totalDevengado']-$data.totalesEncargados[$item5.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
							</tr>
						{/if}
					{/foreach}<!-- end supervisores -->
					{foreach from=$item44.subordinados item=item55 key=key55}<!-- start contadores -->
						{foreach from=$item55.propios item=item6 key=key6 name=contadores}<!-- start contadores propios -->
							<tr>
								<td>{$item6.cliente}</td>
								<td>{$item6.encargado}</td>
								<td>{$item6.contrato}</td>
								<td>{$item6.nombreServicio}</td>
								{foreach from=$item6.instancias item=instancia6}
									<td style="{if $instancia6.class eq 'CompletoTardio' || $instancia6.class eq 'Completo'}
												background-color:#009900 !important;color:#FFF;
										   {else}
												{if $instancia6.class eq 'Iniciado' || $instancia6.class eq 'PorCompletar'}
													background-color:#FC0 !important;color:#FFF;
												{else}
													{if $instancia6.class == "PorIniciar"}
														background-color:#F00 !important;color:#FFF
													{/if}
												{/if}
										   {/if}">
										<div style="cursor:pointer" >
											${$instancia6.costo|number_format:2:".":","}
											{if $instancia6.status eq 'baja'}
												<span style="color:#DA9696">(Inactivo)</span>
											{/if}
										</div>
									</td>
								{/foreach}
								<td>{$item6.totalTrabajado|number_format:2:'.':','}</td>
								<td>{$item6.totalDevengado|number_format:2:'.':','}</td>
								<td>{$item6.totalDevengado-$item6.totalTrabajado|number_format:2:'.':','}</td>
							</tr>
							{if $smarty.foreach.contadores.last}
								<tr>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item55.contratos|count}</td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
									{foreach from=$item55.totalVerticalCompletado item=totalItemComp5}
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp5|number_format:2:'.':','}</td>
									{/foreach}
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item6.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
								</tr>
								<tr>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
									{foreach from=$item55.totalVerticalDevengado item=totalItemDev5}
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev5|number_format:2:'.':','}</td>
									{/foreach}
									<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item6.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
									<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item6.encargadoId]['totalDevengado']-$data.totalesEncargados[$item6.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
								</tr>
							{/if}
						{/foreach}<!-- end contadores propios -->
						{foreach from=$item55.subordinados item=item66 key=key66}<!-- start auxiliares -->
							{foreach from=$item66.propios item=item7 key=key7 name=auxiliares}<!-- start auxiliares propios -->
								<tr>
									<td>{$item7.cliente}</td>
									<td>{$item7.encargado}</td>
									<td>{$item7.contrato}</td>
									<td>{$item7.nombreServicio}</td>
									{foreach from=$item7.instancias item=instancia7}
										<td style="{if $instancia7.class eq 'CompletoTardio' || $instancia7.class eq 'Completo'}
													background-color:#009900 !important;color:#FFF;
											{else}
													{if $instancia7.class eq 'Iniciado' || $instancia7.class eq 'PorCompletar'}
														background-color:#FC0 !important;color:#FFF;
													{else}
														{if $instancia7.class == "PorIniciar"}
															background-color:#F00 !important;color:#FFF
														{/if}
													{/if}
											{/if}">
											<div style="cursor:pointer" >
												${$instancia7.costo|number_format:2:".":","}
												{if $instancia7.status eq 'baja'}
													<span style="color:#DA9696">(Inactivo)</span>
												{/if}
											</div>
										</td>
									{/foreach}
									<td>{$item7.totalTrabajado|number_format:2:'.':','}</td>
									<td>{$item7.totalDevengado|number_format:2:'.':','}</td>
									<td>{$item7.totalDevengado-$item6.totalTrabajado|number_format:2:'.':','}</td>
								</tr>
								{if $smarty.foreach.auxiliares.last}
									<tr>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total empresas</td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$item66.contratos|count}</td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total trabajado</td>
										{foreach from=$item66.totalVerticalCompletado item=totalItemComp6}
											<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemComp6|number_format:2:'.':','}</td>
										{/foreach}
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item7.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
									</tr>
									<tr>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">Total devengado</td>
										{foreach from=$item66.totalVerticalDevengado item=totalItemDev6}
											<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$totalItemDev6|number_format:2:'.':','}</td>
										{/foreach}
										<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item7.encargadoId]['totalDevengado']|number_format:2:'.':','}</td>
										<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totalesEncargados[$item7.encargadoId]['totalDevengado']-$data.totalesEncargados[$item7.encargadoId]['totalCompletado']|number_format:2:'.':','}</td>
									</tr>
								{/if}
							{/foreach}<!-- end auxiliares propios -->
						{/foreach}<!-- end auxiliares-->
					{/foreach}<!-- end contadores -->
				{/foreach}
			{/foreach}
		{/foreach}
	{foreachelse}
	<tr>
		<td colspan="10" align="center">Ning&uacute;n registro encontrado.</td>
	</tr>
	{/foreach}
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	{if $data.totales}
		<tr>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">Suma total empresas</td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totales.totalEmpresas}</td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">Suma total trabajado</td>
			{foreach from=$data.totales.granTotalVerticalCompletado item=itemVerComp}
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$itemVerComp|number_format:2:'.':','}</td>
			{/foreach}
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
		</tr>
		<tr>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">Suma total devengado</td>
			{foreach from=$data.totales.granTotalVerticalDevengado item=itemVerDevengado}
				<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$itemVerDevengado|number_format:2:'.':','}</td>
			{/foreach}
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
		</tr>
		<tr>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000"></td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">Totales</td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totales.granTotalHorizontalCompletado|number_format:2:'.':','}</td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totales.granTotalHorizontalDevengado|number_format:2:'.':','}</td>
			<td  style="font-weight:bold; background: #0e76a8;color: #000000">{$data.totales.granTotalHorizontalDevengado-$data.totales.granTotalHorizontalCompletado|number_format:2:'.':','}</td>
		</tr>
	{/if}
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	</tbody>
</table>
<div style="display: table;width: 100%; border-spacing: 10px">
    <div style="display: table-cell;width: 35%">
        <table width="50%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
            <thead>
            <tr>
                <th class="cabeceraTabla" align="center" style="text-align: left;width: 15%">Nombre</th>
                <th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">Ingresos</th>
                <th class="cabeceraTabla" align="center" style="text-align: left;width: 15%"">Gastos</th>
                <th class="cabeceraTabla" align="center" style="text-align: left">Utilidad</th>
                <th class="cabeceraTabla" align="center" style="text-align: left">% BONO</th>
                <th class="cabeceraTabla" align="center" style="text-align: left">BONO</th>
                <th class="cabeceraTabla" align="center" style="text-align: left">Bono Entregado</th>
            </tr>
            </thead>
            <tbody>
                {foreach from=$data.totalesEncargadosAcumulado item=enc key=ken}
					{math equation ='I-G' I=$enc.totalCompletado G=$enc.sueldoTotal assign=utilidad}
                    <tr>
                        <td>{$enc.name}</td>
                        <td>{$enc.totalCompletado|number_format:2:'.':','}</td>
                        <td>{$enc.sueldoTotal|number_format:2:'.':','}</td>
                        <td>{$utilidad|number_format:2:'.':','}</td>
                        <td>{$enc.porcentajeBono} %</td>
                        <td>{if $utilidad>0}{($utilidad*({$enc.porcentajeBono}/100))|number_format:2:'.':','}{else}0.00{/if}</td>
                        <td>{if $utilidad>0}{($utilidad*({$enc.porcentajeBono}/100))|round:2|number_format:2:'.':','}{else}0.00{/if}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>


