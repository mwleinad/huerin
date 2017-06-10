<div id="divForm">
	<form id="editTipoDocumentoForm" name="editTipoDocumentoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del Documento:</div><input name="nombre" id="nombre" type="text" value="{$post.nombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editTipoDocumento" name="editTipoDocumento" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditTipoDocumento"/>
			<input type="hidden" id="tipoDocumentoId" name="tipoDocumentoId" value="{$post.tipoDocumentoId}"/>
		</fieldset>
	</form>
</div>
