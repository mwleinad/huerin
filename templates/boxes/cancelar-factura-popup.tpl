<div class="popupheader" style="z-index:70">
    <div id="fviewmenu" style="z-index:70">
        <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
    <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close"/></a></span>
        </div>
    </div>
    <div id="ftitl">
        <div class="flabel">&nbsp;</div>
        <div id="vtitl">
            <span title="Titulo">Cancelacion de Factura
                <br/>RFC: {$rfc} Serie y Folio: {$serie}{$folio}
            </span>
        </div>
    </div>
    <div id="draganddrop" style="position:absolute;top:45px;left:640px">
        <img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve"/>
    </div>
</div>
<div class="wrapper">
    {* if $status == 1}
        {include file="{$DOC_ROOT}/templates/forms/motivo-cancelacion.tpl"}
    {else}
        <div class="m">La factura ya fue cancelada.</div>
    {/if *}
    <div x-data="componenteCancelar()" x-init="initData({$id_comprobante})">
        <fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style="width:100%; display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Motivo de
                            cancelación SAT
                        </div>
                        <div style="width:70%;float:left">
                            <select @change="handlerSelectMotivo"
                                    x-model="current_cancelacion.clave_sat"
                                    class="largeInput">
                                <option value="">Seleccionar.......</option>
                                <template x-for="motivo in motivo_cancelacion">
                                    <option :value="motivo.clave_sat" x-text="motivo.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="grid_16" x-show="current_cancelacion.clave_sat === '01'">
                    <div class="formLine" style="width:100%;display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Origen de sustitución</div>
                        <div style="width:70%;float:left">
                            <select @change="handlerOrigenSustitucion"
                                    x-model="current_cancelacion.origen_sustitucion"
                                    class="largeInput">
                                <option value="">Seleccionar.......</option>
                                <template x-for="origen in origen_sustitucion">
                                    <option :value="origen.id" x-text="origen.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="grid_16" x-show="current_cancelacion.clave_sat === '01' && current_cancelacion.origen_sustitucion">
                    <div class="formLine" style="width:100%;display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> UUID que sustituye</div>
                        <div style="width:70%;float:left">
                            <div class="container_16">
                                <div x-show="current_cancelacion.origen_sustitucion == 2"
                                     class="grid_16"
                                     style="padding-bottom: 10px">
                                        <a href="javascript:;"
                                           x-show="current_cancelacion.origen_sustitucion == 2
                                                   && current_cancelacion.uuid_sustitucion === null
                                                   && !loading_sustituyente"
                                           @click="generarFacturaSustituyente"
                                           class="button_grey">
                                            <span>Generar factura y obtener UUID</span>
                                        </a>
                                    <img src="{$WEB_ROOT}/images/loading.gif" x-show="loading_sustituyente"/>
                                </div>
                                <div x-show="current_cancelacion.origen_sustitucion == 1 || current_cancelacion.uuid_sustitucion !== null"
                                     class="grid_16">
                                    <input  :readonly="current_cancelacion.uuid_sustitucion !== null
                                                       && current_cancelacion.origen_sustitucion == 2"
                                            x-model="current_cancelacion.uuid_sustitucion"
                                            type="text"
                                            class="largeInput">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="grid_16">
                    <div class="formLine" style="width:100%;display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Descripción breve</div>
                        <div style="width:70%;float:left">
                            <textarea x-model="current_cancelacion.motivo"
                                      class="largeInput"></textarea>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </fieldset>
        <div class="actionPopup">
            <span class="msjRequired"><em style="color:#ff0000">*</em> Campos requeridos </span><br>
            <div class="actionsChild" x-show="loading">
                <img src="{$WEB_ROOT}/images/loading.gif" id="loading-img"/>
            </div>
            <div class="actionsChild"
                 x-show="show_btn">
                <a href="javascript:;"
                   @click="cancelarFactura"
                   class="button_grey">
                    <span>Cancelar</span>
                </a>
            </div>
        </div>
    </div>
</div>
