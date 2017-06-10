{if $totalDesglosado}
<div style="width:750px;">

{if $impuestos}
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Subtotal Original
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.subtotalOriginal}
  </div>
  <div style="clear:both"></div>

{foreach from=$impuestos item=impuesto}
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	{if $impuesto.tipo == "retencion"}
    Retencion 
    {else if $impuesto.tipo == "deduccion"}
    Deduccion 
    {/if}
  	{$impuesto.tasa}% de {$impuesto.impuesto}
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$impuesto.importe}
  </div>
  <div style="clear:both"></div>

{/foreach}
{/if}	

	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Subtotal
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.subtotal}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Descuento
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.descuento}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Despues de Descuento
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.afterDescuento}
  </div>
  <div style="clear:both"></div>

	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	IVA
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.iva}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Despues de IVA
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.afterIva}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	IEPS
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.ieps}
  </div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Despues de Impuestos
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.afterImpuestos}
  </div>
  <div style="clear:both"></div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Ret. IVA
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.retIva}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Ret. ISR
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.retIsr}
  </div>
  <div style="clear:both"></div>
	<div style="float:left; width:350px ;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	Total
  </div>
	<div style="float:left; width:250px;border:solid; border-width:1px; border-color:#000; background-color:#FFC; padding:5px">
  	$ {$totalDesglosado.total}
  </div>
  <div style="clear:both"></div>
</div>
{else}
Necesitas Agregar al menos un concepto
{/if}
