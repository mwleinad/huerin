<div id="divForm">
	<form id="frmAcuerdoComercial" name="frmAcuerdoComercial" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<input type="hidden" id="type" name="type" value="saveUpAcuerdo"/>
		<input name="id" id="id" type="hidden" value="{$contrato.contractId}"/>
		<fieldset>
            <div class="grid_16">
                <div class="grid_16">
                    <div class="formLine" style="width:100%;  display: inline-block;">
                        <div style="width:100%;float:left"> * Acuerdo comercial</div>
                        <div style="width:100%;float: left;">
                            <textarea name="acuerdo_comercial" id="acuerdo_comercial" rows="10"
                                      class="largeInput">{$contrato.acuerdo_comercial}</textarea>
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
                    <a href="javascript:;"  class="button_grey btn-agregar-acuerdo"><span>{if $contrato.acuerdo_comercial}Actualizar{else}Guardar{/if}</span></a>
                </div>
            </div>
		</fieldset>
	</form>
</div>
