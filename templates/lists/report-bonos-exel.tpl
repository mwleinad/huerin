<!--
EL ARCHIVO huerin_test\templates\lists\report-bonos-exel.tpl Y EL ARCHIVO huerin_test\templates\lists\report-bonos.tpl QUE CONTRUYE LA TABLA DEL REPORTE DEBE SER EL MISMO, LA DIFERENCIA ES QUE EN LA DEL ECXEL NO SE LE DA FORMATO DE MONEDA PORQUE AL REALIZAR OPECACIONES EN EL ECXEL LOS TOMA COMO CADENA Y NO SE PUEDE REALIZAR
-->

{assign var="AMARILLO" value="background-color: yellow;"}
{assign var="BORDER_LEF" value="border-left: 2px solid #000;"}
{assign var="BORDER_TOP" value="border-top: 2px solid #000;"}
{assign var="BORDER_BOTTOM" value="border-bottom: 2px solid #000;"}
{assign var="COLOR_SUBT" value="background-color: rgb(0, 192, 0);color: white;"}

{assign var="COLOR_RED_BLANCO" value="background-color: red;color: white;"}
{assign var="COLOR_BLUE_BLANCO" value="background-color: blue;color: white;"}

<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	{foreach from=$DATOS.SUPERVISOR item=item1 key=key1}
		<tr>
			<td align="center" style="{$AMARILLO}">Cliente</td>
			<td align="center" style="{$AMARILLO}">S. Asignado</td>
			<td align="center" style="{$AMARILLO}">Razon Social</td>
			<td align="center" style="{$AMARILLO}">Servicio</td>
			<td align="center" style="{$AMARILLO}">{$item1.MES1_NAME}</td>
			<td align="center" style="{$AMARILLO}">{$item1.MES2_NAME}</td>
			<td align="center" style="{$AMARILLO}">{$item1.MES3_NAME}</td>
			<td align="center" style="{$AMARILLO}{$BORDER_LEF}">TOTAL</td>
		</tr>
		{foreach from=$item1.CLIENTE item=item2 key=key2}
			{if $item2.OSWA|@count > 0}
				<tr>
					<td align="center" style="{$BORDER_TOP}">{$item2.nameContact}</td>
					<td align="center" style="{$BORDER_TOP}">{$item2.name}</td>
					<td align="center" style="{$BORDER_TOP}">{$item2.nombreComercial}</td>
					<td align="center" style="{$BORDER_TOP}">{$item2.nameServicios}</td>
					<td align="center" style="{$BORDER_TOP}">${$item2.MES1|number_format:2}</td>
					<td align="center" style="{$BORDER_TOP}">${$item2.MES2|number_format:2}</td>
					<td align="center" style="{$BORDER_TOP}">${$item2.MES3|number_format:2}</td>
					<td align="center" style="{$BORDER_TOP}{$BORDER_LEF}"><big><b>${$item2.TOTAL_MES|number_format:2}</b></big></td>
				</tr>
			{/if}
			{foreach from=$item2.OSWA item=item10 key=key10}
				<tr>
					<td align="center" ></td>
					<td align="center" ></td>
					<td align="center" ></td>
					<td align="center" >{$item10.nombreServicio}</td>
					<td align="center" class="{$item10.COLOR_MES1}" >${$item10.MES1|number_format:2}</td>
					<td align="center" class="{$item10.COLOR_MES2}" >${$item10.MES2|number_format:2}</td>
					<td align="center" class="{$item10.COLOR_MES3}" >${$item10.MES3|number_format:2}</td>
					<td align="center" class="{$item10.COLOR_MES3}{$BORDER_LEF}" >0</td>
				</tr>
			{foreachelse}
				{*}
				<!-- <tr>
					<td align="center" colspan="7" >Sin Servicio Asignados Cliente(a) {$item2.nameContact}</td>
					<td align="center" style="{$BORDER_LEF}" ></td>
				</tr> -->
				{*}
			{/foreach}
		{foreachelse}
			{*}
			<!-- <tr>
				<td align="center" colspan="7" >Sin Clietes Asignados Supervisor(a) {$item1.name}</td>
				<td align="center" style="{$BORDER_LEF}" ></td>
			</tr> -->
			{*}
		{/foreach}
			<tr>
				<td align="center" style="{$COLOR_SUBT}" ></td>
				<td align="center" style="{$COLOR_SUBT}" ></td>
				<td align="center" style="{$COLOR_SUBT}" ></td>
				<td align="center" style="{$COLOR_SUBT}" >SUBTOTAL:</td>
				<td align="center" style="{$COLOR_SUBT}" ><B>${$item1.TOTAL_CLIENTE.MES1|number_format:2}</B></td>
				<td align="center" style="{$COLOR_SUBT}" ><B>${$item1.TOTAL_CLIENTE.MES2|number_format:2}</B></td>
				<td align="center" style="{$COLOR_SUBT}" ><B>${$item1.TOTAL_CLIENTE.MES3|number_format:2}</B></td>
				<td align="center" style="{$COLOR_SUBT}{$BORDER_LEF}" ><B>${$item1.TOTAL_CLIENTE.TOTAL_MES|number_format:2}</B></td>
			</tr>
		{foreach from=$item1.CONTADOR item=item2 key=key2}

				<tr>
					<td align="center" style="{$AMARILLO}">Cliente</td>
					<td align="center" style="{$AMARILLO}">C. Asignado</td>
					<td align="center" style="{$AMARILLO}">Razon Social</td>
					<td align="center" style="{$AMARILLO}">Servicio</td>
					<td align="center" style="{$AMARILLO}">{$item2.MES1_NAME}</td>
					<td align="center" style="{$AMARILLO}">{$item2.MES2_NAME}</td>
					<td align="center" style="{$AMARILLO}">{$item2.MES3_NAME}</td>
					<td align="center" style="{$AMARILLO}{$BORDER_LEF}">TOTAL</td>
				</tr>

			{foreach from=$item2.CLIENTE item=item3 key=key3}
				{if $item3.OSWA|@count > 0}
					<tr>
						<td align="center" style="{$BORDER_TOP}">{$item3.nameContact}</td>
						<td align="center" style="{$BORDER_TOP}">{$item3.name}</td>
						<td align="center" style="{$BORDER_TOP}">{$item3.nombreComercial}</td>
						<td align="center" style="{$BORDER_TOP}">{$item3.nameServicios}</td>
						<td align="center" style="{$BORDER_TOP}">${$item3.MES1|number_format:2}</td>
						<td align="center" style="{$BORDER_TOP}">${$item3.MES2|number_format:2}</td>
						<td align="center" style="{$BORDER_TOP}">${$item3.MES3|number_format:2}</td>
						<td align="center" style="{$BORDER_TOP}{$BORDER_LEF}" ><big><b>${$item3.TOTAL_MES|number_format:2}</b></big></td>
					</tr>
				{/if}
				{foreach from=$item3.OSWA item=item10 key=key10}
					<tr>
						<td align="center" ></td>
						<td align="center" ></td>
						<td align="center" ></td>
						<td align="center" >{$item10.nombreServicio}</td>
						<td align="center" class="{$item10.COLOR_MES1}" >${$item10.MES1|number_format:2}</td>
						<td align="center" class="{$item10.COLOR_MES2}" >${$item10.MES2|number_format:2}</td>
						<td align="center" class="{$item10.COLOR_MES3}" >${$item10.MES3|number_format:2}</td>
						<td align="center" class="{$item10.COLOR_MES3}{$BORDER_LEF}" >0</td>
					</tr>
				{foreachelse}
					{*}
					<!-- <tr>
						<td align="center" colspan="7" >Sin Servicio Asignados Cliente(a) {$item3.nameContact}</td>
						<td align="center" style="{$BORDER_LEF}" ></td>
					</tr> -->
					{*}
				{/foreach}
			{foreachelse}
				{*}
				<!-- <tr>
					<td align="center" colspan="7" >Sin Clietes Asignados Contador(a) {$item2.name}</td>
					<td align="center" style="{$BORDER_LEF}" ></td>
				</tr> -->
				{*}
			{/foreach}
				<tr>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" >SUBTOTAL:</td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item2.TOTAL_CLIENTE.MES1|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item2.TOTAL_CLIENTE.MES2|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item2.TOTAL_CLIENTE.MES3|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}{$BORDER_LEF}" ><B>${$item2.TOTAL_CLIENTE.TOTAL_MES|number_format:2}</B></td>
				</tr>
			{foreach from=$item2.AUXILIAR item=item3 key=key3}

					<tr>
						<td align="center" style="{$AMARILLO}">Cliente</td>
						<td align="center" style="{$AMARILLO}">A. Asignado</td>
						<td align="center" style="{$AMARILLO}">Razon Social</td>
						<td align="center" style="{$AMARILLO}">Servicio</td>
						<td align="center" style="{$AMARILLO}">{$item3.MES1_NAME}</td>
						<td align="center" style="{$AMARILLO}">{$item3.MES2_NAME}</td>
						<td align="center" style="{$AMARILLO}">{$item3.MES3_NAME}</td>
						<td align="center" style="{$AMARILLO}{$BORDER_LEF}">TOTAL</td>
					</tr>

				{foreach from=$item3.CLIENTE item=item4 key=key4}
					{if $item4.OSWA|@count > 0}
						<tr>
							<td align="center" style="{$BORDER_TOP}">{$item4.nameContact}</td>
							<td align="center" style="{$BORDER_TOP}">{$item4.name}</td>
							<td align="center" style="{$BORDER_TOP}">{$item4.nombreComercial}</td>
							<td align="center" style="{$BORDER_TOP}">{$item4.nameServicios}</td>
							<td align="center" style="{$BORDER_TOP}">${$item4.MES1|number_format:2}</td>
							<td align="center" style="{$BORDER_TOP}">${$item4.MES2|number_format:2}</td>
							<td align="center" style="{$BORDER_TOP}">${$item4.MES3|number_format:2}</td>
							<td align="center" style="{$BORDER_TOP}{$BORDER_LEF}" ><big><b>${$item4.TOTAL_MES|number_format:2}</b></big></td>
						</tr>
					{/if}
					{foreach from=$item4.OSWA item=item10 key=key10}
						<tr>
							<td align="center" ></td>
							<td align="center" ></td>
							<td align="center" ></td>
							<td align="center" >{$item10.nombreServicio}</td>
							<td align="center" class="{$item10.COLOR_MES1}" >${$item10.MES1|number_format:2}</td>
							<td align="center" class="{$item10.COLOR_MES2}" >${$item10.MES2|number_format:2}</td>
							<td align="center" class="{$item10.COLOR_MES3}" >${$item10.MES3|number_format:2}</td>
							<td align="center" class="{$item10.COLOR_MES3}{$BORDER_LEF}" >0</td>
						</tr>
					{foreachelse}
						{*}
						<!-- <tr>
							<td align="center" colspan="7" >Sin Servicio Asignados Cliente(a) {$item4.nameContact}</td>
							<td align="center" style="{$BORDER_LEF}" ></td>
						</tr> -->
						{*}
					{/foreach}
				{foreachelse}
					{*}
					<!-- <tr>
						<td align="center" colspan="7" >Sin Clietes Asignados Auxiliar {$item3.name}</td>
						<td align="center" style="{$BORDER_LEF}" ></td>
					</tr> -->
					{*}
				{/foreach}
				<tr>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" ></td>
					<td align="center" style="{$COLOR_SUBT}" >SUBTOTAL:</td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item3.TOTAL_CLIENTE.MES1|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item3.TOTAL_CLIENTE.MES2|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}" ><B>${$item3.TOTAL_CLIENTE.MES3|number_format:2}</B></td>
					<td align="center" style="{$COLOR_SUBT}{$BORDER_LEF}" ><B>${$item3.TOTAL_CLIENTE.TOTAL_MES|number_format:2}</B></td>
				</tr>
			{/foreach}
		{/foreach}
	{/foreach}
	{if $DATOS.SUPERVISOR|count > 0}
		<tr>
			<td align="center" style="{$COLOR_RED_BLANCO}" ></td>
			<td align="center" style="{$COLOR_RED_BLANCO}" ></td>
			<td align="center" style="{$COLOR_RED_BLANCO}" ></td>
			<td align="center" style="{$COLOR_RED_BLANCO}" >TOTAL:</td>
			<td align="center" style="{$COLOR_RED_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES1|number_format:2}</B></td>
			<td align="center" style="{$COLOR_RED_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES2|number_format:2}</B></td>
			<td align="center" style="{$COLOR_RED_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES3|number_format:2}</B></td>
			<td align="center" style="{$COLOR_RED_BLANCO}{$BORDER_LEF}" ><B>${$DATOS.TOTAL_GENERAL.TOTAL_MES|number_format:2}</B></td>
		</tr>
	{/if}


	<tr>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" ></td>
		<td align="center" style="{$AMARILLO}{$BORDER_BOTTOM}" >R  E  S  U  M  E  N</td>
		<td align="center" style="{$AMARILLO}{$BORDER_LEF}{$BORDER_BOTTOM}"> TOTAL TRIM.</td>
	</tr>
	{foreach from=$DATOS.SUPERVISOR item=item1 key=key1}
		<tr>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" ></td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" ></td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" >{$item1.name}</td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" >SUPERVISOR(A)</td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES1|number_format:2}</B></td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES2|number_format:2}</B></td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}" ><B>${$DATOS.TOTAL_GENERAL.MES3|number_format:2}</B></td>
			<td align="center" style="{$COLOR_BLUE_BLANCO}{$BORDER_LEF}" ><B>${$DATOS.TOTAL_GENERAL.TOTAL_MES|number_format:2}</B></td>
		</tr>
		{foreach from=$item1.CONTADOR item=item2 key=key2}
			{assign var="COLOR_CONTA_HEREDA_AUX" value="background-color: {$item2.TOT_CONTADOR_GEN.COLOR};color: white;"}
			<tr>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ></td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ></td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" >{$item2.name}</td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" >CONTADOR(A)</td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item2.TOT_CONTADOR_GEN.MES1|number_format:2}</B></td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item2.TOT_CONTADOR_GEN.MES2|number_format:2}</B></td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item2.TOT_CONTADOR_GEN.MES3|number_format:2}</B></td>
				<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}{$BORDER_LEF}" ><B>${$item2.TOT_CONTADOR_GEN.TOTAL_MES|number_format:2}</B></td>
			</tr>
			{foreach from=$item2.AUXILIAR item=item3 key=key3}
				<tr>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ></td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ></td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" >{$item3.name}</td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" >AUXILIAR</td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item3.TOTAL_CLIENTE.MES1|number_format:2}</B></td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item3.TOTAL_CLIENTE.MES2|number_format:2}</B></td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}" ><B>${$item3.TOTAL_CLIENTE.MES3|number_format:2}</B></td>
					<td align="center" style="{$COLOR_CONTA_HEREDA_AUX}{$BORDER_LEF}" ><B>${$item3.TOTAL_CLIENTE.TOTAL_MES|number_format:2}</B></td>
				</tr>
			{/foreach}
		{/foreach}
	{/foreach}
</table>
