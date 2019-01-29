<div id="divForm">
	<form id="frmDropzone" name="frmDropzone"  method="post" enctype="multipart/form-data" onsubmit="return false">
		<input type="hidden" id="type" name="type" value="updateFilesWorkflow"/>
		<input type="hidden" id="contractId" name="contractId" value="{$post.contractId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left" >
				<p>Adjuntar un archivo de tipo zip, la estructura del archivo debe estar formado siguiendo las instrucciones que se definieron para crear zip de worklows.</p>
				<p><a class="spanAll" href="{$WEB_ROOT}/tutoriales/instrucciones_crear_zip.pdf" target="_blank" title="Ver instrucciones">Ver instrucciones</a></p>
				<span style="color: #ff1906;">Unicamente adjunte el archivo si esta seguro de actualizar o cargar los documentos en los workflows.</span>
			</div>
			<div class="clearfix"></div>
			<div class="formLine" style="width:100%; text-align:left" >
				<div style="width:30%;float:left">* Archivo</div>
				<div style="width:100%;float:left" id="idDropzone" class="dropzone"></div>
				<hr>
			</div>
			<div style="clear:both"></div>
		</fieldset>
	</form>
</div>