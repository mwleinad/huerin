<div id="divForm">
	<form id="editDocumentForm" name="editDocumentForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre Completo:</div>
                <input class="smallInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
				<hr />
            </div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Informaci&oacute;n requerida:</div>
                <input class="smallInput medium" name="info" id="info" type="text" value="{$post.info}" size="50"/>
				<hr />
            </div>            
             <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $post.active}checked{/if} value="1"/>
                <hr />       
              </div>
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editDocument"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditDocument"/>
			<input type="hidden" id="docBasicId" name="docBasicId" value="{$post.docBasicId}"/>
		</fieldset>
	</form>
</div>
