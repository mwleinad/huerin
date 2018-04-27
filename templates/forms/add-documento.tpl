<div id="divForm">
	<form id="addDocumentoForm" name="addDocumentoForm" method="post" enctype="multipart/form-data" onsubmit="return false">
  	<input name="contractId" id="contractId" type="hidden" value="{$contractId}" size="50"/>
		<fieldset>
		<div class="formLine" style="width:100%; text-align:left">
		<div style="width:30%;float:left"> * Tipo de Documento:</div>
		<div style="width:52%; float:left;">
			<select name="tipoDocumentoId" id="tipoDocumentoId" class="largeInput">
                {foreach from=$tiposDocumento item=item}
					<option value="{$item.tipoDocumentoId}">{$item.nombre}</option>
                {/foreach}
			</select>
		</div>
		<hr>
       	</div>
		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Archivo:</div>
			<div style="width:50%;float:left">
				<input type="file" name="path" id="path" value="{$post.path}"  class="largeInput"/>
			</div>
		<hr>
		</div>
		<div class="formLine" style="width:100%; text-align:left; display:none;" id="tagExpiration">
			<div style="width:30%;float:left">* Fecha de expiración:</div>
			<div style="width:15%;float:left;vertical-align: middle">
				<input type="text" name="expiration" id="expiration" value="{$post.dateExpiration}" onclick="CalendarioSimple(this)" class="largeInput" maxlength="10"/>
			</div>
		<hr>
		</div>
  		<div style="clear:both"></div>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
			</div>
			<div class="actionsChild">
				<input type="submit" id="addDocumento" name="addDocumento" class=" actionsChild button_grey" value="Agregar" />
			</div>
		</div>
		<input type="hidden" id="type" name="type" value="saveAddDocumento"/>
		<input type="hidden" id="documentoId" name="documentoId" value="{$post.documentoId}"/>
		</fieldset>
	</form>
</div>