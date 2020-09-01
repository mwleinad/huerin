<div id="divForm">
	<form id="frmArchivoDep" name="frmArchivoDep" method="post" enctype="multipart/form-data" onsubmit="return false;" >
		{if $post.departamentosArchivosId}
			<input type="hidden" id="departamentosArchivosId" name="departamentosArchivosId" value="{$post.departamentosArchivosId}"/>
			<input type="hidden" id="type" name="type" value="updateArchivoDepartamento"/>
		{else}
			<input type="hidden" id="type" name="type" value="saveArchivoDepartamento"/>
		{/if}
		<input type="hidden" id="depId" name="depId" value="{$post.departamentoId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
				<input name="name" id="name" type="text" value="{$post.name}" class="largeInput"/>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:100%;float:left">{if !$post.departamentosArchivosId}* {/if}Archivo:</div>
				<div style="width:100%;float:left" id="zoneClick" class="dropzone"></div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="grid_16" style="text-align: center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
			</div>
			<div class="grid_16" style="text-align: center">
				<div class="formLine"  style="display: inline-block">
					<input type="submit" id="btnArchivoDep" class="buttonForm" value="{if $post.departamentosArchivosId}Actualizar{else}Guardar{/if}" />
				</div>
			</div>
		</fieldset>
	</form>
</div>
