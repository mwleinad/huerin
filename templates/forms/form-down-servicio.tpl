<div id="divForm">
	<form id="frmDownServicio" name="frmDownServicio" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="doDownServicio"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$post.servicioId}"/>
		<fieldset>
		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left"> * Tipo de baja</div>
			<div style="width:52%; float:left;">
				<select name="tipoBaja" id="tipoBaja" class="largeInput">
					<option value="">Seleccionar ... </option>
					<option value="complete">Baja definitiva</option>
					<!--<option value="partial">Baja parcial</option>-->
				</select>
			</div>
		<hr>
       	</div>
		<div class="formLine" style="width:100%; text-align:left; display:none;" id="tagLastDatetWorkflow">
			<div style="width:30%;float:left">* Ultima fecha de workflow:</div>
			<div style="width:15%;float:left;vertical-align: middle">
				<input type="text" name="lastDateWorkflow" id="lastDateWorkflow" value="" onclick="CalendarioSimple(this)" class="largeInput" maxlength="10"/>
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
				<input type="submit" id="btnDownServicio" name="btnDownServicio" class=" actionsChild button_grey" value="Guardar" />
			</div>
		</div>
		</fieldset>
	</form>
</div>