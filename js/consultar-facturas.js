function componenteCancelar () {
	return {
        url_path: WEB_ROOT,
        motivo_cancelacion: [
            {
                clave_sat: '01',
                name: "Comprobantes emitidos con errores con relación"
            },
            {
                clave_sat: '02',
                name: "Comprobantes emitidos con errores sin relación"
            },
            {
                clave_sat: '03',
                name: "No se llevó a cabo la operación"
            },
            {
                clave_sat: '04',
                name: "Operación nominativa relacionada en una factura global"
            },
        ],
        origen_sustitucion: [
            {
                id: 1,
                name: "Capturar UUID"
            },
            {
                id: 2,
                name: "Generar nueva factura correspondiente al mes a cancelar."
            },
        ],
        current_cancelacion: {
            id: null,
            clave_sat: null,
            uuid_sustitucion: null,
            motivo: null,
            origen_sustitucion: null,
        },
        // Variables para información SAT e intentos
        sat_info: {
            show_info: false,
            status: 1,
            sat_message: '',
            intentos_cancelacion: 0,
            max_intentos: 2
        },
        // Variables para validación de UUID
        uuid_validation: {
            is_valid: false,
            is_checking: false,
            message: '',
            status: '' // 'success', 'warning', 'error'
        },
        uuid_validation_timer: null,
        resetCurrentCancelacion() {
            this.current_cancelacion = {
                id: null,
                clave_sat: null,
                uuid_sustitucion: null,
                motivo: null,
                origen_sustitucion: null,
            }
            this.resetUuidValidation();
        },
        resetUuidValidation() {
            this.uuid_validation = {
                is_valid: false,
                is_checking: false,
                message: '',
                status: ''
            };
            if (this.uuid_validation_timer) {
                clearTimeout(this.uuid_validation_timer);
                this.uuid_validation_timer = null;
            }
        },
        loading: false,
        loading_consultado_estatus_sat: false,
        loading_sustituyente: false,
        show_btn: false,
        cancelarFactura () {
            // Verificar si se pueden hacer más intentos
            if (this.sat_info.intentos_cancelacion >= this.sat_info.max_intentos) {
                ShowErrorOnPopup('<div class="alert alert-danger">Has excedido el máximo de intentos de cancelación (2) para esta factura.</div>', true);
                return;
            }
            
            // Validar campos antes de enviar
            if (!this.current_cancelacion.clave_sat) {
                ShowErrorOnPopup('<div class="alert alert-danger">Por favor seleccione un motivo de cancelación SAT</div>', true);
                return;
            }
            
            if (!this.current_cancelacion.motivo || !this.current_cancelacion.motivo.trim()) {
                ShowErrorOnPopup('<div class="alert alert-danger">Por favor ingrese una descripción breve</div>', true);
                return;
            }
            
            // Validar UUID si es requerido - SOLO FORMATO, NO EXISTENCIA
            if ((this.current_cancelacion.clave_sat === '01' || this.current_cancelacion.clave_sat === '04') && this.current_cancelacion.origen_sustitucion == 1) {
                if (!this.current_cancelacion.uuid_sustitucion || !this.current_cancelacion.uuid_sustitucion.trim()) {
                    ShowErrorOnPopup('<div class="alert alert-danger">Por favor ingrese el UUID que sustituye</div>', true);
                    return;
                }
                
                if (!this.isValidUUIDFormat(this.current_cancelacion.uuid_sustitucion)) {
                    ShowErrorOnPopup('<div class="alert alert-danger">El formato del UUID no es válido. Debe tener el formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx</div>', true);
                    return;
                }
                
                // VALIDACIÓN DE EXISTENCIA TEMPORALMENTE DESHABILITADA - CONSERVAR LÓGICA PARA USO FUTURO
                /*
                if (!this.uuid_validation.is_valid) {
                    ShowErrorOnPopup('<div class="alert alert-danger">Por favor ingrese un UUID válido y existente en el sistema</div>', true);
                    return;
                }
                */
            }
            
            this.loading = true
            this.show_btn = false
            this.current_cancelacion.type = 2
            fetch(this.url_path + '/ajax/invoice.php', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.current_cancelacion),
            }).then(response => response.json())
              .then((response) => {
                    this.loading=  false
                    ShowStatusPopUp(response.message)
                    this.show_btn = true
                    if(response.result === 1) {
                        document.getElementById('total').innerHTML = response.resumen;
                        document.getElementById('facturasListDiv').innerHTML = response.lista_invoice;
                        this.resetCurrentCancelacion()
                        close_popup();
                    } else {
                        // Recargar información de intentos después de un intento fallido
                        this.loadSatInfo(this.current_cancelacion.id);
                    }
                }).catch((response) => {
                ShowStatusPopUp(response.errorText)
            })
        },
        initData (id) {
          this.current_cancelacion.id = id;
          this.loadSatInfo(id);
        },
        // Cargar información del SAT e intentos
        async loadSatInfo(id) {
            
            this.loading_consultado_estatus_sat = true;
            try {

                const response = await fetch(this.url_path + '/ajax/manage-facturas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        type: 'get_cancelacion_info',
                        id_item: id
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.sat_info = {
                        show_info: true,
                        status: parseInt(result.data.status) || 1,
                        sat_message: result.data.sat_message || '',
                        intentos_cancelacion: parseInt(result.data.intentos_cancelacion) || 0,
                        max_intentos: parseInt(result.data.max_intentos) || 2
                    };
                }
            } catch (error) {
                console.error('Error loading SAT info:', error);
                // Si hay error, asumir que puede continuar
                this.sat_info.show_info = true;
            } finally {
                this.loading_consultado_estatus_sat = false;
            }
        },
        handlerSelectMotivo(event) {
            this.current_cancelacion.uuid_sustitucion = null
            // this.resetUuidValidation(); // VALIDACIÓN UUID DESHABILITADA TEMPORALMENTE
            this.show_btn = event.target.value !== ''
        },
        handlerOrigenSustitucion(event) {
            if (event.target.value !== 2) {
                this.current_cancelacion.uuid_sustitucion = null
                // this.resetUuidValidation(); // VALIDACIÓN UUID DESHABILITADA TEMPORALMENTE
            }
        },
        // Función para validar formato de UUID
        isValidUUIDFormat(uuid) {
            // Expresión regular para validar el formato de UUID del SAT México (8-4-4-4-12)
            const uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;

            return uuidRegex.test(uuid);
        },
        // Función para validar UUID en tiempo real
        validateUUID() {
            const uuid = this.current_cancelacion.uuid_sustitucion;
            
            if (!uuid || uuid.trim() === '') {
                this.resetUuidValidation();
                return;
            }
            
            // Validar formato primero
            if (!this.isValidUUIDFormat(uuid)) {
                if (uuid.length >= 36) {
                    this.uuid_validation = {
                        is_valid: false,
                        is_checking: false,
                        message: 'Formato de UUID inválido',
                        status: 'error'
                    };
                } else {
                    this.resetUuidValidation();
                }
                return;
            }
            
            // Si el formato es válido, marcarlo como correcto
            this.uuid_validation = {
                is_valid: true,
                is_checking: false,
                message: 'Formato de UUID válido',
                status: 'success'
            };
            
            // VALIDACIÓN CON SERVIDOR TEMPORALMENTE DESHABILITADA - CONSERVAR LÓGICA PARA USO FUTURO
            /*
            // Limpiar timer anterior
            if (this.uuid_validation_timer) {
                clearTimeout(this.uuid_validation_timer);
            }
            
            // Establecer estado de verificación
            this.uuid_validation.is_checking = true;
            this.uuid_validation.message = 'Validando UUID...';
            this.uuid_validation.status = '';
            
            // Validar con el servidor después de un delay
            this.uuid_validation_timer = setTimeout(() => {
                this.validateUUIDWithServer(uuid);
            }, 500);
            */
        },
        // Función para validar UUID con el servidor
        async validateUUIDWithServer(uuid) {
            try {
                const response = await fetch(this.url_path + '/ajax/manage-facturas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        type: 'validar_uuid',
                        uuid: uuid
                    })
                });
                
                const result = await response.json();
                
                this.uuid_validation.is_checking = false;
                
                if (result.valid && result.exists) {
                    this.uuid_validation = {
                        is_valid: true,
                        is_checking: false,
                        message: result.message,
                        status: 'success'
                    };
                } else if (result.valid && !result.exists) {
                    this.uuid_validation = {
                        is_valid: false,
                        is_checking: false,
                        message: result.message,
                        status: 'warning'
                    };
                } else {
                    this.uuid_validation = {
                        is_valid: false,
                        is_checking: false,
                        message: result.message,
                        status: 'error'
                    };
                }
            } catch (error) {
                console.error('Error validating UUID:', error);
                this.uuid_validation = {
                    is_valid: false,
                    is_checking: false,
                    message: 'Error al validar UUID',
                    status: 'error'
                };
            }
        },
        async generarFacturaSustituyente() {
           this.loading_sustituyente=  true
           this.current_cancelacion.type = 1
           fetch(this.url_path + '/ajax/invoice.php', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.current_cancelacion),
            }).then(response => response.json())
              .then((response) => {
                  this.loading_sustituyente=  false
                   ShowStatusPopUp(response.message)
                   if(response.result === 1 || response.uuid)
                       this.current_cancelacion.uuid_sustitucion =  response.uuid

              }).catch((response) => {
                  ShowStatusPopUp(response.errorText)
            })
        }
	}
}

