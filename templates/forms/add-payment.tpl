<div id="divForm">
	<form id="frmAddPayment" name="frmAddPayment" method="post" enctype="multipart/form-data" onsubmit="return false">
		<input type="hidden" id="comprobanteId" name="comprobanteId" value="{$id_comprobante}" class="largeInput"/>
		<input type="hidden" id="efectivo" name="efectivo" value="{if isset($smarty.get.id)}0{else}1{/if}" class="largeInput"/>
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

			{if $post.version == '3.3'}
				<div class="formLine" style="width:100%; text-align:left">
					<div style="width:25%;float:left; color:#f00">Generar comprobante con complemento de pago por este importe?</div>
					<!-- CAMPO generarComprobantePago   -->
					<input type="checkbox" name="generarComprobantePago" checked id="generarComprobantePago">
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
</div>
