<div id="divForm">
	<form id="{$data.nameForm}" name="{$data.nameForm}" method="post" onsubmit="return false">
		<input type="hidden" id="type" name="type" value="{$data.nameType}"/>
		{if $post}
			<input type="hidden" id="changeId" name="changeId" value="{$post.changeId}"/>
		{/if}
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Status:</div>
				<div style="width:70%;display:inline-block">
					<select name="status" id="status" class="largeInput medium">
						<option value="">Seleccionar....</option>
						{if $post.status neq "pendiente"}<option value="pendiente">Pendiente</option>{/if}
						{if $post.status neq "revision"}<option value="revision">Revision</option>{/if}
						{if $post.status neq "finalizado"}<option value="finalizado"}>Finalizado</option>{/if}
					</select>
				</div>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<div style="display:inline-block;text-align: center;">
					<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
					<a class="button_grey" id="btnChange" name="btnChange"><span>{$data.nameBtn}</span></a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
