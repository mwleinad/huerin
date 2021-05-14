<div id="divForm">
	<form id="frm" name="frm" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="cancel_cfdi_from_csv"/>
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">* Archivo CSV</div>
				<div style="width:70%;float: left;">
					<input type="file" name="file" id="file"  class="largeInput"/>
				</div>
				<hr>
			</div>
			<hr />
			<div class="grid_16" style="text-align: center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
			</div>
			<div class="grid_16" style="text-align: center">
				<div class="formLine"  style="display: inline-block">
					<a href="javascript:;"  id="btnCheckStatus" class="button_grey">Cancelar</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
