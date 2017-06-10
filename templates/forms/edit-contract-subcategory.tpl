<div id="divForm">
	<form id="editSubcategoryForm" name="editSubcategoryForm" method="post">
    <fieldset>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">* Nombre:</div><input class="smallInput medium" name="name" id="name" type="text" value="{$info.name}" size="50"/>
        <hr />       
      </div>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $info.active}checked{/if} value="1"/>
        <hr />       
      </div>
      <div style="clear:both"></div>
      * Campo requerido		
     	<div class="formLine" style="text-align:center; margin-left:300px">            
            <a class="button_grey" id="editSubcategory"><span>Actualizar</span></a>           
     	</div>
        <input type="hidden" id="type" name="type" value="saveEditSubcategory"/>
        <input type="hidden" id="id" name="id" value="{$info.contSubcatId}" />         
  	</fieldset>
	</form>
</div>
