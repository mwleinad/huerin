<div id="divForm">
	<form id="frm-correo-recotizacion" name="frm-correo-recotizacion" method="post" onsubmit="return false;" action="#"  autocomplete="off">
			<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>
			<input type="hidden" id="type" name="type" value="3"/>
		<fieldset>
            <div class="container_16">
                <div class="grid_16">

                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left; font-weight: bold"><em style="color: #ff0000">*</em> Mensaje</div>
                        <div style="width:100%;float: left;">
                            <textarea name="mensaje" id="mensaje" class="largeInput medium2">
                            </textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="grid_16">
                    <span style="float:left"><em style="color: #ff0000">*</em> Campos Obligatorios</span>
                </div>
                <div class="grid_16" style="text-align: center">
                    <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                </div>
                <div class="grid_16" style="text-align: center">
                    <div class="formLine"  style="display: inline-block">
                        <a href="javascript:;"  id="btn-enviar-recotizacion" class="button_grey"><span>Enviar</span></a>
                    </div>
                </div>
            </div>
		</fieldset>
	</form>
</div>
