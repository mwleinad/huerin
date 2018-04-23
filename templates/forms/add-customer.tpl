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
                <textarea class="smallInput medium" name="state" id="state">{$post.state}</textarea>
                <hr />
			</div>           
      
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" value="1" checked="checked"/>
                <hr />       
              </div>

        <div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Fecha de Alta:</div>
            <div style="width:30%;float:left"><input  name="fechaAlta" id="fechaAlta" type="text" value="{$post.inicioOperacionesMysql}" size="27"/> </div>
            <div style="width:30%;float:left"><a href="javascript:NewCal('fechaAlta','ddmmyyyy')"><img src="{$WEB_ROOT}/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></div>
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
