<div id="divForm">
	<form id="editDocumentoForm" name="editDocumentoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">ContractId:</div><input name="contractId" id="contractId" type="text" value="{$post.contractId}" size="50"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">TipoDocumentoId:</div><input name="tipoDocumentoId" id="tipoDocumentoId" type="text" value="{$post.tipoDocumentoId}" size="50"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Path:</div><input name="path" id="path" type="text" value="{$post.path}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editDocumento" name="editDocumento" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditDocumento"/>
			<input type="hidden" id="documentoId" name="documentoId" value="{$post.documentoId}"/>
		</fieldset>
	</form>
</div>
