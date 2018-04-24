<div id="divForm">
	<form id="addDocumentoForm" name="addDocumentoForm" method="post" enctype="multipart/form-data">
  	<input name="contractId" id="contractId" type="hidden" value="{$contractId}" size="50"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tipo de Archivo:</div>
        <select class="smallInput" name="tipoArchivoId" id="tipoArchivoId">
        {foreach from=$tiposArchivo item=item}
        	<option value="{$item.tipoArchivoId}">{$item.descripcion}</option>
        {/foreach}
        </select>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Archivo:</div>
        <input type="file" name="path" id="path" value="{$post.path}" size="50"/>
			</div>			
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Fecha de vencimiento:</div>
                <input style="width:15%!important;" class="smallInput" type="text" name="datef" id="datef" value="{$post.date}" maxlength="10" onclick="CalendarioSimple(this)" style="vertical-align: middle;"/>
			</div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="submit" id="addDocumento" name="addDocumento" class="buttonForm" value="Agregar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveAddDocumento"/>
			<input type="hidden" id="documentoId" name="documentoId" value="{$post.documentoId}"/>
		</fieldset>
	</form>
</div>
