<div id="divForm">
	<form id="frmPorcent" name="frmPorcent" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		{if $post}
			<input name="porcentId" id="porcentId" type="hidden" value="{$post.porcentId}" size="50"/>
			<input type="hidden" id="type" name="type" value="updatePorcentBono"/>
		{else}
			<input type="hidden" id="type" name="type" value="savePorcentBono"/>
		{/if}
		<fieldset>
			{if !$post}
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left"> * Nombre</div>
				<div style="width:70%;float: left;">
					<input type="text" name="name" id="name" value="{$post.name}" class="largeInput "/>
				</div>
				<hr>
			</div>
			{/if}
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">* % de bono(a escala de 1 a 100%)</div>
				<div style="width:70%;float: left;">
					<input type="text" name="porcentaje" id="porcentaje" value="{$post.porcentaje}" class="xsmallIn "/>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">* Monto en pesos</div>
				<div style="width:70%;float: left;">
					<input type="text" name="monto" id="monto" value="{$post.monto}" class="xsmallIn "/>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left"> * Categoria</div>
				<div style="width:70%;float: left;">
					<input type="text" name="categoria" id="categoria" value="{$post.categoria}" class="xsmallIn "/>
				</div>
				<hr>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-imgs"/>
				<input type="submit"  name="btnPorcent" id="btnPorcent" class="buttonForm" value="{if $post}Actualizar{else}Guardar{/if}" />
			</div>
		</fieldset>
	</form>
</div>
