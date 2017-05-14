<div id="divForm">
	<form id="addDepartamentosForm" name="addDepartamentosForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddDepartamentos"/>
			<input type="hidden" id="departamentoId" name="departamentoId" value="{$post.departamentoId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del departamento:</div><input name="departamento" id="departamento" type="text" value="{$post.departamento}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addDepartamentosButton" name="addDepartamentosButton" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
