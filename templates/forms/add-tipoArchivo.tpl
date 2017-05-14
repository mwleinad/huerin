<div id="divForm">
	<form id="addTipoArchivoForm" name="addTipoArchivoForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddTipoArchivo"/>
			<input type="hidden" id="tipoArchivoId" name="tipoArchivoId" value="{$post.tipoArchivoId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del archivo:</div><input name="descripcion" id="descripcion" type="text" value="{$post.descripcion}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addTipoArchivoButton" name="addTipoArchivoButton" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
