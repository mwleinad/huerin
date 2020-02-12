<div id="divForm">
<form name="frmDownResponsable" id="frmDownResponsable" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="type" value="saveBajaResponsable" />
    <input type="hidden" name="office_resource_id" value="{$post.office_resource_id}" />
    <input type="hidden" name="rs_id" value="{$post.responsable_resource_id}" />
    <fieldset>
        <div class="container_16">
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:100%;float:left"> * Motivo de baja</div>
                    <div style="width:100%;float: left;">
                        <textarea name="motivo_baja" id="motivo_baja" class="largeInput " rows="5"></textarea>
                    </div>
                </div>
                <hr>
            </div>
            <div class="grid_16">
                <div class="formLine" style="width:100%;  display: inline-block;">
                    <div style="width:30%;float:left"> * Adjuntar responsiva de baja(PDF Max 2MB)</div>
                    <div style="width:68%;float:left">
                        <input name="responsiva"  id="responsiva"   type="file" class="largeInput"/>
                    </div>
                </div>
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
                    <a href="javascript:void();"  id="btnResponsable" class="button_grey"><span>Guardar</span></a>
                </div>
            </div>
        </div>
    </fieldset>
</form>
</div>
