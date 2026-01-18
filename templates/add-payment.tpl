<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Agregar Pagos | <a href="{$WEB_ROOT}/cxc">Regresar</a></h1>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">
    <div class="clear"></div>
    <div class="portlet">
        <div class="portlet-header">Informacion del Comprobante
          {include file="{$DOC_ROOT}/templates/boxes/status_no_ajax.tpl"}
          <br/><b>Empresa</b> {$post.razon_social}
          <br/><b>Serie y Folio:</b> {$serie}{$folio}
          <br/><b>Saldo CxC:</b> $<span id="mySaldoSpan">{$post.saldo|number_format:2}</span>
          <br/><b>Metodo pago factura:</b><span id="mySaldoSpan">{$post.metodoDePago}</span>
          <br/><b>Tipo de moneda: </b><span>{$monedaComprobante.moneda}({$monedaComprobante.tipo})</span>
          {if $post.tipoDeComprobante == 'ingreso' && $monedaComprobante.tipo != 'MXN'}
          <br/><b>Tipo de Cambio: </b><span>{$post.tipoDeCambio}</span>
          {/if}

        </div>
        <div class="portlet-content borderGray" id="contenido">
          <div class="wrapper" id="myPaymentsDiv">
            {include file="{$DOC_ROOT}/templates/forms/add-payment.tpl"}
          </div>
        </div>
    </div>
 </div>
<div class="clear"> </div>

</div>

