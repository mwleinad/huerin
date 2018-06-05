<div align="center">
    <form name="frmFile" id="frmFile" action="" method="post">
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
                    <select name="type" id="type" class="largeInput medium2">
                        <option value="">Seleccionar..</option>
                        <option value="update-customer-contract">Actualizar cliente - razon social</option>
                        <option value="imp-data-customer-contract">Importar datos de cliente-contratos</option>
                        <option value="cancelar-uuid">Cancelar CFDI(UUID)</option>
                        <option value="test-funcion">Test funcion</option>
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