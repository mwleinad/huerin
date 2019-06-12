<div id="divForm">
	<form id="frmCheckStatusInvoice" name="frmCheckStatusInvoice" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="checkStatusInvoiceInSat"/>
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:10%;float:left">* Serie</div>
				<div style="width:70%;float: left;">
					<input type="text" name="serie" id="serie"  class="xsmallIn "/>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:10%;float:left">* Folio</div>
				<div style="width:70%;float: left;">
					<input type="text" name="folio" id="folio"  class="xsmallIn"/>
				</div>
			</div>
			<hr />
			<div class="formLine" style="text-align:center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				<input type="submit" id="btnCheckStatus" value="Verificar" class="btn" />
			</div>
		</fieldset>
	</form>
</div>
