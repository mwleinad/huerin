<div class="popup-content">
    <h3>Resultados del procesamiento Excel</h3>
    
    <div class="resumen-section">
        <div class="alert alert-info">
            <strong>Resumen:</strong><br>
            • Total de UUIDs encontrados: <strong>{$total_encontrados}</strong><br>
            • UUIDs válidos: <strong>{$total_validados}</strong><br>
            • Comprobantes activos (cancelables): <strong>{$total_activos}</strong><br>
            • UUIDs con errores: <strong>{$total_errores}</strong>
        </div>
    </div>

    {if $validados && count($validados) > 0}
    <div class="comprobantes-section">
        <h4>Todos los comprobantes encontrados ({count($validados)})</h4>
        
        <div class="table-controls">
            <label>
                <input type="checkbox" id="seleccionar-todos-modal"> 
                Seleccionar todos los comprobantes activos
            </label>
        </div>
        
        <div class="table-container">
            <table class="result-table">
                <thead>
                    <tr>
                        <th width="30">Sel.</th>
                        <th>UUID</th>
                        <th>Serie-Folio</th>
                        <th>Fecha</th>
                        <th>Receptor</th>
                        <th>Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$validados item=comprobante}
                    <tr class="{if $comprobante.datos.status != 'activo' && $comprobante.datos.status != '1'}row-inactive{/if}">
                        <td>
                            {if $comprobante.datos.status == '1' || $comprobante.datos.status == 'activo'}
                                <input type="checkbox" class="comprobante-checkbox-modal" 
                                       value="{$comprobante.datos.comprobanteId}">
                            {else}
                                <span title="Solo se pueden cancelar comprobantes activos">-</span>
                            {/if}
                        </td>
                        <td class="uuid-cell">{$comprobante.uuid}</td>
                        <td>{$comprobante.datos.serie}-{$comprobante.datos.folio}</td>
                        <td>{$comprobante.datos.fecha}</td>
                        <td>{$comprobante.datos.nombre_receptor}</td>
                        <td>${$comprobante.datos.total|number_format:2}</td>
                        <td>
                            {if $comprobante.datos.status == '1' || $comprobante.datos.status == 'activo'}
                                <span class="status-activo">Activo</span>
                            {elseif $comprobante.datos.status == '0' || $comprobante.datos.status == 'cancelado'}
                                <span class="status-cancelado">Cancelado</span>
                            {else}
                                <span class="status-inactivo">Inactivo</span>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        
        <div class="cancelacion-section">
            <div class="form-group">
                <label for="motivo-cancelacion-modal">Motivo de cancelación:</label>
                <select id="motivo-cancelacion-modal" name="motivo">
                    <option value="01">Comprobante emitido con errores con relación</option>
                    <option value="02" selected>Comprobante emitido con errores sin relación</option>
                    <option value="03">No se llevó a cabo la operación</option>
                    <option value="04">Operación nominativa relacionada en una factura global</option>
                </select>
            </div>
        </div>
    </div>
    {/if}

    {if $errores && count($errores) > 0}
    <div class="errores-section">
        <h4>UUIDs con errores ({count($errores)})</h4>
        <div class="error-container">
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Hoja</th>
                        <th>Fila</th>
                        <th>UUID</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$errores item=error}
                    <tr>
                        <td>{$error.hoja}</td>
                        <td>{$error.fila}</td>
                        <td class="uuid-cell">{$error.uuid}</td>
                        <td class="error-text">{$error.error}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {/if}
    
    <div class="form-actions">
        {if $validados && count($validados) > 0}
        <button type="button" id="btnSolicitarCancelacionModal" class="btn btn-danger">
            Solicitar cancelación de seleccionados
        </button>
        {/if}
        <button type="button" onclick="grayOut(false);" class="btn btn-secondary">
            Cerrar
        </button>
    </div>
    
    <div id="loading-cancelacion-modal" class="loading-indicator" style="display: none;">
        <img src="{$WEB_ROOT}/images/loading.gif" alt="Cargando..." />
        Procesando solicitudes de cancelación...
    </div>
</div>

<style>
.popup-content {
    padding: 20px;
    max-width: 900px;
    max-height: 80vh;
    overflow-y: auto;
}

.resumen-section {
    margin-bottom: 25px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.alert-info {
    background-color: #e3f2fd;
    border: 1px solid #90caf9;
    color: #0d47a1;
}

.comprobantes-section,
.errores-section {
    margin-bottom: 25px;
}

.comprobantes-section h4,
.errores-section h4 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid #eee;
}

.table-controls {
    margin-bottom: 10px;
}

.table-container,
.error-container {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.result-table {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
    margin: 0;
}

.result-table th,
.result-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 12px;
}

.result-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    position: sticky;
    top: 0;
    z-index: 1;
}

.result-table tbody tr:hover {
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

.cancelacion-section {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.form-actions {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 10px 20px;
    margin: 0 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}

.btn-danger {
    background-color: #d32f2f;
    color: white;
}

.btn-secondary {
    background-color: #757575;
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

.status-activo {
    color: #4caf50;
    font-weight: bold;
}

.status-cancelado {
    color: #f44336;
    font-weight: bold;
}

.status-inactivo {
    color: #9e9e9e;
    font-weight: bold;
}

.row-inactive {
    background-color: #f5f5f5 !important;
    color: #666;
}

.row-inactive .uuid-cell,
.row-inactive td {
    opacity: 0.7;
}
</style>

<script type="text/javascript">
jQ(document).ready(function() {
    
    // Seleccionar/deseleccionar todos
    jQ('#seleccionar-todos-modal').on('change', function() {
        jQ('.comprobante-checkbox-modal').prop('checked', this.checked);
    });
    
    // Solicitar cancelación
    jQ('#btnSolicitarCancelacionModal').on('click', function() {
        var seleccionados = [];
        jQ('.comprobante-checkbox-modal:checked').each(function() {
            seleccionados.push(jQ(this).val());
        });
        
        if (seleccionados.length === 0) {
            alert('Por favor selecciona al menos un comprobante para cancelar');
            return;
        }
        
        if (!confirm('¿Estás seguro de solicitar la cancelación de ' + seleccionados.length + ' comprobante(s)?')) {
            return;
        }
        
        var motivo = jQ('#motivo-cancelacion-modal').val();
        
        jQ('#btnSolicitarCancelacionModal').hide();
        jQ('#loading-cancelacion-modal').show();
        
        jQ.ajax({
            url: WEB_ROOT + '/ajax/utilerias.php',
            type: 'POST',
            data: {
                type: 'solicitarCancelacionExcel',
                comprobantes_ids: JSON.stringify(seleccionados),
                motivo: motivo
            },
            success: function(response) {
                jQ('#loading-cancelacion-modal').hide();
                jQ('#btnSolicitarCancelacionModal').show();
                
                // Mostrar resultado en el modal
                grayOut(true);
                jQ('#fview').show();
                FViewOffSet(response);
            },
            error: function() {
                jQ('#loading-cancelacion-modal').hide();
                jQ('#btnSolicitarCancelacionModal').show();
                alert('Error de comunicación con el servidor');
            }
        });
    });
});
</script>