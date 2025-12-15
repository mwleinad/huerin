jQ(document).on("click",".spanDoAction",function () {
   var type =  jQ(this).data('type');
   jQ.ajax({
      url:WEB_ROOT+"/ajax/utilerias.php",
      type:'post',
      data:{type:type},
      success:function (response) {
          var splitResponse =  response.split("[#]");
          ShowStatusPopUp(splitResponse[1]);
      } ,
       error:function () {
           alert("Error al cancelar");
       }
   });
});

jQ(document).on("click",".spanOpenModalCheck",function () {
    var type =  jQ(this).data('type');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/utilerias.php",
        type:'post',
        data:{type:type},
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            
            // Aumentar tamaño del modal para cancelación Excel
            if (type === 'open_cancel_cfdi_from_excel') {
                jQ('#fview').addClass('modal-excel-large');
            } else {
                jQ('#fview').removeClass('modal-excel-large');
            }
            
            FViewOffSet(response);
        } ,
        error:function () {
            alert("Error al cancelar");
        }
    });
});

jQ(document).on('click','#btnCheckStatus',function(){
    var form = jQ(this).parents('form:first');
    var data = new FormData(form[0]);
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/utilerias.php',
            method:'post',
            data: data,
            processData: false,
            contentType: false,
            beforeSend:function(){
                jQ('#btnCheckStatus').hide();
                jQ('#loading-img').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0] === 'ok'){
                    jQ('#loading-img').hide();
                    jQ('#btnCheckStatus').show();
                    ShowStatusPopUp(splitResp[1]);
                    if(splitResp[2] === '1')
                        location.href = splitResp[3]
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#btnCheckStatus').show();
                    jQ('#loading-img').hide();
                }
            }
        });
    }else
        return;

});

jQ(document).on("click",".spanOpenGetSalario",function () {
    var type =  jQ(this).data('type');
    jQ.ajax({
        url:WEB_ROOT+"/ajax/utilerias.php",
        type:'post',
        data:{type:type},
        success:function (response) {
            grayOut(true);
            jQ('#fview').show();
            FViewOffSet(response);
        } ,
        error:function () {
            alert("Error");
        }
    });
});

jQ(document).on('click','#btnGetSalario',function(){
    var form = jQ(this).parents('form:first');
    if(form.length>0){
        jQ.ajax({
            url:WEB_ROOT+'/ajax/utilerias.php',
            method:'post',
            data:form.serialize(true),
            beforeSend:function(){
                jQ('#btnGetSalario').hide();
                jQ('#loading-img').show();
            },
            success:function(response){
                var splitResp =  response.split("[#]");
                if(splitResp[0]=='ok'){
                    jQ('#loading-img').hide();
                    jQ('#btnGetSalario').show();
                    ShowStatusPopUp(splitResp[1]);
                }
                else{
                    ShowStatusPopUp(splitResp[1]);
                    jQ('#btnGetSalario').show();
                    jQ('#loading-img').hide();
                }
            }
        });
    }else
        return;

});

jQ(document).on('click','#btnUploadExcel',function(){
    var fileInput = jQ('#archivo_excel')[0];
    
    if (!fileInput.files.length) {
        alert('Por favor selecciona un archivo Excel');
        return;
    }
    
    var file = fileInput.files[0];
    var fileName = file.name.toLowerCase();
    
    if (!fileName.endsWith('.xlsx') && !fileName.endsWith('.xls')) {
        alert('Por favor selecciona un archivo Excel válido (.xlsx o .xls)');
        return;
    }
    
    var formData = new FormData(jQ('#frm-cancel-cfdi-from-excel')[0]);
    
    jQ('#btnUploadExcel').hide();
    jQ('#loading-excel').show();
    
    jQ.ajax({
        url: WEB_ROOT + '/ajax/utilerias.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            jQ('#loading-excel').hide();
            
            try {
                var data = JSON.parse(response);
                if (data.success) {
                    mostrarResultadosExcel(data);
                } else {
                    alert('Error: ' + data.error);
                    jQ('#btnUploadExcel').show();
                }
            } catch (e) {
                // Si no es JSON, es el formato de modal estándar
                var splitResp = response.split("[#]");
                if(splitResp[0] === 'fail') {
                    jQ('#btnUploadExcel').show();
                    ShowStatusPopUp(splitResp[1]);
                } else {
                    alert('Respuesta inesperada del servidor');
                    jQ('#btnUploadExcel').show();
                }
            }
        },
        error: function() {
            jQ('#loading-excel').hide();
            jQ('#btnUploadExcel').show();
            alert('Error de comunicación con el servidor');
        }
    });
});