function componenteEnviarCorreo () {
    return {
        url_path: WEB_ROOT,
        tipos_destinatario: [
            'Responsable CxC',
            'Cliente',
        ],
        comprobante_id: null,
        nombre_cxc: null,
        email_cxc: null,
        correo_empresa:null,
        tipo_destinatario: 'Responsable CxC',
        correo_destinatario: null,
        loading: false,
        show_btn: false,
        initData (id,nombre_cxc,email_cxc,correo_empresa) {
            this.comprobante_id = id;
            this.nombre_cxc = nombre_cxc;
            this.email_cxc = email_cxc;
            this.correo_empresa  = correo_empresa ?? null;
            this.correo_destinatario = this.email_cxc;
        },
        handlerSelectTipoDestinatario(event) {
            this.correo_destinatario= null
            if (event.target.value === 'Cliente')
                this.correo_destinatario = this.correo_empresa;
            else
                this.correo_destinatario = this.email_cxc;
        },
        async enviarCorreo() {
            this.loading=  true
            const data_send = {
                type: 3,
                correo_destinatario: this.correo_destinatario,
                nombre_destinatario: this.nombre_cxc,
                tipo_destinatario: this.tipo_destinatario,
                comprobante_id: this.comprobante_id,
            }
            fetch(this.url_path + '/ajax/invoice.php', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data_send),
            }).then(response => response.json())
                .then((response) => {
                    this.loading=  false;
                    ShowStatusPopUp(response.message);
                    if(response.result === 1) {
                        document.getElementById('total').innerHTML = response.resumen;
                        document.getElementById('facturasListDiv').innerHTML = response.lista_invoice;
                    }

                }).catch((response) => {
                ShowStatusPopUp(response.errorText)
            })
        }
    }
}
function showDetailsPopup(id) {

    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php', {
        method: 'post',
        parameters: {type: "showDetails", id_item: id},
        onSuccess: function (transport) {
            var response = transport.responseText || "no response text";
            FViewOffSet(response);
            Event.observe($('closePopUpDiv'), "click", function () {
                showDetailsPopup(0);
            });
            $('accionList').observe("click", SendItemsListeners);

        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });

}//showDetailsPopup

