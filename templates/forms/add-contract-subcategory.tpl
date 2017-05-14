<div id="divForm">
	<form id="addSubcategoryForm" name="addSubcategoryForm" method="post">
    <fieldset>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">* Nombre:</div><input class="smallInput medium" name="name" id="name" type="text" value="" size="50"/>
        <hr />       
      </div>
      <div class="formLine" style="width:100%; text-align:left">
        <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" value="1" checked="checked"/>
        <hr />       
      </div>
      <div style="clear:both"></div>
      * Campo requerido		
     	<div class="formLine" style="text-align:center; margin-left:300px">            
            <a class="button_grey" id="addSubcategory"><span>Agregar</span></a>           
     	</div>
        <input type="hidden" id="type" name="type" value="saveAddSubcategory"/>     
  	</fieldset>
	</form>
</div>
