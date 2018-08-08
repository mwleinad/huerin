<div align="center">
    <form name="frmCobranzaMensual" id="frmCobranzaMensual" action="" method="post">
        <input type="hidden" name="type" id="type" value="searchCobranzaMensual">
        <table width="900" align="center">
            <tr style="background-color:#CCC">
                <td colspan="7" bgcolor="#CCCCCC" align="center"><b>Filtro de busqueda</b></td>
            </tr>
            <tr>
                <td align="center">Empresa</td>
                <td align="center">Mes</td>
                <td align="center">Año</td>

            </tr>
            <tr>
                <td style="width: 20%; padding:0px 4px 4px 8px;" align="center">
                    <select name="empresaId" id="empresaId" class="largeInput medium2">
                        {foreach from=$empresas item=item key=key}
                            <option value="{$item.empresaId}">{$item.razonSocial}</option>
                        {/foreach}
                    </select>
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <select name="mes" id="mes" class="largeInput medium2">
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
                </td>
            </tr>
            <tr align="center">
                <td colspan="5" align="center">
                    <div style="display:inline-block;text-align: center;">
                        <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                        <a class="button_grey" id="btnSearch"><span>Buscar</span></a>
                    </div>

                </td>
            </tr>
        </table>
</div>