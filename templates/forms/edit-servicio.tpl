<div id="divForm">
	<form id="editCustomerForm" name="editCustomerForm" method="post">
	<input type="hidden" id="type" name="type" value="saveEditServicio"/>
	<input type="hidden" id="customerId" name="servicioId" value="{$post.servicioId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Servicio:</div>
               <select name="tipoServicioId" id="tipoServicioId" class="smallInput medium" onchange="UpdateCosto()">
               {foreach from=$tiposDeServicio item=item}
               <option {if $item.tipoServicioId == $post.tipoServicioId} selected="selected"{/if}value="{$item.tipoServicioId}">{$item.nombreServicio}</option>
               {/foreach}
               </select>
				<hr />
            </div>		

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Fecha Inicio Operaciones:</div>
         <div style="width:30%;float:left"><input  name="inicioOperaciones" id="inicioOperaciones" type="text" value="{$post.inicioOperacionesMysql}" size="27"/> </div>
                <div style="width:30%;float:left"><a href="javascript:NewCal('inicioOperaciones','ddmmyyyy')"><img src="{$WEB_ROOT}/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></div>
				<hr />
        </div>		
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Inicio Factura:</div>
         <div style="width:30%;float:left"><input  name="inicioFactura" id="inicioFactura" type="text" value="{$post.inicioFacturaMysql}" size="27"/> </div>
                <div style="width:30%;float:left"><a href="javascript:NewCal('inicioFactura','ddmmyyyy')"><img src="{$WEB_ROOT}/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Costo:</div>
                <input class="smallInput medium" name="costo" id="costo" type="text" value="{$post.costo}" size="50"/>
                <hr />
			</div> 
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editCustomer"><span>Actualizar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>
