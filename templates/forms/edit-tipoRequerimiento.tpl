<div id="divForm">
	<form id="editTipoRequerimientoForm" name="editTipoRequerimientoForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del Requerimiento:</div><input name="nombre" id="nombre" type="text" value="{$post.nombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="editTipoRequerimiento" name="editTipoRequerimiento" class="buttonForm" value="Actualizar" />
			</div>
			<input type="hidden" id="type" name="type" value="saveEditTipoRequerimiento"/>
			<input type="hidden" id="tipoRequerimientoId" name="tipoRequerimientoId" value="{$post.tipoRequerimientoId}"/>
		</fieldset>
	</form>
</div>
