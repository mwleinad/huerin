<div id="divForm">
	<form id="addDocumentoForm" name="addDocumentoForm" method="post" enctype="multipart/form-data">
  	<input name="id" id="id" type="hidden" value="{$id}" size="50"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre:</div>
        <input type="text" name="name" id="name" class="longInput" />
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Archivo:</div>
        <input type="file" name="path" id="path" value="{$post.path}" size="50" class="longInput"/>
			</div>			
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="submit" id="addDocumento" name="addDocumento" class="buttonForm" value="Subir" />
			</div>
			<input type="hidden" id="type" name="type" value="saveAddDocumento"/>
		</fieldset>
	</form>
</div>
