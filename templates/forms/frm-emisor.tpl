<div id="divForm">
	<form id="frmResource" name="frmResource" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		{if $post}
			<input name="rfcId" id="rfcId" type="hidden" value="{$post.rfcId}" size="50"/>
			<input type="hidden" id="type" name="type" value="updateEmisor"/>
		{else}
			<input type="hidden" id="type" name="type" value="saveEmisor"/>
		{/if}
		<fieldset>
            <div class="container_16">
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * Nombre Razon o razon social</div>
                        <div style="width:70%;float: left;">
                            <input type="text" name="razonSocial" id="razonSocial" value="{$post.razonSocial}" class="largeInput">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_16">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:30%;float:left"> * RFC</div>
                        <div style="width:70%;float: left;">
                            <input type="text" name="rfc" id="rfc" value="{$post.rfc}" class="largeInput">
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="grid_10">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Calle</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="calle" id="calle" value="{$post.calle}" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_3">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">  No exterior</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="noExt" id="noExt" value="{$post.noExt}" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_3">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> No interior</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="noInt" id="noInt" value="{$post.noInt}" class="largeInput">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Codigo postal</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="cp" id="cp" value="{$post.cp}" class="largeInput">
                        </div>
                    </div>
                </div>

                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> Colonia</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="colonia" id="colonia" value="{$post.colonia}" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> Ciudad</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="ciudad" id="ciudad" value="{$post.ciudad}" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> Estado</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="estado" id="estado" value="{$post.estado}" class="largeInput">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left"> Pais</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="pais" id="pais" value="{$post.pais}" class="largeInput">
                        </div>
                    </div>
                </div>
                <div class="grid_4">
                    <div class="formLine" style=" width:100%;display: inline-block;">
                        <div style="width:100%;float:left">* Clave facturador</div>
                        <div style="width:100%;float: left;">
                            <input type="text" name="claveFacturador" id="claveFacturador" value="{$post.claveFacturador}" {if $post}readonly{/if} class="largeInput">
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
                        <a href="javascript:;"  id="btnEmisor" class="button_grey"><span>{if $post}Actualizar{else}Guardar{/if}</span></a>
                    </div>
                </div>
            </div>
		</fieldset>
	</form>
</div>
