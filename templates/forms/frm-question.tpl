<div id="divForm">
	<form id="frmMultiple" name="frmMultiple" method="post"  onsubmit="return false" autocomplete="off">
		{if $post.id}<input type="hidden" id="id" name="id" value="{$post.id}"/>{/if}
		<fieldset>
			<fieldset>
				<div class="grid_16">
					<div class="grid_16">
						<div class="formLine" style="width:100%;  display: inline-block;">
							<div style="width:30%;float:left"> * Servicio</div>
							<div style="width:70%;float: left;">
								<select name="service_id" id="service_id" class="largeInput">
									<option value="">Seleccionar</option>
									{foreach from=$services key=key item=item}
										<option value="{$item.tipoServicioId}" {if $item.tipoServicioId eq $post.service_id}selected{/if}>{$item.nombreServicio}</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					<hr>
					<div class="grid_16">
						<div class="formLine" style="width:100%;  display: inline-block;">
							<div style="width:30%;float:left"> * Pregunta</div>
							<div style="width:70%;float: left;">
								<input name="question" id="question" class="largeInput" value="{$post.question}" />
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="grid-16">
					<div class="grid_6">
						<div class="formLine" style="width:100%;  display: inline-block;">
							<div style="width:100%;float:left"> * Respuesta</div>
							<div style="width:100%;float: left;">
								<input type="text" name="optionText" id="optionText" class="largeInput">
							</div>
						</div>
						<hr />
						<div class="formLine" style="width:100%;  display: inline-block;">
							<div style="width:100%;float:left"> * Valor</div>
							<div style="width:100%;float: left;">
								<input type="text" name="optionPrice" id="optionPrice" class="largeInput">
							</div>
						</div>
						<hr />
						<a  href="javascript:;" class="button_grey spanAddOption">
							<span>Guardar respuesta</span>
						</a>
					</div>
					<div class="grid_10" id="listOption">
						{include file="{$DOC_ROOT}/templates/lists/option-question.tpl" options=$post.answer}
					</div>
				</div>
			</fieldset>
		</fieldset>
		<div style="clear:both"></div>
		<hr>
		<div class="actionPopup">
			<span class="msjRequired">* Campos requeridos </span><br>
			<div class="actionsChild">
				<img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img-mul"/>
			</div>
			<div class="actionsChild">
				<a  href="javascript:;" class="button_grey spanSaveQuestion">
					<span>{if $post}Actualizar{else}Guardar{/if}</span>
				</a>
			</div>
		</div>
	</form>

</div>
