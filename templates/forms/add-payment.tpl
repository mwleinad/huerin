<div id="divForm">
	<form id="frmAddPayment" name="frmAddPayment" method="post" enctype="multipart/form-data" onsubmit="return false">
		<input type="hidden" id="comprobanteId" name="comprobanteId" value="{$id_comprobante}" class="largeInput"/>
		<input type="hidden" id="efectivo" name="efectivo" value="{if isset($smarty.get.id)}0{else}1{/if}" class="largeInput"/>
		<input type="hidden" id="saldoComprobante" value="{$post.saldo}"/>
		<input type="hidden" id="monedaComprobante" value="{$monedaComprobante.tipo}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Forma de Pago:</div>
			<select name="metodoDePago" id="metodoDePago" class="largeInput">
					{foreach from=$formasDePago item=formaDePago}
						{if $formaDePago.c_FormaPago != '99'}
							<option value="{$formaDePago.c_FormaPago}"
									{if $formaDePago.c_FormaPago == "01"} selected{/if}
							>{$formaDePago.c_FormaPago}-{$formaDePago.descripcion}</option>
						{/if}
					{/foreach}
		  </select>
        </div>
        <div id="metodoDePagoError" style="color:red;"></div>		

			<div class="formLine" style="width:100%; ">
				<div style="width:30%;float:left">* Fecha:</div>
				<div style="width:100%;float:left">
					<input  style="width:15% !important; float:left;" name="paymentDate" id="paymentDate" type="text" value="{$fecha|date_format:'d-m-Y'}" maxlength="10" onclick="CalendarioSimple(this)" class="largeInput"/>
				</div>
				<div style="clear:left"></div>
				<div id="paymentDateError" style="color:red;"></div>
			</div>
            
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Tipo de Moneda:</div>
				<select name="tipoDeMoneda" id="tipoDeMoneda" class="largeInput" data-invoice-currency="{$monedaComprobante.tipo}">
					{foreach from=$tiposDeMoneda item=moneda}
						<option value="{$moneda.tipo}" {if $moneda.tipo == $monedaComprobante.tipo}selected{/if}>{$moneda.moneda} ({$moneda.tipo})</option>
					{/foreach}
				</select>
				<div style="clear:left"></div>
				 <div id="tipoDeMonedaError" style="color:red;"></div>
			</div>
           
			<div class="formLine" style="width:100%; text-align:left" id="tipoCambioDiv">
				<div style="width:30%;float:left">* Tipo de Cambio:</div>
				<input class="largeInput medium" name="tipoCambio" id="tipoCambio" type="text" value="" size="50" maxlength="12" oninput="validateNumeric(this)" placeholder="Ej: 18.123456"/>
				<small style="color:#666;">Máximo 6 decimales</small>
				<div style="clear:left"></div>
				<div id="tipoCambioError" style="color:red;"></div>
			</div>
            
            <div style="clear:both"></div>
        
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left" id="amountLabel">* Importe:</div>
                <input class="largeInput medium" name="amount" id="amount" type="text" value="" size="50" maxlength="15" oninput="validateNumeric(this)"/>
				<div style="clear:left"></div>
				<div id="amountError" style="color:red;"></div>
			</div>
          
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left" id="depositoLabel">* Deposito</div>
				<input class="largeInput medium" name="deposito" id="deposito" type="text" value="" size="50" maxlength="15" oninput="validateNumeric(this)"/>
				<div style="clear:left"></div>	
				<div id="depositoError" style="color:red;"></div>
			</div>
			
			<div class="formLine" style="width:100%; text-align:left" id="confirmAmountDiv" style="display:none;">
				<div style="width:30%;float:left">* Importe en {$monedaComprobante.tipo} (Confirmar en la moneda de la factura, es importante para evitar diferencias en decimales):</div>
				<input class="largeInput medium" name="confirmAmount" id="confirmAmount" type="text" value="" size="50" maxlength="15" oninput="validateNumeric(this)"/>
				<div style="clear:left"></div>
				<div id="confirmAmountError" style="color:red;"></div>
			</div>
			

			{if ($post.version == '3.3' ||  $post.version == '4.0') && $post.metodoDePago === 'PPD'}
				<div class="formLine" style="width:100%; text-align:left">
					<div style="width:25%;float:left; color:#f00">Generar comprobante con complemento de pago por este importe?</div>
					<!-- CAMPO generarComprobantePago   -->
					<input type="checkbox" name="generarComprobantePago" checked id="generarComprobantePago">
				</div>
			{else}
				<div class="formLine">
					<input type="hidden" name="generarComprobantePago" id="generarComprobantePago">
					<p style="color: rgb(30, 46, 197); padding:3px">Las facturas generadas con Metodo de Pago diferentes a PPD no pueden generar complemento de pago, proceda unicamente a registrar el pago efectuado</p>
				</div>
			{/if}

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">  Comprobante de pago:</div>
                <input class="largeInput medium" name="comprobante" id="comprobante" type="file" value="" size="50"/>
				<hr />
        </div>		
            
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">
                <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                <input type="submit" class="button_grey" id="addPayment" value="Agregar Pago" />
            </div>			
			<input type="hidden" id="type" name="type" value="saveAddPayment"/>
		</fieldset>
	</form>
	<script>
	function validateNumeric(input) {
		// Allow only numbers and one decimal point
		input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
	}
	jQ(document).ready(function(){
		function toggleTipoCambio() {
			var selected = jQ('#tipoDeMoneda').val();
			var invoiceCurrency = jQ('#tipoDeMoneda').data('invoice-currency');
			// Solo ocultar si AMBOS son MXN
			if (selected == 'MXN' && invoiceCurrency == 'MXN') {
				jQ('#tipoCambioDiv').hide();
				jQ('#tipoCambio').val('1');
				jQ('#tipoCambio').prop('disabled', true);
			} else {
				jQ('#tipoCambioDiv').show();
				jQ('#tipoCambio').prop('disabled', false);
				// Limpiar el valor si no son ambos MXN
				if(jQ('#tipoCambio').val() == '1') {
					jQ('#tipoCambio').val('');
				}
			}
			// Mostrar/ocultar confirmAmount si monedas diferentes
			if (selected != invoiceCurrency) {
				jQ('#confirmAmountDiv').show();
			} else {
				jQ('#confirmAmountDiv').hide();
				jQ('#confirmAmount').val('');
			}
			// Actualizar labels con sufijo de moneda
			jQ('#amountLabel').text('* Importe (' + selected + '):');
			jQ('#depositoLabel').html('* Deposito (' + selected + ')');
		}
		jQ('#tipoDeMoneda').on('change', toggleTipoCambio);
		toggleTipoCambio(); // initial
	});
	</script>
</div>
