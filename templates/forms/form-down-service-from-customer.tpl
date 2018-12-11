<div id="divForm">
	<form id="frmDownServicio" name="frmDownServicio" method="post" enctype="multipart/form-data" onsubmit="return false">
	<input type="hidden" id="type" name="type" value="{$post.typeSave}"/>
	<input type="hidden" id="customerId" name="customerId" value="{$post.id}"/>
		<fieldset>
		{if $post.reactive}
			<p style="margin-bottom: 10px;color: red;">Seleccione una o varias razones sociales para reactivar todos sus servicios con baja temporal.</p>
		{else}
			<p style="margin-bottom: 10px;color: red;">Seleccione una o varias razones sociales que desea darle de baja temporal a todos sus servicios.</p>
			<p style="margin-bottom: 10px;color: red;">El campo ultimo workflow sera aplicado para todos los servicios, favor de verificar.</p>
		{/if}
		<div class="formLine">
			<table width="100%">
				{foreach from=$contratos item=item key=key}
					<tr>
						<td><input type="checkbox" name="idContracts[]" class="smallInput medium" value="{$item.contractId}"/></td>
						<td>{$item.name}</td>
						{if !$post.reactive}
							<td>Ultimo workflow : </td>
							<td><input type="text" name="dateWorkflow{$item.contractId}" id="dateWorkflow{$item.contractId}" value="" onclick="CalendarioSimple(this)" class="largeInput" maxlength="10"/></td>
						{/if}
					</tr>
				{/foreach}
			</table>
			<hr />
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