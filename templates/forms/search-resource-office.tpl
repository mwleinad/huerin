<div align="center"  id="divForm">
    <form name="frmSearch" id="frmSearch" action="export/report-cxc.php" method="post">
    <input type="hidden" name="type" id="type" value="search" />
    <input type="hidden" name="showAll" id="showAll" value="1" />
        <div class="grid_16">
            <div class="grid_16" style="background: #CCCC;">
                <h3 class="text-bold">Filtro de busqueda</h3>
            </div>
            <div class="grid_4">
                <div class="grid_16 text-left"><label for="" class="label-form">Responsable</label></div>
                <div class="grid_16">
                    <select id="responsable" name="responsable" class="largeInput">
                        <option value="">Seleccionar.....</option>
                        {foreach from=$empleados key=kem item=empleado}
                            <option value="{$empleado.name}">{$empleado.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="grid_4">
                <div class="grid_16 text-left "><label for="" class="label-form">Fecha  de alta</label></div>
                <div class="grid_16" style="display: table;border-collapse: separate;position: relative">
                    <input type="text" id="fecha_alta_inicio" name="fecha_alta_inicio" class="largeInput" onclick="CalendarioSimple(this)" style="float: left;display: table-cell!important;"/>
                    <span class="input-addon">al</span>
                    <input type="text" id="fecha_alta_fin" name="fecha_alta_fin" class="largeInput" onclick="CalendarioSimple(this)" style="float: left;display: table-cell!important;" />
                </div>
            </div>
            <div class="grid_4">
                <div class="grid_16 text-left "><label for="" class="label-form">Fecha  de compra</label></div>
                <div class="grid_16" style="display: table;border-collapse: separate;position: relative">
                    <input type="text" id="fecha_compra_inicio" name="fecha_compra_inicio" class="largeInput" onclick="CalendarioSimple(this)" style="float: left;display: table-cell!important;"/>
                    <span class="input-addon">al</span>
                    <input type="text" id="fecha_compra_fin" name="fecha_compra_fin" class="largeInput" onclick="CalendarioSimple(this)"  style="float: left;display: table-cell!important;" />
                </div>
            </div>
        </div>
        <div class="grid_16">
            <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading"/>
        </div>
        <div class="grid_16" style="text-align: center">
            <div style="display: inline-block">
                <a href="javascript:;"  id="btnSearch" class="button_grey"><span>Generar reporte</span></a>
            </div>
        </div>
    </form>
</div>
