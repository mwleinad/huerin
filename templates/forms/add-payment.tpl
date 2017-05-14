<div id="divForm">
	<form id="editCustomerForm" name="editCustomerForm" method="post" enctype="multipart/form-data">
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
         <div style="width:30%;float:left"><input  name="paymentDate" id="paymentDate" type="text" value="{$fecha}" size="35" class="largeInput"/> </div>
                <div style="width:30%;float:left"><a href="javascript:NewCal('paymentDate','ddmmyyyy')"><img src="{$WEB_ROOT}/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></div>
        </div>		
        
      <div style="clear:both"></div>  
        
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Importe:</div>
                <input class="largeInput medium" name="amount" id="amount" type="text" value="" size="50" maxlength="15"/>
        </div>		

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">  Comprobante de pago:</div>
                <input class="largeInput medium" name="comprobante" id="comprobante" type="file" value="" size="50"/>
				<hr />
        </div>		
            
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <input type="submit" class="button_grey" id="" value="Agregar Pago" />          
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditCustomer"/>
		</fieldset>
	</form>
</div>
