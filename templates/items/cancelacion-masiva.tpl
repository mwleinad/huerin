<div class="portlet">
    <div class="portlet-header">
        <img src="{$WEB_ROOT}/images/icons/cancel.png" width="16" height="16" alt="Cancelación Masiva" />
        Cancelación Masiva de Comprobantes
    </div>
    <div class="portlet-content">
        
        <!-- Sección de carga de archivo -->
        <div id="seccion-upload" class="form-section">
            <h3>1. Cargar archivo Excel</h3>
            <p class="help-text">
                Sube un archivo Excel (.xlsx o .xls) que contenga los UUIDs a cancelar en la <strong>columna C</strong>.
                El archivo puede tener múltiples hojas, todas serán procesadas.
            </p>
            
            <form id="form-upload-excel" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="archivo_excel">Seleccionar archivo Excel:</label>
                    <input type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls" required>
                    <input type="hidden" name="type" value="uploadExcel">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-upload"></i> Procesar Archivo
                    </button>
                </div>
            </form>
            
            <div id="loading-upload" class="loading-indicator" style="display: none;">
                <img src="{$WEB_ROOT}/images/loading.gif" alt="Cargando..." /> 
                Procesando archivo Excel...
            </div>
        </div>

        <!-- Sección de resultados -->
        <div id="seccion-resultados" class="form-section" style="display: none;">
            <h3>2. Resultados de validación</h3>
            
            <div id="resumen-resultados" class="alert-info">
                <!-- Aquí se mostrará el resumen -->
            </div>

            <!-- Comprobantes válidos -->
            <div id="comprobantes-validos" class="table-section" style="display: none;">
                <h4>Comprobantes encontrados (activos para cancelar)</h4>
                
                <div class="table-controls">
                    <label>
                        <input type="checkbox" id="seleccionar-todos"> 
                        Seleccionar todos los comprobantes activos
                    </label>
                </div>
                
                <table id="tabla-comprobantes-validos" class="data-table">
                    <thead>
                        <tr>
                            <th width="30">Sel.</th>
                            <th>UUID</th>
                            <th>Serie-Folio</th>
                            <th>Fecha</th>
                            <th>Receptor</th>
                            <th>RFC Receptor</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se llenarán los datos -->
                    </tbody>
                </table>
                
                <div class="form-section">
                    <h4>3. Solicitar cancelación</h4>
                    <div class="form-group">
                        <label for="motivo-cancelacion">Motivo de cancelación:</label>
                        <select id="motivo-cancelacion" name="motivo">
                            <option value="01">Comprobante emitido con errores con relación</option>
                            <option value="02" selected>Comprobante emitido con errores sin relación</option>
                            <option value="03">No se llevó a cabo la operación</option>
                            <option value="04">Operación nominativa relacionada en una factura global</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="btn-solicitar-cancelacion" class="btn btn-danger">
                            <i class="icon-cancel"></i> Solicitar Cancelación de Seleccionados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Errores encontrados -->
            <div id="comprobantes-errores" class="table-section" style="display: none;">
                <h4>UUIDs con errores</h4>
                
                <table id="tabla-errores" class="data-table">
                    <thead>
                        <tr>
                            <th>Hoja</th>
                            <th>Fila</th>
                            <th>UUID</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se llenarán los errores -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de resultados de cancelación -->
        <div id="seccion-cancelacion" class="form-section" style="display: none;">
            <h3>Resultado de la cancelación</h3>
            <div id="resultado-cancelacion">
                <!-- Aquí se mostrarán los resultados -->
            </div>
        </div>

    </div>
</div>

