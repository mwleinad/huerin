<div id="divForm">
	<form id="frm-correo-recotizacion" name="frm-correo-recotizacion" method="post" onsubmit="return false;" action="#"  autocomplete="off">
			<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>
			<input type="hidden" id="type" name="type" value="3"/>
		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                  <p style="font-family: 'Tw Cen MT'; font-size: 16px; color: #767070; text-align: justify">
                      <b>IMPORTANTE!!. </b>
                      Este apartado de plataforma, hace llegar por correo al responsable de atencion al cliente, la carta de
                      ajuste de precios en los servicios de las empresas.
                  </p>
                </div>
                <hr>
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
