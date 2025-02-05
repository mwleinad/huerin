<div class="popupheader" style="z-index:70">
    <div id="fviewmenu" style="z-index:70">
        <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
    <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close"/></a></span>
        </div>
    </div>
    <div id="ftitl">
        <div class="flabel">&nbsp;</div>
        <div id="vtitl">
            <span title="Titulo">Envio de Factura</span>
            <span style="display: block; font-size: 14px;">Nombre: {$nombre} </span>
            <span style="display: block; font-size: 14px;">RFC: {$rfc}</span>
            <span style="display: block;font-size: 14px;">Serie y Folio: {$serie}{$folio}</span>
        </div>
    </div>
    <div id="draganddrop" style="position:absolute;top:45px;left:640px">
        <img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve"/>
    </div>
</div>
<div class="wrapper">
    <div x-data="componenteEnviarCorreo()" x-init="initData('{$comprobanteId}','{$responsableCxc.name}','{$responsableCxc.email}','{$correosReceptor}')">
        <fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style="width:100%; display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Seleccione el tipo de destinatario
                        </div>
                        <div style="width:70%;float:left">
                            <select @change="handlerSelectTipoDestinatario"
                                    x-model="tipo_destinatario"
                                    class="largeInput">

                                <template x-for="tipo in tipos_destinatario">
                                    <option :value="tipo" x-text="tipo"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="grid_16">
                    <div class="formLine" style="width:100%;display: inline-block">
                        <div style="width:30%;float:left; font-weight: bold"><em style="color:#ff0000">*</em> Correo</div>
                        <div style="width:70%;float:left">
                            <input x-model="correo_destinatario"
                                   readonly="readonly"
                                   class="largeInput"/>
                            <small style="display: block; font-size: 12px">Si el campo esta vacio o el correo es incorrecto, es necesario corregir desde el apartado de Datos de la empresa.</small>
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
                 x-show="correo_destinatario!='' && !loading">
                <a href="javascript:;"
                   @click="enviarCorreo"
                   class="button_grey">
                    <span>Enviar</span>
                </a>
            </div>
        </div>
    </div>
</div>
