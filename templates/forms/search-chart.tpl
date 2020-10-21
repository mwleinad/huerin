<div align="center">
    <form name="frmSearchChart" id="frmSearchChart" action="" method="post">
        <input type="hidden" name="type" id="type" value="generateChart">
        <table width="900" align="center">
            <tr style="background-color:#CCC">
                <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Opciones de busqueda</b></td>
            </tr>
            <tr>
                <td align="center">Tipo de grafica</td>
            </tr>
            <tr>
                <td style="width:auto; padding:0px 4px 4px 8px;" align="center">
                    <select name="typeChart" id="typeChart" class="largeInput medium2">
                        <option value="">Todos</option>
                        <option value="altas_bajas">Altas y bajas de empresas mensual</option>
                        <option value="status_company">Estatus de empresas</option>
                        <option value="type_person">Tipo de personas</option>
                        <option value="month_13">Empresas que generan factura mes 13</option>
                    </select>
                </td>
            </tr>
            <tr align="center">
                <td  align="center">
                    <div style="display:inline-block;text-align: center;">
                        <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                        <a class="button_grey" id="btnSearch"><span>Buscar</span></a>
                    </div>
                </td>
            </tr>
        </table>
</div>
