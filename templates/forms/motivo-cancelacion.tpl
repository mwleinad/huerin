<div id="divForm">
    <form name="frmCancelar" id="frmCancelar" method="post" action="" onsubmit="return false;">
        <input type="hidden" name="type" value="cancelar_factura"/>
        <input type="hidden" name="id_comprobante" value="{$id_comprobante}"/>
        <fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style="width:100%; display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Motivo de
                            cancelación SAT
                        </div>
                        <div style="width:70%;float:left">
                            <select name="motivo_sat" id="motivo_sat"
                                    class="largeInput">
                                <option value="">---seleccionar---</option>
                                <option value="01">Comprobantes emitidos con errores con relación</option>
                                <option value="02">Comprobantes emitidos con errores sin relación</option>
                                <option value="03">No se llevó a cabo la operación</option>
                                <option value="04">Operación nominativa relacionada en una factura global</option>
                            </select>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="grid_16 cfdi-sustitucion" style="display: none">
                    <div class="formLine" style="width:100%;display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> UUID que sustituye</div>
                        <div style="width:70%;float:left">
                            <input type="text" class="largeInput"
                                   value=""
                                   id="uuid_sustitucion"
                                   name="uuid_sustitucion">
                        </div>
                    </div>
                    <hr/>
                </div>
               <div class="grid_16">
                   <div class="formLine" style="width:100%;display: inline-block">
                       <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Descripción breve</div>
                       <div style="width:70%;float:left">
                           <textarea name="motivo" id="motivo" class="largeInput"></textarea>
                       </div>
                   </div>
                   <hr/>
               </div>
            </div>
        </fieldset>
        <div class="actionPopup">
            <span class="msjRequired"><em style="color:#ff0000">*</em> Campos requeridos </span><br>
            <span class="msjRequired"><em style="color:#ff8c00">*</em>  El proceso puede llevar varios segundos. Favor de ser paciente y no dar click 2 veces en el boton.</span><br>
            <div class="actionsChild">
                <img src="{$WEB_ROOT}/images/loading.gif" style="display:none" id="loading-img"/>
            </div>
            <div class="actionsChild">
                <a href="javascript:;" id="btnCancelar" name="btnCancelar" class="button_grey">
                    <span>Cancelar</span>
                </a>
            </div>
        </div>
    </form>
</div>
