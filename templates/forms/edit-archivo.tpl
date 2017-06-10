<div id="divForm">
	<form id="editArchivoForm" name="editArchivoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">ContractId:</div><input name="contractId" id="contractId" type="text" value="{$post.contractId}" size="50"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">TipoArchivoId:</div><input name="tipoArchivoId" id="tipoArchivoId" type="text" value="{$post.tipoArchivoId}" size="50"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Path:</div><input name="path" id="path" type="text" value="{$post.path}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editArchivo" name="editArchivo" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditArchivo"/>
			<input type="hidden" id="archivoId" name="archivoId" value="{$post.archivoId}"/>
		</fieldset>
	</form>
</div>
