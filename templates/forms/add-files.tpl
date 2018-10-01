<div id="divForm">
	<form id="frmDropzone"  method="post" enctype="multipart/form-data" onsubmit="return false">
		<input name="id" id="id" type="hidden" value="{$post.contractId}" />
		<input name="tipoFile" id="tipoFile" type="hidden" value="{$post.tipo}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left"> * Tipo de {$post.tipo|ucfirst}:</div>
				<div style="width:52%; float:left;">
					<select name="fileId" id="fileId" class="largeInput">
						<option value="">Seleccionar ...</option>
						{foreach from=$tiposFiles item=item}
							<option value="{$item.id}">{$item.nombre}</option>
						{/foreach}
					</select>
				</div>
				<hr>
			</div>
			{if $post.tipo eq 'archivo'}
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Fecha de vencimiento:</div>
				<div style="width:15%;float:left;vertical-align: middle">
					<input  class="largeInput" type="text" name="datef" id="datef" value="" maxlength="10" onclick="CalendarioSimple(this)"/>
				</div>
				<hr>
			</div>
			{/if}
			<div class="formLine" style="width:100%; text-align:left" >
				<div style="width:100%;float:left" id="idDropzone" class="dropzone"></div>
				<hr>
			</div>

			<div style="clear:both"></div>
			<input type="hidden" id="type" name="type" value="saveFile"/>
		</fieldset>
	</form>
</div>