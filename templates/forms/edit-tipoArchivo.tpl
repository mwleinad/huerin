<div id="divForm">
	<form id="editTipoArchivoForm" name="editTipoArchivoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre del Archivo:</div><input name="descripcion" id="descripcion" type="text" value="{$post.descripcion}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editTipoArchivo" name="editTipoArchivo" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditTipoArchivo"/>
			<input type="hidden" id="tipoArchivoId" name="tipoArchivoId" value="{$post.tipoArchivoId}"/>
		</fieldset>
	</form>
</div>