function Exportar() {
    $('type').value = "exportar";
    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php', {
        method: 'post',
        parameters: $('frmBusqueda').serialize(true),
        onLoading: function () {
            $('loadBusqueda').show();
        },
        onSuccess: function (transport) {
            $('loadBusqueda').hide();

            var response = transport.responseText || "no response text";
            alert(response);
        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });

}//Buscar

function Buscar() {
    $('type').value = "buscar";

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php', {
        method: 'post',
        parameters: $('frmBusqueda').serialize(true),
        onLoading: function () {
            $('loadBusqueda').show();
        },
        onSuccess: function (transport) {
            $('loadBusqueda').hide();

            var response = transport.responseText || "no response text";
            console.log(response);
            var splitResponse = response.split("[#]");
            if (splitResponse[0].trim() == "ok") {
                $('total').innerHTML = splitResponse[1];
                $('facturasListDiv').innerHTML = splitResponse[2];
            }
        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });

}//Buscar

function EnviarEmail(id) {

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php',
        {
            method: 'post',
            parameters: {type: 'enviar_email', id_comprobante: id},
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] === "ok") {
                    ShowStatusPopUp(splitResponse[1])
                } else {
                    ShowStatusPopUp(splitResponse[1])
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });

}//EnviarEmail

function OpenEnviarPorCorreo(id) {

    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php', {
        method: 'post',
        parameters: {type: "open_enviar_por_email", comprobante_id: id},
        onSuccess: function (transport) {
            var response = transport.responseText || "no response text";
            FViewOffSet(response);
            Event.observe($('closePopUpDiv'), "click", function () {
                showDetailsPopup(0);
            });
        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });

}

