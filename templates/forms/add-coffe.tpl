<div id="divForm">
	<form id="frmMenu" name="frmMenu" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="saveMenu"/>
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:10%;float:left"> * Platillo</div>
				<div style="width:50%;float: left;padding-right: 10px">
					<input type="text" name="name" id="name"  class="largeInput"/>
				</div>
				<div style="width:10%;float: left;">
					<div style="display: inline-block"> <a class="button" id="btnAddPlatillo" name="btnAddPlatillo" title="Agregar platillo"><span>+</span></a></div>
				</div>
			</div>
			</div>
			<div style="clear:both"></div>
			<div class="stack_platillo"></div>
			<hr />
			<div class="actionPopup">
				<span class="msjRequired">* Campos requeridos </span><br>
				<div class="actionsChild">
					<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				</div>
				<div class="actionsChild">
					<div style="display: inline-block"> <a class="button" id="btnMenu" name="btnMenu" title="guardar"><span>Guardar</span></a></div>
					<div style="display: inline-block"> <a href="{$WEB_ROOT}/vp_menu" class="button_grey" id="btnVistaPrevia" name="btnVistaPrevia" title="Vista previa menu" target="_blank"><span>Vista previa menu</span></a></div>
				</div>
			</div>
		</fieldset>
	</form>
</div>
