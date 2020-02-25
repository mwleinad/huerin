<div id="divForm">
    {if $rfc.dataCertificate.noCertificado}
		<form name="frmAgregarFolios" id="frmAgregarFolios" method="post" action="">
            <input name="rfcId" id="rfcId" type="hidden" value="{$rfc.rfcId}" size="50"/>
            {if $post}
                <input name="serieId" id="seridId" type="hidden" value="{$post.serieId}" size="50"/>
                <input type="hidden" id="type" name="type" value="updateFolios"/>
            {else}
                <input type="hidden" id="type" name="type" value="saveFolios"/>
            {/if}
		<fieldset>
            <div class="container">
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Serie</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="serie" id="serie" value="{$post.serie}" {if $post}readonly{/if} class="largeInput wide2">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_8">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Folio inicial</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="folio_inicial" id="folio_inicial" value="{$post.folioInicial}" class="largeInput wide2">
                        </div>
                    </div>
                </div>
                <div class="grid_8">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Folio final</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="folio_final" id="folio_final" value="{$post.folioFinal}" class="largeInput wide2">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Tipo de comprobante</div>
                        <div style="width:100%;float: left;">
                            <select name="tiposComprobanteId" id="tiposComprobanteId"  class="largeInput wide2">
                                <option value="">Seleccione</option>
                                {foreach from=$tiposComprobantes item=com key=key}
                                    <option value="{$com.tiposComprobanteId}" {if $com.tiposComprobanteId eq $post.tiposComprobanteId}selected{/if}>{$com.nombre}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Lugar de  expedicion</div>
                        <div style="width:100%;float: left;">
                            <select name="lugar_expedicion" id="lugar_expedicion"  class="largeInput wide2">
                                <option value="">Seleccione</option>
                                {foreach from=$sucursales item=suc key=key}
                                    <option value="{$suc.sucursalId}" {if $suc.sucursalId eq $post.sucursalId}selected{/if}>{$suc.identificador}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Numero de certificado</div>
                        <div style="width:100%;float: left;">
                            <select name="no_certificado" id="no_certificado"  class="largeInput wide2">
                                <option value="{$rfc.dataCertificate.noCertificado}">{$rfc.dataCertificate.noCertificado}</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> * Email de aviso</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="email" id="email"  class="largeInput wide2" value="{$post.email}">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> Adjuntar logo</div>
                        <div style="width:100%;float: left;">
                            <input type="file" name="file_logo" id="file_logo"  class="largeInput wide2">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <span style="float:left">* Campos Obligatorios</span>
                </div>
                <div class="grid_16" style="text-align: center">
                    <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                </div>
                <div class="grid_16" style="text-align: center">
                    <div class="formLine"  style="display: inline-block">
                        <a href="javascript:;"  id="btnFolios" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                    </div>
                </div>
            </div>
		</fieldset>
		</form>
    {else}
        <span style="color: red">No se encontro un certificado valido para poder dar de alta folios</span>
    {/if}
</div>     
<!-- End Form -->