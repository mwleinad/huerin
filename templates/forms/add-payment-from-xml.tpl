<div id="divForm">
	<form id="frmAddPayment" name="frmAddPayment" method="post" enctype="multipart/form-data" onsubmit="return false">
		<input type="hidden" id="file_xml" name="file_xml" value="{$factura.nameXml}" class="largeInput"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Metodo de Pago:</div>
        	<select name="metodoDePago" id="metodoDePago" class="largeInput">
          	<option value="Efectivo">Efectivo</option>
          	<option value="Bonificacion">Bonificacion</option>
          	<option value="Deposito">Deposito</option>
          	<option value="Transferencia">Transferencia</option>
          	<option value="Cheque">Cheque</option>
          	<option value="Saldo a Favor">Saldo a Favor</option>
          </select>
        </div>		

		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Fecha:</div>
			<div style="width:100%;float:left">
				<input  style="width:15% !important; float:left;" name="paymentDate" id="paymentDate" type="text" value="{$fecha|date_format:'d-m-Y'}" maxlength="10" onclick="CalendarioSimple(this)" class="largeInput"/>
			</div>
		</div>
		<div style="clear:both"></div>

		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Importe:</div>
			<input class="largeInput medium" name="amount" id="amount" type="text" value="" size="50" maxlength="15"/>
		</div>
		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Deposito(<small>Dejar vacio con metodo de pago saldo a favor</small>)</div>
			<input class="largeInput medium" name="deposito" id="deposito" type="text" value="" size="50" maxlength="15"/>
		</div>

		{if $factura.version == '3.3'}
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:25%;float:left; color:#f00">Generar comprobante con complemento de pago por este importe?</div>
				<!-- CAMPO generarComprobantePago   -->
				<input type="checkbox" name="generarComprobantePago" checked id="generarComprobantePago">
			</div>
		{/if}

		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">  Comprobante de pago:</div>
			<input class="largeInput medium" name="comprobante" id="comprobante" type="file" value="" size="50"/>
		</div>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
			</div>
			<div class="actionsChild">
				<a  href="javascript:;" id="addPayment" name="addPayment" class="button_grey">
					<span>Guardar</span>
				</a>
			</div>
		</div>
		<input type="hidden" id="type" name="type" value="saveAddPaymentFromXml"/>
		</fieldset>
	</form>
	<hr>
	<div style="clear:both"></div>
	<div id="payments_from_xml">
		{include file="{$DOC_ROOT}/templates/lists/payments-from-xml.tpl" payments=$payments}
	</div>

</div>
