<div id="divForm">
	<form id="frmRol" name="frmRol" method="post" onsubmit="return false;"  enctype="multipart/form-data">
		{if $post}
			<input name="rolId" id="rolId" type="hidden" value="{$post.rolId}" size="50"/>
			<input type="hidden" id="type" name="type" value="updateRol"/>
		{else}
			<input type="hidden" id="type" name="type" value="saveRol"/>
		{/if}
		<fieldset>
			<div class="formLine" style="width:100%;  display: inline-block;">
				<div style="width:30%;float:left">Nombre de rol</div>
				<div style="width:70%;float: left;">
					<input type="text" name="name" id="name" value="{$post.name}" class="largeInput "/>
				</div>

			</div>
			<div style="clear:both"></div>
			<hr />
			<div class="formLine" style="text-align:center">
				<img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-imgs"/>
				<input type="submit" {if !$post}id="btnRol" name="btnRol"{else}id="btnEdit" name="btnEdit"{/if} class="buttonForm" value="{if $post}Actualizar{else}Guardar{/if}" />
			</div>
		</fieldset>
	</form>
</div>
