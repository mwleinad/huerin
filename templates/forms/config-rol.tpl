<div id="divForm">
	<div class="formLine" style="width:100%; text-align:left">
		<div style="width:30%;float:left">Copiar permisos del rol :</div>
		<select name="rolBaseId" id="rolBaseId" class="smallInput medium" ">
		<option value="">Seleccionar permisos</option>
        {foreach from=$roles item=item key=key}
			<option value="{$item.rolId}">{$item.name}</option>
        {/foreach}
		</select>
		<input type="button" id="copyPermiso" value="Copiar" />
		<hr />
	</div>
	<form id="frmPermisos" name="frmPermisos" method="post">
			<input type="hidden" id="type" name="type" value="save_config"/>
			<input type="hidden" id="id" name="id" value="{$info.rolId}"/>
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
                {function name=draw_permiso level=0 father=''}
					<ul class="noShow level{$level}" id="level{$level}">
                        {foreach from=$data item=item key=key}
                            {if $item.children|@count > 0}
								<li><a href="javascript:void(0);" class="deepList" id="level{$key}{$item.permisoId}">[+]-</a><input type="checkbox" name="permisos[]" value="{$item.permisoId}" class="father-{$item.permisoId} child-{$father}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                                {draw_permiso data=$item.children level="{$key}{$item.permisoId}" father="{$item.permisoId}"}
                            {else}
								<li><a href="javascript:void(0);">[<small>{'x'|lower}</small>]-</a><input type="checkbox" name="permisos[]" value="{$item.permisoId}" class="father-{$item.permisoId} child-{$father}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                            {/if}
                        {/foreach}
					</ul>
                {/function}
				<ul id="lista-main">
                    {foreach from=$modulos item=item key=key}
						{if $item.children|@count > 0}
							<li><a href="javascript:void(0);" class="deepList " id="level{$key}{$item.permisoId}">[+]-</a><input type="checkbox" name="permisos[]" class="father-{$item.permisoId}" value="{$item.permisoId}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                            {draw_permiso data=$item.children level="{$key}{$item.permisoId}" father="{$item.permisoId}"}
						{else}
							<li><a href="javascript:void(0);">[<small>{'x'|lower}</small>]-</a><input type="checkbox" name="permisos[]" value="{$item.permisoId}"  class="father-{$item.permisoId}" {if $item.letMe}checked{/if}/>{$item.titulo}</li>
                        {/if}
                    {/foreach}
				</ul>
			</div>
			<div style="clear:both"></div>
			<hr />
			<div style="clear:both"></div>
			{include file= "{$DOC_ROOT}/templates/boxes/loader.tpl"}
			<div class="formLine" style="text-align:center; margin-left:300px">
				<a class="button_grey" id="saveConfig"><span>Guardar permiso</span></a>
			</div>
		</fieldset>
	</form>
</div>
