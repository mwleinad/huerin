<div align="center">
    <form name="frmSearchFromXml" id="frmSearchFromXml" action="" method="post">
        <input type="hidden" name="type" id="type" value="searchFacturaFromXml">
        <table width="100%" align="center">
            <tr style="background-color:#CCC">
                <td colspan="5" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td style="text-align: center;width: 10%">Folio de.</td>
                <td style="text-align: center;width: 10%">Folio a.</td>
                <td style="text-align: center;width: 40%">Razon social</td>
                <td style="text-align: center;width: 10%">A&ntilde;o</td>
                <td style="text-align: center;width: 10%">Mes</td>
                <td style="text-align: center;width: 10%">Ordenar por</td>
            </tr>
            <tr>
                <td style="text-align: center;width: 10%; padding:0px 4px 4px 8px;">
                    <input type="text"  name="finicial" id="finicial" class="largeInput" autocomplete="off" value="" />
                </td>
                <td style="text-align: center;width: 10%; padding:0px 4px 4px 8px;">
                    <input type="text" name="ffinal" id="ffinal" class="largeInput" autocomplete="off" value="" />
                </td>
                <td style="width: 30%; padding:0px 4px 4px 8px;" align="center">
                    <input type="text" size="25" name="rfc2" id="rfc2" class="largeInput" autocomplete="off" value="{$search.rfc}" />
                    <div id="loadingDivDatosFactura2"></div>
                    <div style="position:relative">
                        <div style="display:none;position:absolute;top:-2px; left:2px; z-index:100" id="suggestionDiv2">
                        </div>
                    </div>
                </td>
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
                    <select name="orderby" id="orderby" class="largeInput medium2">
                        <option value="fecha">Fecha</option>
                        <option value="folio">Folio</option>
                        <option value="receptorName">Nombre</option>
                    </select>
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