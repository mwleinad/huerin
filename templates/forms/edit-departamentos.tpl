<div id="divForm">
	<form id="editDepartamentosForm" name="editDepartamentosForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del Departamento:</div><input name="departamento" id="departamento" type="text" value="{$post.departamento}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editDepartamentos" name="editDepartamentos" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditDepartamentos"/>
			<input type="hidden" id="departamentoId" name="departamentoId" value="{$post.departamentoId}"/>
		</fieldset>
	</form>
</div>
