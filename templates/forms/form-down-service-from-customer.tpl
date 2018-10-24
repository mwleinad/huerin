<div id="divForm">
	<form id="frmDownServicio" name="frmDownServicio" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="doBajaTemporal"/>
	<input type="hidden" id="contractId" name="contractId" value="{$post.id}"/>
		<fieldset>
		<p style="margin-bottom: 10px;color: red;">Seleccione una razon social que desea darle de baja temporal a todos sus servicios.</p>
		<p style="margin-bottom: 10px;color: red;">El campo ultima fecha de workflows sera aplicado para todos los servicios, favor de verificar.</p>
		<div class="formLine">
			<div style="width:30%;float:left">* Razones sociales:</div>
			<div style="width:52%;float:left;vertical-align: middle" >
				<select class="largeInput" name="contractId" id="contractId">
					<option value="">Seleccionar...</option>
					{foreach from=$contratos item=item key=key}
						<option value="{$item.contractId}">{$item.name}</option>
					{/foreach}
				</select>
			</div>
			<hr>
		</div>
		<div class="formLine">
			<div style="width:30%;float:left">* Ultima fecha de workflows:</div>
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