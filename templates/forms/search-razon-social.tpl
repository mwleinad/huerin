<div align="center">
    <form name="frmSearchRazon" id="frmSearchRazon" action="{$WEB_ROOT}/export/rsocial.php" method="post">
        <input type="hidden" name="type" id="type" value="getRazonSocial">
        <input type="hidden" name="cliente" id="cliente" value="0" />
        <table width="80%" align="center">
            <tr style="background-color:#CCC">
                <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td style="text-align: center;width: 30%">Cliente</td>
                <td style="text-align: center;width: 30%">Encargado</td>
                <td style="text-align: center;width: 10 %">Incluir subordinados</td>
                <td style="text-align: center;width: 10%">Status</td>
            </tr>
            <tr>
                <td style="text-align: center;width: 30%; padding:0px 4px 4px 8px;">
                    <input type="text" size="25" name="rfc" id="rfc" class="largeInput" autocomplete="off" value="{$search.rfc}" />
                    <div id="loadingDivDatosFactura"></div>
                    <div style="position:relative">
                        <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv">
                        </div>
                    </div>
                </td>
                <td style="width: 30%; padding:0px 4px 4px 8px;" align="center">
                    {include file="{$DOC_ROOT}/templates/forms/comp-filter-personal.tpl"}
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                    <input type="checkbox" name="deep" id="deep" class="largeInput"/>
                </td>
                <td style="width: 10%; padding:0px 4px 4px 8px;" align="center">
                  <select class="largeInput" name="tipos" id="tipos">
                      <option value="">Todos</option>
                      <option value="activos">Activos</option>
                      <option value="inactivos">Inactivos</option>
                  </select>
                </td>

            </tr>
            <tr align="center">
                <td colspan="5" align="center">
                    <div style="display:inline-block;text-align: center;">
                        <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                        <input type="submit" class="button_grey"  id="btnSearch" value="Buscar">
                        {*<submit class="button_grey" id="btnSearch"><span>Buscar</span></submit>*}
                    </div>

                </td>
            </tr>

        </table>
</div>