<div id="divForm">
<form name="frmCancelar" id="frmCancelar" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="type" value="cancelar_factura" />
    <input type="hidden" name="id_comprobante" value="{$id_comprobante}" />
    <fieldset>
        <div class="a">
            <div class="l">Motivo de la cancelacion *</div>
            <div class="r"><textarea name="motivo" id="motivo" class="largeInput wide2"></textarea>
          </div>
        </div>
        <div style="clear:both"></div>
        <div class="actionPopup">
            <span class="msjRequired">* Campos requeridos </span><br>
            <span class="msjRequired">*  El proceso puede llevar varios segundos. Favor de ser paciente y no dar click 2 veces en el boton.</span><br>
            <div class="actionsChild">
                <img  src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
            </div>
            <div class="actionsChild">
                <a  href="javascript:;" id="btnCancelar" name="btnCancelar" class="button_grey">
                    <span>Guardar</span>
                </a>
            </div>
        </div>
    </fieldset>
</form>
</div>
