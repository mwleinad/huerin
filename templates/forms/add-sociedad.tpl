<div id="divForm">
	<form id="addSociedadForm" name="addSociedadForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddSociedad"/>
			<input type="hidden" id="sociedadId" name="sociedadId" value="{$post.sociedadId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre de la Sociedad:</div><input name="nombreSociedad" id="nombreSociedad" type="text" value="{$post.nombreSociedad}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addSociedadButton" name="addSociedadButton" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
