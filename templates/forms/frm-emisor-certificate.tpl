<div id="divForm">
	<form id="frmResource" name="frmResource" method="post" onsubmit="return false;" action="#"  autocomplete="off">
			<input name="rfcId" id="rfcId" type="hidden" value="{$post.rfcId}" size="50"/>
            <input name="empresaId" id="empresaId" type="hidden" value="{$post.empresaId}" size="50"/>
			<input type="hidden" id="type" name="type" value="processCertificate"/>
		<fieldset>
            <div class="container_16">
                {if $post.dataCertificate.noCertificado}
                    <div class="grid_10">
                        <div class="formLine" style=" width:100%;display: inline-block;">
                            <div style="width:100%;float:left"> Certificado</div>
                            <div style="width:100%;float: left;">
                                <select name="certificado" id="certificado" class="largeInput">
                                    {if $post.dataCertificate.noCertificado}
                                        <option value="{$post.dataCertificate.noCertificate}">{$post.dataCertificate.noCertificado}</option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="grid_6">
                        <div class="formLine" style=" width:100%;display: inline-block;">
                            <div style="width:100%;float:left"> Fecha vencimiento de certificado</div>
                            <div style="width:100%;float: left;">
                                <input name="fecha" id="fecha" type="text" size="30" readonly="readonly" value="{$post.dataCertificate.expireDate}" class="largeInput"/>
                            </div>
                        </div>
                    </div>
                    <hr>
                {/if}
                <div class="grid_6">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Ruta del certificado</div>
                        <div style="width:100%;float: left;">
                            <input type="file" name="file_certificado" id="file_certificado"  class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_6">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Ruta de la llave privada</div>
                        <div style="width:100%;float: left;">
                            <input type="file" name="file_llave" id="file_llave" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Contraseña llave privada</div>
                        <div style="width:100%;float: left;">
                            <input type="password" name="pass_llave" id="pass_llave" class="largeInput">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="grid_16">
                    <span style="float:left">* Campos Obligatorios</span>
                </div>
                <div class="grid_16" style="text-align: center">
                    <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                </div>
                <div class="grid_16" style="text-align: center">
                    <div class="formLine"  style="display: inline-block">
                        <a href="javascript:;"  id="btnEmisor" class="button_grey"><span>{if $post.dataCertificate.noCertificado}Actualizar{else}Guardar{/if}</span></a>
                    </div>
                </div>
            </div>
		</fieldset>
	</form>
</div>