function CancelarFactura(id) {

    grayOut(true);
    $('fview').show();
    if (id == 0) {
        $('fview').hide();
        grayOut(false);
        return;
    }

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php', {
        method: 'post',
        parameters: {type: "cancelar_div", id_item: id},
        onSuccess: function (transport) {
            var response = transport.responseText || "no response text";
            FViewOffSet(response);
            Event.observe($('closePopUpDiv'), "click", function () {
                showDetailsPopup(0);
            });
            Event.observe($('btnCancelar'), "click", DoCancelacion);
        },
        onFailure: function () {
            alert('Something went wrong...')
        }
    });

}//CancelarFactura

function DoCancelacion() {

    new Ajax.Request(WEB_ROOT + '/ajax/manage-facturas.php',
        {
            method: 'post',
            parameters: $('frmCancelar').serialize(true),
            onLoading: function () {
                $('loading-img').style.display = 'block';
                $('btnCancelar').style.display = 'none';
            },
            onSuccess: function (transport) {
                var response = transport.responseText || "no response text";
                var splitResponse = response.split("[#]");
                if (splitResponse[0] == "ok") {
                    $('total').innerHTML = splitResponse[2];
                    $('facturasListDiv').innerHTML = splitResponse[3];
                    $('loading-img').style.display = 'none';
                    $('btnCancelar').style.display = 'block';
                    $('frmCancelar').reset();
                    close_popup();
                    ShowStatusPopUp(splitResponse[1]);

                } else {
                    ShowStatusPopUp(splitResponse[1]);
                    $('loading-img').style.display = 'none';
                    $('btnCancelar').style.display = 'block';
                }
            },
            onFailure: function () {
                alert('Something went wrong...')
            }
        });
}//DoCancelacion

