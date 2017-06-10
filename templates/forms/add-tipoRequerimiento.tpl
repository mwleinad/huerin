<div id="divForm">
	<form id="addTipoRequerimientoForm" name="addTipoRequerimientoForm" method="post">
			<input type="hidden" id="type" name="type" value="saveAddTipoRequerimiento"/>
			<input type="hidden" id="tipoRequerimientoId" name="tipoRequerimientoId" value="{$post.tipoRequerimientoId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Nombre del Requerimiento:</div><input name="nombre" id="nombre" type="text" value="{$post.nombre}" size="50"/>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<input type="button" id="addTipoRequerimientoButton" name="addTipoRequerimientoButton" class="buttonForm" value="Agregar" />
			</div>
		</fieldset>
	</form>
</div>
