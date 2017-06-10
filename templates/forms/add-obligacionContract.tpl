<div id="divForm">
	<form id="addDocumentoForm" name="addDocumentoForm" method="post" enctype="multipart/form-data">
  	<input name="contractId" id="contractId" type="hidden" value="{$contractId}" size="50"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tipo de Obligacion:</div>
        <select name="obligacionId" id="obligacionId">
        {foreach from=$obligaciones item=item}
        	<option value="{$item.obligacionId}">{$item.obligacionNombre}</option>
        {/foreach}
        </select>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="submit" id="addDocumento" name="addDocumento" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
