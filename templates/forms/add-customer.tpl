<div id="divForm">
	<form id="addCustomerForm" name="addCustomerForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddCustomer"/>
		<fieldset>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Directivo:</div>
                <input class="smallInput medium" name="nameContact" id="nameContact" type="text" value="{$post.name}" size="50"/>
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
                <textarea class="largeInput medium" name="state" id="state">{$post.state}</textarea>
                <hr />
			</div>           
      
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" value="1" checked="checked"/>
                <hr />       
              </div>

        <div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Fecha de Alta:</div>
            <input style="width:20%!important;"  name="fechaAlta" id="fechaAlta" class="largeInput" type="text" value="{$post.inicioOperacionesMysql}" onclick="CalendarioSimple(this)"  maxlength="10"/>
            <hr />
        </div>		
              
			<div style="clear:both"></div>
			* Campos requeridos
             {include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}  
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="btnAddCustomer"><span>Agregar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>
