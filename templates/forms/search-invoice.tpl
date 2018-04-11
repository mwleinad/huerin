<div align="center">
    <form name="frmSearchInvoice" id="frmSearchInvoice" action="" method="post">
        <input type="hidden" name="type" id="type" value="search">
        <table width="900" align="center">
            <tr style="background-color:#CCC">
                <td colspan="7" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td align="center">A&ntilde;o</td>
                <td align="center">Mes</td>
                <td align="center">Serie</td>
                <td align="center">Cliente/Razon social</td>
                <td align="center">Status</td>
                <td align="center">Generado</td>
                <td align="center">Incluir complementos</td>
            </tr>
            <tr>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-year.tpl"}
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <select name="mes" id="mes" class="largeInput medium2">
                        <option value="">Selec..</option>
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
                    <select name="serie" id="serie" class="largeInput medium2">
                        <option value="">Selecionar</option>
                        {foreach from=$series key=key item=item}
                          <option value="{$item.serie}">{$item.serie}</option>
                        {/foreach}
                    </select>
                </td>
                <td style="width: 35%; padding:0px 4px 4px 8px;" align="center">
                    <input type="text" name="name" id="name" class="largeInput medium2">
                    <input type="hidden" name="rfc" id="rfc" class="largeInput medium2">
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <select name="status" id="status" class="largeInput medium2">
                        <option value="">Todos</option>
                        <option value="1">Activos</option>
                        <option value="0">Cancelados</option>
                    </select>
                </td>
                <td style="width: 15%; padding:0px 4px 4px 8px;" align="center">
                    <select name="generateby" id="generateby" class="largeInput medium2">
                        <option value="">Todos</option>
                        <option value="automatico">Automatico</option>
                        <option value="manual">Manuales</option>
                    </select>
                </td>
                <td style="width: 15%; padding:0px 4px 4px 8px;" align="center">
                    <input type="checkbox" name="addComplemento" id="addComplemento" class="largeInput medium2" />

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