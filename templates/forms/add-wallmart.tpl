<div id="divForm">
	<form id="addWallmartForm" name="addWallmartForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre Completo:</div>
                <input class="smallInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
				<hr />
            </div>		
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tel&eacute;fono:</div>
                <input class="smallInput medium" name="phone" id="phone" type="text" value="{$post.phone}" size="50"/>            	<hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Correo electr&oacute;nico:</div>
                <input class="smallInput medium" name="email" id="email" type="text" value="{$post.email}" size="50"/>
                <hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Usuario:</div>
                <input class="smallInput medium" name="username" id="username" type="text" value="{$post.username}" size="50"/>            	<hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Contrase&ntilde;a:</div>
                <input class="smallInput medium" name="passwd" id="passwd" type="password" value="{$post.passwd}" size="50"/>            	<hr />
			</div>           
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" value="1" checked="checked"/>
                <hr />       
              </div>
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="btnAddWallmart"><span>Agregar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveAddWallmart"/>
		</fieldset>
	</form>
</div>
