<div align="center">
    <form name="frmFile" id="frmFile" action="" method="post">
        <table width="80%" align="center">
            <tr style="background-color:#CCC">
                <td colspan="2" bgcolor="#CCCCCC" align="center"><b>Opciones de Exportacion e importacion</b></td>
            </tr>
            <tr>
                <td style = "width: 20%" align="center">* Accion a ejecutar</td>
                <td style = "width: 20%" align="center">* Archivo</td>
            </tr>
            <tr>
                <td>
                    <select name="type" id="type" class="largeInput medium2">
                        <option value="">Seleccionar..</option>
                        <option value="update#contract">Actualizar razones sociales / contratos</option>
                        <option value="update#customer">Actualizar clientes principales</option>
                        <option value="add#contract">Importar nuevas razones sociales / contratos</option>
                        <option value="add#customer">Importar nuevos clientes principales</option>
                        <option value="importar_servicios_nuevos">Importar nuevos servicios</option>
                        <option value="update-servicios">Actualizar servicios de clientes</option>
                        <!--option value="imp-new-customer">Importar clientes nuevos</--option>
                        <option value="imp-new-contract">Importar razones sociales nuevos</option>
                        <option value="update-only-encargado">Actualizar encargados de area</option>

                        -->
                        {if $User.isRoot}
                            <option value="doPermiso">Reconstruir permisos</option>
                            <option value="update_comercial_activity">Importar nuevas actividades comerciales</option>
                            <!--option value="importar_customer_rebuild">Importar clientes rebuild</option>
                            <option value="importar_contrato_rebuild">Importar contratos rebuild</option>
                            <option value="importar_empleados_rebuild">Importar empleados rebuild</option>
                            <option value="importar_servicios_rebuild">Importar servicios a contratos rebuild</option>
                            <option value="importar_servicios_nominas">Importar servicios nominas</option>
                            <option value="update_direccion_fiscal">Actualizar direccion fiscal de clientes</option>
                            <option value="update_extensiones_tasks">Actualizar extensiones en tareas</option-->
                        {/if}

                        <!--<option value="cancelar-uuid">Cancelar CFDI(UUID)</option>
                        <option value="cancelar-uuid">Cancelar CFDI(UUID)</option>
                        <option value="test-funcion">Test funcion</option>-->
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
