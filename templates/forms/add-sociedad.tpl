<div id="divForm">
	<form id="addSociedadForm" name="addSociedadForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddSociedad"/>
			<input type="hidden" id="sociedadId" name="sociedadId" value="{$post.sociedadId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre de la Sociedad:</div><input name="nombreSociedad" id="nombreSociedad" type="text" value="{$post.nombreSociedad}"  class="largeInput"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			{include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
			<div class="formLine" style="text-align:center; margin-left:300px">
				<a class="button_grey" id="addSociedadButton" name="addSociedadButton"><span>Agregar</span></a>
			</div>
		</fieldset>
	</form>
</div>
