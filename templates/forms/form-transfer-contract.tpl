<div id="divForm">
	<form id="frmTransferContract" name="frmTransferContract" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="doTransferContract"/>
	<input type="hidden" id="contractId" name="contractId" value="{$post.id}"/>
		<fieldset>
		<div class="formLine">
			<div style="width:30%;float:left">Cliente</div>
			<div style="float:left;vertical-align: middle">
				<select name="customerId" id="customerId" class="largeInput medium2">
					<option value="">Seleccionar un cliente..</option>
					{foreach from=$clientes item=item key=key}
						<option value="{$item.customerId}">{$item.nameContact}-({$item.customerId})</option>
					{/foreach}
				</select>
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
				<a  href="javascript:;" id="btnTransferContract" name="btnTransferContract" class="button_grey">
					<span>Guardar</span>
				</a>
			</div>
		</div>
		</fieldset>
	</form>
</div>