<!-- Form -->
     
     <div class="m">
		<form name="frmEditarFolios" id="frmEditarFolios" method="post" action="">
		<fieldset>
			<div class="a">
            	<div class="l">Serie</div>
                <div class="r"><input type="text" name="serie" id="serie" value="{$info.serie}" class="largeInput wide2"></div>
            </div>
			<div class="a">
            	<div class="l">Folio Inicial *</div>
                <div class="r"><input type="text" name="folio_inicial" id="folio_inicial" value="{$info.folioInicial}" class="largeInput wide2" /></div> 
            </div>
            <div class="a">
            	<div class="l">Folio Final *</div>
                <div class="r"><input type="text" name="folio_final" id="folio_final" value="{$info.folioFinal}" class="largeInput wide2" /></div>
            </div>

						<div class="a">
            	<div class="l">Comprobante *</div>
                <div class="r">
                		<select name="comprobante" id="comprobante" class="largeInput wide2">
                		<option value="">Seleccione</option>
                        {foreach from=$comprobantes item=com key=key}
                        <option value="{$com.tiposComprobanteId}" {if $info.tiposComprobanteId == $com.tiposComprobanteId} selected="selected" {/if}>{$com.nombre}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="a">
            	<div class="l">Lugar de expedicion *</div>
                <div class="r">
                	<select name="lugar_expedicion" id="lugar_expedicion" class="largeInput wide2">
                		<option value="">Seleccione</option>
                        {foreach from=$sucursales item=suc key=key}
                        <option value="{$suc.sucursalId}" {if $info.lugarDeExpedicion == $suc.sucursalId} selected="selected" {/if}>{$suc.identificador}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="a">
            	<div class="l">Numero de certificado *</div>
                <div class="r">
                	<select name="no_certificado" id="no_certificado" class="largeInput wide2">
                		<option value="">Seleccione</option>
                        <option value="{$nom_certificado}" {if $info.noCertificado == $nom_certificado} selected {/if}>{$nom_certificado}</option>
                    </select>
                </div>
            </div>
            <div class="a">
            	<div class="l">Email de aviso *</div>
                <div class="r"><input type="text" name="email" id="email"  value="{$info.email}" class="largeInput wide2"></div>
            </div>           	
			<div class="a">
            	<div class="l">&nbsp;</div>
                <div class="r">
                <a class="button" id="btnEditarFolios"><span>Guardar Folios</span></a></div>
            </div>
             <div class="a">
            	<div class="l">* Campos requeridos</div>               
            </div>
            <div class="a">
            	<div id="txtMsg"></div>
            </div>	
			<div class="a"></div>
		</fieldset>
        <input type="hidden" name="id_serie" value="{$info.serieId}" />
		</form>
	</div>
     
<!-- End Form -->