<div id="divForm">
	<form id="editImpuestoForm" name="editImpuestoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Impuesto:</div><input name="impuestoNombre" id="impuestoNombre" type="text" value="{$post.impuestoNombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editImpuesto" name="editImpuesto" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditImpuesto"/>
			<input type="hidden" id="impuestoId" name="impuestoId" value="{$post.impuestoId}"/>
		</fieldset>
	</form>
</div>