function mostrarResultadosExcel(data) {
    // Ocultar sección de upload y mostrar resultados
    jQ('#seccion-upload-excel').hide();
    jQ('#seccion-resultados-excel').show();
    
    // Mostrar resumen
    var resumen = '<div class="resumen-box">';
    resumen += '<strong>Resumen del procesamiento:</strong><br>';
    resumen += 'Total de UUIDs encontrados: <strong>' + data.total_encontrados + '</strong><br>';
    resumen += 'UUIDs válidos: <strong>' + data.total_validados + '</strong><br>';
    resumen += 'UUIDs con errores: <strong>' + data.total_errores + '</strong>';
    resumen += '</div>';
    
    jQ('#resumen-procesamiento').html(resumen);
    
    // Mostrar comprobantes válidos
    if (data.validados && data.validados.length > 0) {
        var comprobantesActivos = data.validados.filter(function(item) {
            return item.datos && (item.datos.status === 'activo' || item.datos.status == '1');
        });
        
        var tablaHtml = '<h4>Todos los comprobantes encontrados (' + data.validados.length + ')</h4>';
        tablaHtml += '<div class="checkbox-container">';
        tablaHtml += '<label><input type="checkbox" id="seleccionar-todos-excel"> Seleccionar todos los comprobantes activos</label>';
        tablaHtml += '</div>';
        tablaHtml += '<div class="tabla-container">';
        tablaHtml += '<table class="tabla-resultados">';
        tablaHtml += '<thead><tr>';
        tablaHtml += '<th width="30">Sel.</th><th>UUID</th><th>Serie-Folio</th><th>Fecha</th><th>Receptor</th><th>Total</th><th>Estado</th>';
        tablaHtml += '</tr></thead><tbody>';
        
        data.validados.forEach(function(item) {
            // Convertir estado numérico a texto
            var statusText = '';
            var statusClass = '';
            if (item.datos.status == '1' || item.datos.status === 'activo') {
                statusText = 'Activo';
                statusClass = 'activo';
            } else if (item.datos.status == '0' || item.datos.status === 'cancelado') {
                statusText = 'Cancelado';
                statusClass = 'cancelado';
            } else {
                statusText = 'Inactivo';
                statusClass = 'inactivo';
            }
            
            var isActive = (item.datos.status == '1' || item.datos.status === 'activo');
            var rowClass = !isActive ? ' class="row-inactive"' : '';
            tablaHtml += '<tr' + rowClass + '>';
            
            if (isActive) {
                tablaHtml += '<td><input type="checkbox" class="comprobante-checkbox-excel" value="' + item.datos.comprobanteId + '"></td>';
            } else {
                tablaHtml += '<td><span title="Solo se pueden cancelar comprobantes activos">-</span></td>';
            }
            
            tablaHtml += '<td class="uuid-cell">' + item.uuid + '</td>';
            tablaHtml += '<td>' + item.datos.serie + '-' + item.datos.folio + '</td>';
            tablaHtml += '<td>' + item.datos.fecha + '</td>';
            tablaHtml += '<td>' + item.datos.nombre_receptor + '</td>';
            tablaHtml += '<td>$' + parseFloat(item.datos.total).toFixed(2) + '</td>';
            tablaHtml += '<td><span class="status-' + statusClass + '">' + statusText + '</span></td>';
            tablaHtml += '</tr>';
        });
        
        tablaHtml += '</tbody></table></div>';
        
        if (comprobantesActivos.length > 0) {
            tablaHtml += '<div class="formLine" style="margin-top: 15px;">';
            tablaHtml += '<div style="width:30%;float:left; font-weight: bold">Motivo de cancelación:</div>';
            tablaHtml += '<div style="width:70%;float:left">';
            tablaHtml += '<select id="motivo-cancelacion-excel" class="largeInput">';
            tablaHtml += '<option value="01">Comprobante emitido con errores con relación</option>';
            tablaHtml += '<option value="02" selected>Comprobante emitido con errores sin relación</option>';
            tablaHtml += '<option value="03">No se llevó a cabo la operación</option>';
            tablaHtml += '<option value="04">Operación nominativa relacionada en una factura global</option>';
            tablaHtml += '</select></div></div>';
            
            jQ('#btnSolicitarCancelacionExcel').show();
        }
        
        jQ('#tabla-comprobantes-encontrados').html(tablaHtml).show();
    }
    
    // Mostrar errores
    if (data.errores && data.errores.length > 0) {
        var erroresHtml = '<h4>UUIDs con errores (' + data.errores.length + ')</h4>';
        erroresHtml += '<div class="tabla-container">';
        erroresHtml += '<table class="tabla-resultados">';
        erroresHtml += '<thead><tr><th>Hoja</th><th>Fila</th><th>UUID</th><th>Error</th></tr></thead><tbody>';
        
        data.errores.forEach(function(error) {
            erroresHtml += '<tr>';
            erroresHtml += '<td>' + error.hoja + '</td>';
            erroresHtml += '<td>' + error.fila + '</td>';
            erroresHtml += '<td class="uuid-cell">' + error.uuid + '</td>';
            erroresHtml += '<td class="error-text">' + error.error + '</td>';
            erroresHtml += '</tr>';
        });
        
        erroresHtml += '</tbody></table></div>';
        jQ('#tabla-errores-encontrados').html(erroresHtml).show();
    }
}

