<div align="center">
    <form name="frmSearchRazon" id="frmSearchRazon" method="post" onsubmit="return false">
        <input type="hidden" name="type" id="type" value="generate_report_razon_social">
        <input type="hidden" name="cliente" id="cliente" value="0" />
        <input type="hidden" name="type_report" id="type_report" value="complete_report_cc" />
        <table width="100%" align="center">
            <tr style="background-color:#CCC">
                <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td style="text-align: center;width: 20%">Cliente</td>
                <td style="text-align: center;width: 20%">Encargado</td>
                <td style="text-align: center;width: 10 %">Incluir subordinados</td>
                <td style="text-align: center;width: 10%">Status</td>
                <td style="text-align: center;width: 10%">Generan factura del mes 13</td>
            </tr>
            <tr>
                <td style="text-align: center;width: 20%; padding:0px 4px 4px 8px;">
                    <input type="text" size="25" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
                    <div id="loadingDivDatosFactura"></div>
                    <div style="position:relative">
                        <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                        </div>
                    </div>
                </td>
                <td style="width: 20%; padding:0px 4px 4px 8px;" align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <input type="checkbox" name="deep" id="deep" class="largeInput"/>
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                  <select class="largeInput" name="tipos" id="tipos">
                      <option value="">Todos</option>
                      <option value="activos">Activos</option>
                      <option value="temporal">Bajas temporales</option>
                      <option value="inactivos">Inactivos</option>
                  </select>
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <select class="largeInput" name="factura13" id="factura13">
                        <option value="">Todos</option>
                        <option value="si">Si</option>
                        <option value="no">No</option>
                    </select>
                </td>
            </tr>
            <tr align="center">
                <td colspan="5" align="center">
                    <div style="display:inline-block;text-align: center;">
                        <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                        <input type="submit" class="button_grey"  id="btnSearch" value="Buscar">
                    </div>

                </td>
            </tr>

        </table>
</div>