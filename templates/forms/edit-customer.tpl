<div id="divForm">
	<form id="editCustomerForm" name="editCustomerForm" method="post">
  <input type="hidden" name="valur" id="valur" value="{$valur}" />
  <input type="hidden" name="tipo" id="tipo" value="{$tipo}" />
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Directivo:</div>
                <input class="smallInput medium" name="nameContact" id="nameContact" type="text" value="{$post.nameContact}" size="50"/>
				<hr />
        </div>		
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tel&eacute;fono Contacto Directivo:</div>
                <input class="smallInput medium" name="phone" id="phone" type="text" value="{$post.phone}" size="50"/>            	<hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Email Contacto Directivo:</div>
                <input class="smallInput medium" name="email" id="email" type="text" value="{$post.email}" size="50"/>
                <hr />
			</div> 
      
          <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Password:</div>
                <input class="smallInput medium" name="password" id="password" type="text" value="{$post.password}" size="50"/>
                <hr />
			</div>          

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Observaciones:</div>
                <textarea class="smallInput medium" name="state" id="state" size="50">{$post.state}</textarea>
                <hr />
			</div>          
      
             <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $post.active}checked{/if} value="1"/>
                <hr />       
              </div>

		<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Fecha de Alta:</div>
                <input  style="width:20%!important;" class="largeInput" onclick="CalendarioSimple(this)" name="fechaAlta" id="fechaAlta" type="text" value="{$post.fechaMysql}" maxlength="10"/>
                <hr />
        </div>		

		<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* NO generar factura 13:</div>
         <div style="width:30%;float:left"><input  name="noFactura13" id="noFactura13" type="checkbox" value="Si" {if $post.noFactura13 == "Si"} checked="checked"{/if}/> </div>
				<hr />
        </div>		
                      
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editCustomer"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditCustomer"/>
			<input type="hidden" id="customerId" name="customerId" value="{$post.customerId}"/>
		</fieldset>
	</form>
</div>