jQ(document).on('change', '#seleccionar-todos-excel', function() {
    jQ('.comprobante-checkbox-excel').prop('checked', this.checked);
});

jQ(document).on('click', '#btnNuevoProcesamiento', function() {
    // Limpiar y mostrar sección de upload
    jQ('#seccion-resultados-excel').hide();
    jQ('#seccion-upload-excel').show();
    jQ('#frm-cancel-cfdi-from-excel')[0].reset();
    jQ('#btnUploadExcel').show();
});

// Cerrar modal y remover clase modal grande
jQ(document).on('click', '#closePopUpDiv, #fviewclose', function() {
    jQ('#fview').removeClass('modal-excel-large');
    close_popup();
});

jQ(document).on('click', '#btnSolicitarCancelacionExcel', function() {
    var seleccionados = [];
    jQ('.comprobante-checkbox-excel:checked').each(function() {
        seleccionados.push(parseInt(jQ(this).val(), 10));
    });
    
    if (seleccionados.length === 0) {
        alert('Por favor selecciona al menos un comprobante para cancelar');
        return;
    }
    
    if (!confirm('¿Estás seguro de solicitar la cancelación de ' + seleccionados.length + ' comprobante(s)?')) {
        return;
    }
    
    var motivo = jQ('#motivo-cancelacion-excel').val();
    
    jQ('#botones-resultados').hide();
    jQ('#loading-cancelacion-excel').show();
    
    jQ.ajax({
        url: WEB_ROOT + '/ajax/utilerias.php',
        type: 'POST',
        data: {
            type: 'solicitarCancelacionExcel',
            comprobantes_ids: JSON.stringify(seleccionados),
            motivo: motivo
        },
        success: function(response) {
            jQ('#loading-cancelacion-excel').hide();
            jQ('#botones-resultados').show();
            
            var splitResp = response.split("[#]");
            ShowStatusPopUp(splitResp[1]);
            
            if(splitResp[0] === 'ok') {
                // Limpiar selecciones después de cancelación exitosa
                jQ('.comprobante-checkbox-excel').prop('checked', false);
                jQ('#seleccionar-todos-excel').prop('checked', false);
            }
        },
        error: function() {
            jQ('#loading-cancelacion-excel').hide();
            jQ('#botones-resultados').show();
            alert('Error de comunicación con el servidor');
        }
    });
});
