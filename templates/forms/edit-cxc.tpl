<div id="divForm">
	<form id="editCustomerForm" name="editCustomerForm" method="post">
		<input type="hidden" id="comprobanteId" name="comprobanteId" value="{$post.comprobanteId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Actualizar Descuento (%):</div>
                <input class="smallInput medium" name="cxcDiscount" id="cxcDiscount" type="text" value="{$post.cxcDiscount}" size="50" maxlength="3"/>
				<hr />
        </div>		
            
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editButton"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditCustomer"/>
		</fieldset>
	</form>
</div>
