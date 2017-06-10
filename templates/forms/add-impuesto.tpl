<div id="divForm">
	<form id="addImpuestoForm" name="addImpuestoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Impuesto:</div><input name="impuestoNombre" id="impuestoNombre" type="text" value="{$post.impuestoNombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addImpuestoButton" name="addImpuestoButton" class="buttonForm" value="Agregar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveAddImpuesto"/>
			<input type="hidden" id="impuestoId" name="impuestoId" value="{$post.impuestoId}"/>
		</fieldset>
	</form>
</div>
