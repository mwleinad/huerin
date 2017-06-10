<div id="divForm">
	<form id="editCategoryForm" name="editCategoryForm" method="post">
    <fieldset>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">* Nombre:</div><input name="name" id="name" type="text" value="{$info.name}" class="smallInput medium" size="50"/>
        <hr />       
      </div>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $info.active}checked{/if} value="1"/>
        <hr />       
      </div>
      <div style="clear:both"></div>
      * Campo requerido     	
        <div class="formLine" style="text-align:center; margin-left:300px">            
            <a class="button_grey" id="editCategory"><span>Actualizar</span></a>           
     	</div>
        <input type="hidden" id="type" name="type" value="saveEditCategory"/>
        <input type="hidden" id="id" name="id" value="{$info.contCatId}" />       
  	</fieldset>
	</form>
</div>
