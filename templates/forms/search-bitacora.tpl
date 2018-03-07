<div align="center">
    <form name="frmSearchBitacora" id="frmSearchBitacora" action="" method="post">
        <input type="hidden" name="type" id="type" value="search">
        <table width="900" align="center">
            <tr style="background-color:#CCC">
                <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td align="center">Modulo</td>
                <td align="center">Fecha inicial</td>
                <td align="center">Fecha final</td>
            </tr>
            <tr>
                <td style="width:auto; padding:0px 4px 4px 8px;" align="center">
                    <select name="modulo" id="modulo" class="largeInput medium2">
                        <option value="">Seleccionar </option>
                        <option value="servicio">Servicios</option>
                        <option value="contract">Contratos</option>
                        <option value="customer">Clientes</option>
                    </select>
                </td>
                <td style="width: auto; padding:0px 4px 4px 8px;" align="center">
                    <input type="text" name="finicial" id="finicial" class="largeInput medium2" onclick="SetDateCalendar(this)">
                </td>
                <td style="width: auto; padding:0px 4px 4px 8px;" align="center">
                    <input type="text" name="ffinal" id="ffinal" class="largeInput medium2" onclick="SetDateCalendar(this)">
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