<div id="divForm">
	<form id="frmImportarResource" name="frmImportarResource" method="post" onsubmit="return false;" action="#"  autocomplete="off">
		<fieldset>
			<div class="grid_16">
				<div style="text-align: right">
					<div class="formLine"  style="display: inline-block">
						<a href="javascript:;" class="button_grey" id="btn-descargar-layout"><span>Descargar layout</span></a>
						<a class="button_grey noShow" id="loading-layout"> <span>Descargando espere un momento...</span></a>
					</div>
				</div>
				<hr>
			</div>
			<div class="grid_16">
				<div class="formLine" style="width:100%;  display: inline-block;">
					<div style="width:30%;float:left"> * Archivo</div>
					<div style="width:70%;float: left;">
						<input type="file" name="file" id="file" class="largeInput"  />
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
                    <a href="javascript:;"  id="btn-importar-resource" class="button_grey"><span>Importar</a>
                </div>
            </div>
		</fieldset>
	</form>
</div>
