<div id="divForm">
	<form id="editSociedadForm" name="editSociedadForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre de la Sociedad:</div><input name="nombreSociedad" id="nombreSociedad" type="text" value="{$post.nombreSociedad}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editSociedad" name="editSociedad" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditSociedad"/>
			<input type="hidden" id="sociedadId" name="sociedadId" value="{$post.sociedadId}"/>
		</fieldset>
	</form>
</div>
