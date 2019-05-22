<div id="divForm">
	<form id="editCustomerForm" name="editCustomerForm" method="post" autocomplete="off">
	<input type="hidden" id="type" name="type" value="saveEditServicio"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$post.servicioId}"/>
	<input type="hidden" id="contractId" name="contractId" value="{$post.contractId}"/>
	<fieldset>
		<div class="formLine" style="width:100%; text-align:left">
		   <div style="width:30%;float:left">* Servicio:</div>
		   <select name="tipoServicioId" id="tipoServicioId" class="smallInput medium" onchange="UpdateCosto()">
			   {foreach from=$tiposDeServicio item=item}
				<option {if $item.tipoServicioId == $post.tipoServicioId} selected="selected"{/if}value="{$item.tipoServicioId}">{$item.nombreServicio}</option>
			   {/foreach}
		   </select>
			<hr />
		</div>
		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">* Fecha Inicio Operaciones:</div>
			<input style="width:20%!important;float:left" class="smallInput medium" onclick="CalendarioSimple(this)" name="inicioOperaciones" id="inicioOperaciones" type="text" value="{$post.inicioOperacionesMysql}" maxlength="10" />
			<hr />
		</div>
		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">Inicio Factura:</div>
			<input style="width:20%!important;float:left" class="smallInput medium" onclick="CalendarioSimple(this)"  name="inicioFactura" id="inicioFactura" type="text" value="{$post.inicioFacturaMysql}" />
			<hr />
		</div>

		<div class="formLine" style="width:100%; text-align:left">
			<div style="width:30%;float:left">Costo:</div>
			<input class="smallInput medium" name="costo" id="costo" type="text" value="{$post.costo}" size="50"/>
			<hr />
		</div>
		<div style="clear:both"></div>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
			</div>
			<div class="actionsChild">
				<a class="button_grey" id="editCustomer"><span>Actualizar</span></a>
			</div>
		</div>
	</fieldset>
	</form>
</div>
