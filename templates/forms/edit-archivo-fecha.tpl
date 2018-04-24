<div id="divForm">
	<form id="editArchivoFechaForm" name="editArchivoFechaForm" method="post">
		<input type="hidden" id="type" name="type" value="saveEditArchivoFecha"/>
		<input type="hidden" id="archivoId" name="archivoId" value="{$post.archivoId}"/>
    <input name="contractId" id="contractId" type="hidden" value="{$post.contractId}" size="50"/>
		<fieldset>


            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Fecha de vencimiento:</div>
                <input style="width:15%!important;" class="smallInput" onclick="CalendarioSimple(this)" name="datef" id="datef" type="text" value="{$post.date}" maxlength="10"/>
				<hr />
			</div>	


        <div style="clear:both"></div>
			<div class="formLine" style="text-align:center">
				<input type="button" id="editArchivoFecha" name="editArchivoFecha" class="buttonForm" value="Actualizar" />
			</div>
		</fieldset>
	</form>
</div>
