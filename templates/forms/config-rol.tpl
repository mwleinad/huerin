<div id="divForm">
	<form id="frmPermisos" name="frmPermisos" method="post">
			<input type="hidden" id="type" name="type" value="save_config"/>
			<input type="hidden" id="id" name="id" value="{$info.rolId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
                {function name=draw_permiso level=0}
					<ul class="level{$level}">
                        {foreach from=$data item=item key=key}
                            {if $item.children|@count > 0}
								<li><input type="checkbox" name="permisos[]" value="{$item.permisoId}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                                {draw_permiso data=$item.children level=$level+1}
                            {else}
								<li><input type="checkbox" name="permisos[]" value="{$item.permisoId}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                            {/if}
                        {/foreach}
					</ul>
                {/function}
				<ul id="lista-main">
                    {foreach from=$modulos item=item key=key}
						<li><input type="checkbox" name="permisos[]" value="{$item.permisoId}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                        {if $item.children|@count > 0}
                            {draw_permiso data=$item.children level=1}
                        {/if}
                    {/foreach}
				</ul>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div style="clear:both"></div>
			<div class="formLine" style="text-align:center; margin-left:300px">
				<a class="button_grey" id="saveConfig"><span>Guardar permiso</span></a>
			</div>
		</fieldset>
	</form>
</div>