Event.observe(window, 'load', function () {

    AddEditItemsListeners = function (e) {

        var el = e.element();
        var del = el.hasClassName('spanDetails');
        var id = el.identify();

        if (del == true)
            showDetailsPopup(id);

        del = el.hasClassName('spanCancel');
        if (del == true)
            CancelarFactura(id);

    }

    SendItemsListeners = function (e) {

        var el = e.element();
        var del = el.hasClassName('spanSend');
        var id = el.identify();

//			alert(id);
        if (del == true)
            EnviarEmail(id);

    }

    $('facturasListDiv').observe("click", AddEditItemsListeners);
    $('btnBuscar').observe("click", Buscar);
    var element = document.getElementById('btnExportar');
    if (typeof (element) != 'undefined' && element != null)
        $('btnExportar').observe("click", Exportar);


    $('rfc').observe("keypress", function (evt) {
        if (evt.keyCode == 13)
            Buscar();
    });

    $('nombre').observe("keypress", function (evt) {
        if (evt.keyCode == 13)
            Buscar();
    });

    $('anio').observe("keypress", function (evt) {
        if (evt.keyCode == 13)
            Buscar();
    });
    
    // Variables para manejar la validación de UUID
    var uuidValidationTimer = null;
    var lastValidatedUUID = '';
    var isUUIDValid = false;
    
    // Función para validar formato de UUID
    function isValidUUIDFormat(uuid) {
        var uuidRegex = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
        return uuidRegex.test(uuid);
    }
    
    // Función para validar UUID con el servidor
    function validateUUIDWithServer(uuid) {
        if (!uuid || uuid === lastValidatedUUID) return;
        
        lastValidatedUUID = uuid;
        jQ('#uuid_loading').show();
        jQ('#uuid_validation_message').hide();
        
        jQ.ajax({
            url: WEB_ROOT + '/ajax/manage-facturas.php',
            method: 'POST',
            data: {
                type: 'validar_uuid',
                uuid: uuid
            },
            success: function(response) {
                jQ('#uuid_loading').hide();
                try {
                    var result = JSON.parse(response);
                    var messageDiv = jQ('#uuid_validation_message');
                    
                    if (result.valid && result.exists) {
                        messageDiv.html('<span class="uuid-validation-success">✓ ' + result.message + '</span>');
                        isUUIDValid = true;
                        jQ('#uuid_sustitucion').removeClass('input-validation-warning input-validation-error').addClass('input-validation-success');
                    } else if (result.valid && !result.exists) {
                        messageDiv.html('<span class="uuid-validation-warning">⚠ ' + result.message + '</span>');
                        isUUIDValid = false;
                        jQ('#uuid_sustitucion').removeClass('input-validation-success input-validation-error').addClass('input-validation-warning');
                    } else {
                        messageDiv.html('<span class="uuid-validation-error">✗ ' + result.message + '</span>');
                        isUUIDValid = false;
                        jQ('#uuid_sustitucion').removeClass('input-validation-success input-validation-warning').addClass('input-validation-error');
                    }
                    messageDiv.show();
                } catch(e) {
                    console.error('Error parsing UUID validation response:', e);
                    jQ('#uuid_validation_message').html('<span style="color: red;">Error al validar UUID</span>').show();
                    isUUIDValid = false;
                }
            },
            error: function() {
                jQ('#uuid_loading').hide();
                jQ('#uuid_validation_message').html('<span style="color: red;">Error al validar UUID</span>').show();
                isUUIDValid = false;
            }
        });
    }
    
    // Event listener para el input de UUID
    jQ(document).on('input', '#uuid_sustitucion', function() {
        var uuid = jQ(this).val().trim();
        var messageDiv = jQ('#uuid_validation_message');
        
        // Limpiar timer anterior
        if (uuidValidationTimer) {
            clearTimeout(uuidValidationTimer);
        }
        
        // Limpiar estilos y mensajes
        jQ(this).removeClass('input-validation-success input-validation-warning input-validation-error');
        messageDiv.hide();
        jQ('#uuid_loading').hide();
        // isUUIDValid = false; // Comentado temporalmente - no validamos contra servidor
        
        if (uuid.length === 0) {
            lastValidatedUUID = '';
            return;
        }
        
        // Validar formato primero
        if (!isValidUUIDFormat(uuid)) {
            if (uuid.length >= 36) { // Solo mostrar error si ya escribió suficientes caracteres
                messageDiv.html('<span class="uuid-validation-error">✗ Formato de UUID inválido</span>').show();
                jQ(this).addClass('input-validation-error');
            }
            return;
        }
        
        // Si el formato es válido, mostrar éxito
        messageDiv.html('<span class="uuid-validation-success">✓ Formato de UUID válido</span>').show();
        jQ(this).addClass('input-validation-success');
        
        // VALIDACIÓN CON SERVIDOR TEMPORALMENTE DESHABILITADA - CONSERVAR LÓGICA PARA USO FUTURO
        /*
        // Validar con el servidor después de un delay
        uuidValidationTimer = setTimeout(function() {
            validateUUIDWithServer(uuid);
        }, 500);
        */
    });
    
    // Mejorar la función DoCancelacion para validar UUID antes de enviar
    var originalDoCancelacion = window.DoCancelacion;
    window.DoCancelacion = function() {
        var motivoSat = jQ('#motivo_sat').val();
        var uuidSustitucion = jQ('#uuid_sustitucion').val().trim();
        
        // Validar campos requeridos
        if (!motivoSat) {
            alert('Por favor seleccione un motivo de cancelación SAT');
            return false;
        }
        
        if (!jQ('#motivo').val().trim()) {
            alert('Por favor ingrese una descripción breve');
            return false;
        }
        
        // Validar UUID si es requerido
        if ((motivoSat === '01' || motivoSat === '04')) {
            if (!uuidSustitucion) {
                alert('Por favor ingrese el UUID que sustituye');
                jQ('#uuid_sustitucion').focus();
                return false;
            }
            
            if (!isValidUUIDFormat(uuidSustitucion)) {
                alert('El formato del UUID no es válido. Debe tener el formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
                jQ('#uuid_sustitucion').focus();
                return false;
            }
            
            // VALIDACIÓN DE EXISTENCIA TEMPORALMENTE DESHABILITADA - CONSERVAR LÓGICA PARA USO FUTURO
            /*
            if (!isUUIDValid) {
                alert('Por favor ingrese un UUID válido y existente en el sistema');
                jQ('#uuid_sustitucion').focus();
                return false;
            }
            */
        }
        
        // Si todo está válido, proceder con la cancelación original
        return originalDoCancelacion();
    };
    
    jQ(document).on('change', '#motivo_sat', function () {
        (this.value === '01' || this.value === '04')
            ? jQ('.cfdi-sustitucion').show()
            : jQ('.cfdi-sustitucion').hide();
            
        // Limpiar validación de UUID cuando se oculta el campo
        if (this.value !== '01' && this.value !== '04') {
            jQ('#uuid_sustitucion').val('').removeClass('input-validation-success input-validation-warning input-validation-error');
            jQ('#uuid_validation_message').hide();
            jQ('#uuid_loading').hide();
            lastValidatedUUID = '';
            isUUIDValid = false;
        }
    });
});