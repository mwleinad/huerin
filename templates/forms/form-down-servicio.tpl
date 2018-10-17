<div id="divForm">
	<form id="frmDownServicio" name="frmDownServicio" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="doDownServicio"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$post.servicioId}"/>
		<fieldset>
		<div class="formLine">
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
				<a  href="javascript:;" id="btnDownServicio" name="btnDownServicio" class="button_grey">
					<span>Guardar</span>
				</a>
			</div>
		</div>
		</fieldset>
	</form>
</div>