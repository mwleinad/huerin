<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Nuevo Comprobante</h1>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
     
      <div class="portlet-content nopadding borderGray" id="contenido">
      <ul>
{if $info.version != "auto" && $certNuevo == ""}
    	<li>No has subido tu Certificado de Sello Digital. Para subirlo Ir a la Seccion de Folios > Actualizar Certificado. No podras hacer facturas hasta subirlo.</li>
  {/if}
	{if $noFolios == 0}
    	<li style="color:#C00; font-size:14px">Tienes que subir al menos una serie de Folios. Para hacerlo ve a Folios > Nuevos Folios. No podras hacer facturas hasta hacerlo.</li>
  {/if}
	{if $info.version == "auto" && $qrs == 0}
    	<li style="color:#C00; font-size:14px">No has subido tu Codigo de Barras Bidimensional. Para subirlo ve a la seccion de Folios > Cambiar QR</li>
  {/if}
	{if $countClientes == 0}
    	<li style="color:#C00; font-size:14px">Tienes que agregar al menos un cliente. Para crearlo ve a la seccion de Clientes > Nuevo Cliente</li>
  {/if}  
  
    </ul>
{include file="forms/nueva-factura.tpl"}

     </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>

