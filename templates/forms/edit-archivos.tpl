<div id="divForm">
	<form id="editArchivoForm" name="editArchivoForm" method="post" enctype="multipart/form-data">
			<input type="hidden" id="departamentosArchivosId" name="departamentosArchivosId" value="{$post.departamentosArchivosId}"/>
			<input type="hidden" id="type" name="type" value="saveEditArchivo"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre:</div><input name="name" id="contractId" type="text" value="{$post.name}" size="50"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Archivo:</div><input name="path" id="path" type="file" value="" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="submit" id="editArchivo" name="editArchivo" class="buttonForm" value="Actualizar" />
			</div>
		</fieldset>
	</form>
</div>
