<div id="divForm">
	<form id="frmCompany" name="frmCompany" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="2"/>
		<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>
		<fieldset>
            <div class="grid_16">
                <div class="grid_16">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Nombre</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="nombre" id="nombre" value="{$post.nombre}"
                                   class="largeInput medium"/>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div style="clear:both"></div>
            <div class="grid_16">
                <span style="float:left">* Campos Obligatorios</span>
            </div>
            <div class="grid_16" style="text-align: center">
                <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
            </div>
            <div class="grid_16" style="text-align: center">
                <div class="formLine"  style="display: inline-block">
                    <a href="javascript:;"  class="button_grey btn-control-clasificacion"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>
		</fieldset>
	</form>
</div>
