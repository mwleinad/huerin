<div id="divForm">
	<form id="addObligacionForm" name="addObligacionForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre de la Obligacion:</div><input name="obligacionNombre" id="obligacionNombre" type="text" value="{$post.obligacionNombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addObligacionButton" name="addObligacionButton" class="buttonForm" value="Agregar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveAddObligacion"/>
			<input type="hidden" id="obligacionId" name="obligacionId" value="{$post.obligacionId}"/>
		</fieldset>
	</form>
</div>
