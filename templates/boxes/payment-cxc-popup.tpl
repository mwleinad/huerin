		<div class="popupheader" style="z-index:70">
		<div id="fviewmenu" style="z-index:70">
	    <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
        <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close" /></a></span>
      </div>
      </div>

      <div id="ftitl">
    	<div class="flabel">&nbsp;</div>
			<div id="vtitl">
            	<span title="Titulo">Agregar Pago a Cuenta por Cobrar
                    <br />Serie y Folio: {$serie}{$folio}
                    <br />Saldo CxC: $<span id="mySaldoSpan">{$post.saldo|number_format:2}</span>
                    <br />Saldo a Favor: $<span id="mySaldoFavorSpan">{$usr.cxcSaldoFavor|number_format:2}</span>
                </span>
           </div>
    </div>
	<div id="draganddrop" style="position:absolute;top:45px;left:640px">
    		<img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve" />
   	</div>
		</div>
		
    <div class="wrapper" id="myPaymentsDiv">
			{include file="{$DOC_ROOT}/templates/forms/add-payment.tpl"}
		</div>