<div id="divForm">
	<form id="editRegimenForm" name="editRegimenForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Regimen:</div><input name="regimenName" id="regimenName" type="text" value="{$post.nombreRegimen}" size="50"/>
			</div>
      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tipo de Persona:</div>
        <select id="tipoDePersona" name="tipoDePersona">
        <option value="Persona Fisica" {if $post.tipoDePersona == "Persona Fisica"} selected="selected"{/if}>Persona Fisica</option>
        <option value="Persona Moral" {if $post.tipoDePersona == "Persona Moral"} selected="selected"{/if}>Persona Moral</option>
        </select>
			</div>
			<div style="clear:both"></div>
			<hr />
      
			<div class="formLine" style="text-align:center">
				<input type="button" id="editRegimen" name="editRegimen" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditRegimen"/>
			<input type="hidden" id="regimenId" name="regimenId" value="{$post.regimenId}"/>
		</fieldset>
	</form>
</div>
