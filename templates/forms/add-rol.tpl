<div id="divForm">
	<form id="frmRol" name="frmRol" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		{if $post}
			<input name="rolId" id="rolId" type="hidden" value="{$post.rolId}" size="50"/>
			<input type="hidden" id="type" name="type" value="updateRol"/>
		{else}
			<input type="hidden" id="type" name="type" value="saveRol"/>
		{/if}
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left"> * Nombre de rol</div>
				<div style="width:70%;float: left;">
					<input type="text" name="name" id="name" value="{$post.name}" class="largeInput "/>
				</div>
				<hr>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">* Departamento</div>
				<div style="width:70%;float: left;">
					<select name="depId" id="depId" class="largeInput">
						<option value="">Seleccionar..</option>
						{foreach from=$deps item=item key=key}
							<option value="{$item.departamentoId}" {if $item.departamentoId eq $post.departamentoId}selected{/if}>{$item.departamento}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">* Categoria</div>
				<div style="width:70%;float: left;">
					<select name="nivel" id="nivel" class=" largeInput">
						<option value="">Seleccionar..</option>
						{foreach from=$niveles item=item key=key}
							<option value="{$item.categoria}" {if $item.categoria eq $post.nivel}selected{/if}>{$item.categoria}</option>
						{/foreach}
						<option value="100" {if $post.nivel >= 100}selected{/if}>100</option>
					</select>
				</div>
				<hr>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<span style="float:left">* Campos Obligatorios</span>
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
				<input type="submit" {if !$post}id="btnRol" name="btnRol"{else}id="btnEdit" name="btnEdit"{/if} class="buttonForm" value="{if $post}Actualizar{else}Guardar{/if}" />
			</div>
		</fieldset>
	</form>
</div>
