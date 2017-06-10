<div id="divForm">
	<form id="editArchivoFechaForm" name="editArchivoFechaForm" method="post">
		<input type="hidden" id="type" name="type" value="saveEditArchivoFecha"/>
		<input type="hidden" id="archivoId" name="archivoId" value="{$post.archivoId}"/>
    <input name="contractId" id="contractId" type="hidden" value="{$post.contractId}" size="50"/>
		<fieldset>


            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Fecha de vencimiento:</div>
         <div style="width:30%;float:left"><input  name="datef" id="datef" type="text" value="{$post.date}" size="27"/> </div>
                <div style="width:30%;float:left"><a href="javascript:NewCal('datef','ddmmyyyy')"><img src="{$WEB_ROOT}/images/cal.gif" width="16" height="16" border="0" alt="Pick a date"></a></div>
				<hr />
			</div>	


        <div style="clear:both"></div>
			<div class="formLine" style="text-align:center">
				<input type="button" id="editArchivoFecha" name="editArchivoFecha" class="buttonForm" value="Actualizar" />
			</div>
		</fieldset>
	</form>
</div>
