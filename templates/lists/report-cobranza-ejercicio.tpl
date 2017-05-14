<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	<tr>
		<td align="center" style="background-color: yellow;">Cliente</td>
		<td align="center" style="background-color: yellow;">Responsable</td>
		<td align="center" style="background-color: yellow;">Honorarios</td>

		<td align="center" style="background-color: yellow;">E</td>
		<td align="center" style="background-color: yellow;">F</td>
		<td align="center" style="background-color: yellow;">M</td>

		<td align="center" style="background-color: yellow;">A</td>
		<td align="center" style="background-color: yellow;">M</td>
		<td align="center" style="background-color: yellow;">J</td>

		<td align="center" style="background-color: yellow;">J</td>
		<td align="center" style="background-color: yellow;">A</td>
		<td align="center" style="background-color: yellow;">S</td>

		<td align="center" style="background-color: yellow;">O</td>
		<td align="center" style="background-color: yellow;">N</td>
		<td align="center" style="background-color: yellow;">D</td>

		<td align="center" style="background-color: yellow;border-left: 2px solid #000;">13</td>
	</tr>
	{foreach from=$DATOS.items item=item1 key=key1}
		<tr>
			<td align="center" >{$item1.nombreComercial}</td>
			<td align="center" >{$item1.namePersonal}</td>
			<td align="center" >{$item1.COSTO_T}</td>
			{if $item1.ENERO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.ENERO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.ENERO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.ENERO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.FEBRERO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.FEBRERO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.FEBRERO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.FEBRERO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.MARZO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.MARZO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.MARZO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.MARZO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.ABRIL_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.ABRIL_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.ABRIL_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.ABRIL_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.MAYO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.MAYO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.MAYO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.MAYO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.JUNIO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.JUNIO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.JUNIO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.JUNIO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.JULIO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.JULIO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.JULIO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.JULIO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.AGOSTO_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.AGOSTO_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.AGOSTO_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.AGOSTO_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.SEPTIEMBRE_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.SEPTIEMBRE_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.SEPTIEMBRE_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.SEPTIEMBRE_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.OCTUBRE_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.OCTUBRE_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.OCTUBRE_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.OCTUBRE_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.NOVIEMBRE_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.NOVIEMBRE_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.NOVIEMBRE_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.NOVIEMBRE_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}
			{if $item1.DICIEMBRE_N eq "BLANCO"}
				<td align="center" title="Hay facturas no pagadas"></td>
			{elseif $item1.DICIEMBRE_N eq "NEGRO"}
				<td align="center" style="background-color: black" title="No se encuentran servicios del cliente en este mes"></td>
			{elseif $item1.DICIEMBRE_N eq "ASTERISCO"}
				<td align="center" title="Las facturas ya estan pagadas" >*</td>
			{elseif $item1.DICIEMBRE_N eq "SIN-COMPROBANTE"}
				<td align="center" title="Con servicios no facturados" >?</td>
			{/if}

			<td align="center" > * </td>
		</tr>
	{/foreach}
</table>

