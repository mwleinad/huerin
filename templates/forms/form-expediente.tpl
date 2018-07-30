<div id="divForm">
	<form id="frmExpediente" name="frmExpediente" method="post" onsubmit="return false">

			<input type="hidden" id="type" name="type" value="{if $info}updateExpediente{else}saveExpediente{/if}"/>
			<input type="hidden" id="id" name="id" value="{$info.expedienteId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
				<div style="width:70%;display:inline-block"><input name="nombre" id="nombre" type="text" value="{$info.name}" class="largeInput medium" /></div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<div style="display:inline-block;text-align: center;">
					<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
					<a class="button_grey" id="btnExpediente" name="btnExpediente"><span>{if $info}Actualizar{else}Guardar{/if}</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
