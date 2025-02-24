<div class="grid_16" id="content">

    <div class="grid_9">
        <h1 class="catalogos">Organigrama</h1>
    </div>
    <div class="grid_6" id="eventbox">
    </div>
    <div class="clear">
    </div>
    <div id="portlets">
        <div class="clear"></div>
        <div class="portlet">
            <div class="grid_6">
                <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                <a style="font-size: 16px;" href="javascript:;" onclick="descargarOrganigrama()" id="btnDescargar" type="button" title="Descargar reporte" target="_self">
                    <img style="display: block;" src="{$WEB_ROOT}/images/excel.PNG" width="32">Click para generar reporte</a>
            </div>
            <div class="grid_6">
                <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img-2"/>
                <a style="font-size: 16px; display: block" href="javascript:;" onclick="descargarOrganigramaV2()" id="btnDescargarV2" type="button" title="Descargar formato para plataforma 2.0" target="_self">
                    <img style="display: block" src="{$WEB_ROOT}/images/excel.PNG" width="32" />Descargar formato para plataforma 2.0</a>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
</div>