<style>
.form-section {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.help-text {
    color: #666;
    font-style: italic;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.form-actions {
    margin-top: 15px;
}

.btn {
    padding: 8px 15px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #007cba;
    color: white;
}

.btn-danger {
    background-color: #d32f2f;
    color: white;
}

.btn:hover {
    opacity: 0.8;
}

.loading-indicator {
    text-align: center;
    padding: 20px;
    color: #666;
}

.alert-info {
    background-color: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 3px;
    padding: 15px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #e8f5e8;
    border: 1px solid #81c784;
    border-radius: 3px;
    padding: 15px;
    margin-bottom: 20px;
}

.alert-error {
    background-color: #ffebee;
    border: 1px solid #e57373;
    border-radius: 3px;
    padding: 15px;
    margin-bottom: 20px;
}

.table-section {
    margin-top: 20px;
}

.table-controls {
    margin-bottom: 10px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.data-table th,
.data-table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.data-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.data-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.status-activo {
    color: #4caf50;
    font-weight: bold;
}

.status-cancelado {
    color: #f44336;
    font-weight: bold;
}

.uuid-cell {
    font-family: monospace;
    font-size: 11px;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    
    // Manejar envío del formulario de upload
    $('#form-upload-excel').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $('#loading-upload').show();
        $('#seccion-resultados').hide();
        $('#seccion-cancelacion').hide();
        
        $.ajax({
            url: 'ajax/cancelacion-masiva.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#loading-upload').hide();
                
                if (response.success) {
                    mostrarResultados(response);
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                $('#loading-upload').hide();
                alert('Error de comunicación con el servidor');
            }
        });
    });
    
    // Función para mostrar resultados
    function mostrarResultados(data) {
        $('#seccion-resultados').show();
        
        // Mostrar resumen
        var resumen = '<strong>Resumen del procesamiento:</strong><br>';
        resumen += 'Total de UUIDs encontrados: ' + data.total_encontrados + '<br>';
        resumen += 'UUIDs válidos: ' + data.total_validados + '<br>';
        resumen += 'UUIDs con errores: ' + data.total_errores;
        
        $('#resumen-resultados').html(resumen);
        
        // Mostrar comprobantes válidos
        if (data.validados && data.validados.length > 0) {
            var comprobantesActivos = data.validados.filter(function(item) {
                return item.datos && item.datos.status === 'activo';
            });
            
            if (comprobantesActivos.length > 0) {
                $('#comprobantes-validos').show();
                var tbody = $('#tabla-comprobantes-validos tbody');
                tbody.empty();
                
                comprobantesActivos.forEach(function(item) {
                    var row = '<tr>' +
                        '<td><input type="checkbox" class="comprobante-checkbox" value="' + item.datos.comprobanteId + '"></td>' +
                        '<td class="uuid-cell">' + item.uuid + '</td>' +
                        '<td>' + item.datos.serie + '-' + item.datos.folio + '</td>' +
                        '<td>' + item.datos.fecha + '</td>' +
                        '<td>' + item.datos.nombre_receptor + '</td>' +
                        '<td>' + item.datos.rfc_receptor + '</td>' +
                        '<td>$' + parseFloat(item.datos.total).toFixed(2) + '</td>' +
                        '<td><span class="status-activo">' + item.datos.status + '</span></td>' +
                        '</tr>';
                    tbody.append(row);
                });
            }
        }
        
        // Mostrar errores
        if (data.errores && data.errores.length > 0) {
            $('#comprobantes-errores').show();
            var tbody = $('#tabla-errores tbody');
            tbody.empty();
            
            data.errores.forEach(function(item) {
                var row = '<tr>' +
                    '<td>' + item.hoja + '</td>' +
                    '<td>' + item.fila + '</td>' +
                    '<td class="uuid-cell">' + item.uuid + '</td>' +
                    '<td>' + item.error + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }
    }
    
    // Seleccionar/deseleccionar todos
    $('#seleccionar-todos').on('change', function() {
        $('.comprobante-checkbox').prop('checked', this.checked);
    });
    
    // Solicitar cancelación
    $('#btn-solicitar-cancelacion').on('click', function() {
        var seleccionados = [];
        $('.comprobante-checkbox:checked').each(function() {
            seleccionados.push($(this).val());
        });
        
        if (seleccionados.length === 0) {
            alert('Por favor selecciona al menos un comprobante para cancelar');
            return;
        }
        
        if (!confirm('¿Estás seguro de solicitar la cancelación de ' + seleccionados.length + ' comprobante(s)?')) {
            return;
        }
        
        var motivo = $('#motivo-cancelacion').val();
        
        $.ajax({
            url: 'ajax/cancelacion-masiva.php',
            type: 'POST',
            data: {
                type: 'solicitarCancelacion',
                comprobantes_ids: JSON.stringify(seleccionados),
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                $('#seccion-cancelacion').show();
                
                if (response.success) {
                    var resultado = '<div class="alert-success">';
                    resultado += '<strong>Cancelación procesada exitosamente</strong><br>';
                    resultado += 'Total procesados: ' + response.total_procesados + '<br>';
                    resultado += 'Cancelaciones solicitadas: ' + response.total_cancelados + '<br>';
                    
                    if (response.errores && response.errores.length > 0) {
                        resultado += '<br><strong>Errores:</strong><br>';
                        response.errores.forEach(function(error) {
                            resultado += '• ' + error + '<br>';
                        });
                    }
                    
                    resultado += '</div>';
                    $('#resultado-cancelacion').html(resultado);
                    
                    // Limpiar formulario
                    $('#form-upload-excel')[0].reset();
                    $('#seccion-resultados').hide();
                    
                } else {
                    $('#resultado-cancelacion').html('<div class="alert-error">Error: ' + response.error + '</div>');
                }
            },
            error: function() {
                $('#resultado-cancelacion').html('<div class="alert-error">Error de comunicación con el servidor</div>');
            }
        });
    });
});
</script>