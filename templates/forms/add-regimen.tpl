<div id="divForm">
	<form id="addRegimenForm" name="addRegimenForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddRegimen"/>
			<input type="hidden" id="regimenId" name="regimenId" value="{$post.regimenId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Regimen:</div><input name="regimenName" id="regimenName" type="text" value="{$post.regimenName}" size="50"/>
			</div>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tipo de Persona:</div>
        <select id="tipoDePersona" name="tipoDePersona">
        <option value="Persona Fisica">Persona Fisica</option>
        <option value="Persona Moral">Persona Moral</option>
        </select>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addRegimenButton" name="addRegimenButton" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
