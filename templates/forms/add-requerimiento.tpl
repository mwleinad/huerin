<div id="divForm">
	<form id="addRequerimientoForm" name="addRequerimientoForm" method="post" enctype="multipart/form-data">
  	<input name="contractId" id="contractId" type="hidden" value="{$contractId}" size="50"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Tipo de Requerimiento:</div>
        <select name="tipoRequerimientoId" id="tipoRequerimientoId">
        {foreach from=$tiposRequerimiento item=item}
        	<option value="{$item.tipoRequerimientoId}">{$item.nombre}</option>
        {/foreach}
        </select>
			</div>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Archivo:</div>
        <input type="file" name="path" id="path" value="{$post.path}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="submit" id="addRequerimiento" name="addRequerimiento" class="buttonForm" value="Agregar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveAddRequerimiento"/>
			<input type="hidden" id="requerimientoId" name="requerimientoId" value="{$post.requerimientoId}"/>
		</fieldset>
	</form>
</div>
