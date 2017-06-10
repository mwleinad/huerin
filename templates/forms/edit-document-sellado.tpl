<div id="divForm">
	<form id="editDocumentForm" name="editDocumentForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre Completo:</div>
                <input class="smallInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
				<hr />
            </div>  
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" value="1" {if $post.active}checked="checked"{/if}/>
                <hr />       
              </div>        
            
			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editDocument"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditDocument"/>
			<input type="hidden" id="docSelladoId" name="docSelladoId" value="{$post.docSelladoId}"/>
		</fieldset>
	</form>
</div>
