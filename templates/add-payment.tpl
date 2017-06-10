<div class="grid_16" id="content">
    
  <div class="grid_9">
  <h1 class="catalogos">Agregar Pagos | <a href="{$WEB_ROOT}/cxc">Regresar</a></h1>
  </div>
  
  <div class="clear">
  </div>
  
  <div id="portlets">

  <div class="clear"></div>
  
  <div class="portlet">
          {include file="{$DOC_ROOT}/templates/boxes/status_no_ajax.tpl"}

										<br /><b>Serie y Folio:</b> {$serie}{$folio}
                    <br /><b>Saldo CxC:</b> $<span id="mySaldoSpan">{$post.saldo|number_format:2}</span>
                    <br /><b>Saldo a Favor:</b> $<span id="mySaldoFavorSpan">{$usr.cxcSaldoFavor|number_format:2}</span     <br><br /><br />
      <div class="portlet-content nopadding borderGray" id="contenido">
        <div class="wrapper" id="myPaymentsDiv">
          {include file="{$DOC_ROOT}/templates/forms/add-payment.tpl"}
        </div>
     </div>
      
    </div>

 </div>
  <div class="clear"> </div>

</div>

