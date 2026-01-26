<div id="divForm">
	<form id="frm-correo-recotizacion" name="frm-correo-recotizacion" method="post" onsubmit="return false;" action="#"  autocomplete="off">
			<input name="id" id="id" type="hidden" value="{$post.id}" size="50"/>
			<input type="hidden" id="type" name="type" value="3"/>
		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                  <p style="font-family: 'Tw Cen MT'; font-size: 16px; color: #767070; text-align: justify">
                      <b>IMPORTANTE!!. </b>
                      Este proceso realiza el envio de la carta de ajuste de precios en los servicios, al correo de los responsables de Cuentas por cobrar
                      de cada empresa.
                  </p> 
                </div>
                <div class="grid_16">
                    <p style="font-family: 'Tw Cen MT'; font-size: 16px; color: #767070; text-align: justify">
                        Favor de seleccionar el mes a partir del cual tendra efecto el ajuste de precios, para incluirlo en la carta.
                    </p> 
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float: left;">
                                <select name="mes_inicio" id="mes_inicio" class="largeInput">
                                    <option value="">Seleccione una opción</option>
                                    <option value="enero">Enero</option>
                                    <option value="febrero">Febrero</option>
                                    <option value="marzo">Marzo</option>
                                    <option value="abril">Abril</option>
                                    <option value="mayo">Mayo</option>
                                    <option value="junio">Junio</option>
                                    <option value="julio">Julio</option>
                                    <option value="agosto">Agosto</option>
                                    <option value="septiembre">Septiembre</option>
                                    <option value="octubre">Octubre</option>
                                    <option value="noviembre">Noviembre</option>
                                    <option value="diciembre">Diciembre</option>
                                </select>
                                <div id="error-mes_inicio" style="color: red; display: none;"></div>
                        </div>
                    </div>
                </div>
                <div class="grid_16">
                    <p style="font-family: 'Tw Cen MT'; font-size: 16px; color: #767070; text-align: justify">
                        Favor de seleccionar el departamento de los encargados a cual se enviara la carta.
                    </p> 
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float: left;">
                               <select name="departamento" id="departamento"  class="largeInput">
                                <option value="">Seleccione...</option>
                                {foreach from=$departamentos item=departamento}
                                    <option value="{$departamento.departamento}">{$departamento.departamento}</option>
                                {/foreach}
                            </select>
                                <div id="error-departamento" style="color: red; display: none;"></div>
                        </div>
                    </div>
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
