<div id="divForm">
	<form id="editContractForm" name="editContractForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre Proyecto:</div>
                <input class="smallInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
				<hr />
            </div>
            {*	
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
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $post.active}checked{/if} value="1"/>
                <hr />       
              </div>
             *}
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editContract"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditContract"/>
			<input type="hidden" id="contractId" name="contractId" value="{$post.contractId}"/>
		</fieldset>
	</form>
</div>
