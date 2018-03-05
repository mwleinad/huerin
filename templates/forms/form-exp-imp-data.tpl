<div align="center">
    <form name="frmFile" id="frmFile" action="" method="post">
        <input type="hidden" name="type" id="type" value="importar-datos">
        <table width="900" align="center">
            <tr style="background-color:#CCC">
                <td colspan="6" bgcolor="#CCCCCC" align="center"><b>Opciones de Exportacion e importacion</b></td>
            </tr>
            <tr>
                <td align="center">* Tipo</td>
                <td align="center">* Archivo</td>
            </tr>
            <tr>
                <td>
                    <select name="tipo-ei" id="tipo-ei" class="largeInput medium2">
                        <option value="">Seleccionar..</option>
                        <option value="imp-rsocial">Importar razones sociales</option>
                    </select>
                </td>
                <td align="center">
                    <input type="file" name="file" id="file" class="largeInput medium2">
                </td>
            </tr>
            <tr align="center">
                <td colspan="2" align="center">
                    <img src="{$WEB_ROOT}/images/loading.gif"  style="display:none" id="loading-img"/>
                    <a class="button_grey" id="btnRun"><span>Ejecutar</span></a>
                </td>
            </tr>
        </table>
</div>