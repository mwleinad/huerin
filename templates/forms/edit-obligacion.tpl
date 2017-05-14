<div id="divForm">
	<form id="editObligacionForm" name="editObligacionForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre de la Obligacion:</div><input name="obligacionNombre" id="obligacionNombre" type="text" value="{$post.obligacionNombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editObligacion" name="editObligacion" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditObligacion"/>
			<input type="hidden" id="obligacionId" name="obligacionId" value="{$post.obligacionId}"/>
		</fieldset>
	</form>
</div>
