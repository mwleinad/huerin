<div class="popupheader" style="z-index:70">
    <div id="fviewmenu" style="z-index:70">
        <div id="fviewclose"><span style="color:#CCC" id="closePopUpDiv">
    <a href="javascript:void(0)">Close<img src="{$WEB_ROOT}/images/b_disn.png" border="0" alt="close"/></a></span>
        </div>
    </div>
    <div id="ftitl">
        <div class="flabel">&nbsp;</div>
        <div id="vtitl">
            <span title="Titulo">{$data.title}
                <br/>Cancelación masiva mediante archivo Excel
            </span>
        </div>
    </div>
    <div id="draganddrop" style="position:absolute;top:45px;left:640px">
        <img src="{$WEB_ROOT}/images/draganddrop.png" border="0" alt="mueve"/>
    </div>
</div>
<div class="wrapper">
    <!-- Sección de carga de archivo -->
    <div id="seccion-upload-excel">
        <form name="frm-cancel-cfdi-from-excel" id="frm-cancel-cfdi-from-excel" enctype="multipart/form-data">
            <fieldset>
                <div class="container_16">
                    <div class="grid_16">
                        <div class="formLine" style="width:100%; display: inline-block; margin-bottom: 20px;">
                            <div class="help-text-box">
                                <strong>Instrucciones:</strong><br>
                                • Sube un archivo Excel (.xlsx o .xls)<br>
                                • Los UUIDs deben estar en la <strong>columna C</strong><br>
                                • El archivo puede tener múltiples hojas<br>
                                • Solo se procesarán las celdas que contengan UUIDs válidos
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <div class="grid_16">
                        <div class="formLine" style="width:100%; display: inline-block">
                            <div style="width:30%;float:left; font-weight: bold">
                                <em style="color:#ff0000">*</em> Seleccionar archivo Excel:
                            </div>
                            <div style="width:70%;float:left">
                                <input type="file" 
                                       name="archivo_excel" 
                                       id="archivo_excel" 
                                       accept=".xlsx,.xls" 
                                       class="largeInput" 
                                       required>
                                <input type="hidden" name="type" value="uploadExcel">
                            </div>
                        </div>
                        <hr/>
                    </div>
                    <div class="grid_16" id="loading-excel" style="display: none; margin-top: 15px; margin-bottom: 15px;">
                        <div class="formLine" style="width:100%; display: inline-block; text-align: center;">
                            <img src="{$WEB_ROOT}/images/loading.gif" alt="Cargando..." />
                            <br>Procesando archivo Excel...
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="actionPopup">
                <span class="msjRequired"><em style="color:#ff0000">*</em> Campos requeridos </span><br>
                <div class="actionsChild">
                    <a href="javascript:;" id="btnUploadExcel" class="button_grey">
                        <span>Procesar archivo</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Sección de resultados del Excel -->
    <div id="seccion-resultados-excel" style="display: none;">
        <fieldset>
            <div class="container_16">
                <div class="grid_16" id="resumen-procesamiento">
                    <!-- Aquí se mostrará el resumen -->
                </div>
                <div class="grid_16" id="tabla-comprobantes-encontrados" style="display: none;">
                    <!-- Aquí se mostrará la tabla de comprobantes -->
                </div>
                <div class="grid_16" id="tabla-errores-encontrados" style="display: none;">
                    <!-- Aquí se mostrarán los errores -->
                </div>
            </div>
        </fieldset>
        <div class="actionPopup">
            <div class="actionsChild" id="loading-cancelacion-excel" style="display: none;">
                <img src="{$WEB_ROOT}/images/loading.gif" alt="Cargando..." />
                <br>Procesando cancelaciones...
            </div>
            <div class="actionsChild" id="botones-resultados">
                <a href="javascript:;" id="btnSolicitarCancelacionExcel" class="button_grey" style="display: none;">
                    <span>Solicitar cancelación de seleccionados</span>
                </a>
                <a href="javascript:;" id="btnNuevoProcesamiento" class="button_grey">
                    <span>Procesar nuevo archivo</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.help-text-box {
    background-color: #f0f8ff;
    padding: 15px;
    border-left: 4px solid #2196f3;
    font-size: 14px;
    line-height: 1.4;
    border-radius: 4px;
}

input[type="file"].largeInput {
    width: 100%;
    padding: 8px;
    border: 2px dashed #ccc;
    border-radius: 4px;
    background-color: #fafafa;
    font-size: 13px;
}

input[type="file"].largeInput:focus {
    border-color: #2196f3;
    outline: none;
}

/* Estilos para modal más grande específico para Excel */
#fview.modal-excel-large {
    width: 90% !important;
    max-width: 1200px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    margin-left: 0 !important;
}

#fview.modal-excel-large .wrapper {
    min-width: 900px;
}

/* Ajustar popupheader al tamaño del modal grande */
#fview.modal-excel-large .popupheader {
    width: 100% !important;
    min-width: 900px !important;
}

#fview.modal-excel-large #ftitl {
    width: calc(100% - 100px) !important;
}

#fview.modal-excel-large #vtitl {
    width: 100% !important;
}

#fview.modal-excel-large #draganddrop {
    right: 10px !important;
    left: auto !important;
}

.resumen-box {
    background-color: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    color: #0d47a1;
}

.tabla-resultados {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 12px;
}

.tabla-resultados th,
.tabla-resultados td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.tabla-resultados th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.tabla-resultados tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.uuid-cell {
    font-family: 'Courier New', monospace;
    font-size: 10px;
    word-break: break-all;
}

.error-text {
    color: #d32f2f;
    font-size: 11px;
}

.status-activo {
    color: #4caf50;
    font-weight: bold;
}

.tabla-container {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 10px;
}

.checkbox-container {
    text-align: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
}
</style>

