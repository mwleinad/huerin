<div id="divForm">
<form name="frmDownResource" id="frmDownResource" method="post" action="" onsubmit="return false;">
    <input type="hidden" name="type" value="downResource" />
    <input type="hidden" name="office_resource_id" value="{$post.office_resource_id}" />
    <fieldset>
        <div class="grid_16">
            <div class="formLine" style="width:100%;  display: inline-block;">
                <div style="width:100%;float:left"> * Motivo por la que se dara de baja</div>
                <div style="width:100%;float: left;">
                    <textarea name="motivo_baja" id="motivo_baja" class="largeInput " rows="15"></textarea>
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
                <a href="javascript:void();"  id="btnResource" class="button_grey"><span>Guardar</span></a>
            </div>
        </div>
    </fieldset>
</form>
</div>
