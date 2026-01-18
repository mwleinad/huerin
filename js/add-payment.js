var AJAX_PATH = WEB_ROOT+'/ajax/add-payment.php'

// Función para convertir entre monedas
function convertirMoneda(monto, monedaOrigen, monedaDestino, tipoCambio) {
    // Si las monedas son iguales, no hay conversión
    if (monedaOrigen == monedaDestino) {
        return monto;
    }
    
    // Definir moneda base (MXN)
    var monedaBase = 'MXN';
    var resultado = monto;
    
    // Si la moneda origen es MXN y destino es otra (USD, EUR, etc.)
    if (monedaOrigen == monedaBase && monedaDestino != monedaBase) {
        // De pesos a otra moneda: dividir entre tipo de cambio
        resultado = monto / tipoCambio;
    }
    // Si la moneda origen es otra y destino es MXN
    else if (monedaOrigen != monedaBase && monedaDestino == monedaBase) {
        // De otra moneda a pesos: multiplicar por tipo de cambio
        resultado = monto * tipoCambio;
    }
    // Si ambas son diferentes de MXN (ej: USD a EUR)
    else if (monedaOrigen != monedaBase && monedaDestino != monedaBase) {
        // Convertir primero a MXN y luego a la moneda destino
        // Esto requeriría dos tipos de cambio, por ahora usar el tipo de cambio directo
        resultado = monto * tipoCambio;
    }
    
    // Retornar con 4 decimales de precisión
    return Math.round(resultado * 10000) / 10000;
}
jQ(document).ready(function(){
   jQ('#addPayment').on('click',function(){
        var id =  this.id;
        var form = jQ(this).parents('form:first');
        var fd =  new FormData(form[0]);

        // Validation
        jQ('#metodoDePagoError').text('');
        jQ('#paymentDateError').text('');
        jQ('#amountError').text('');
        jQ('#depositoError').text('');
        jQ('#tipoDeMonedaError').text('');
        jQ('#tipoCambioError').text('');
        jQ('#confirmAmountError').text('');

        var hasError = false;

        if (jQ('#metodoDePago').val() == '') {
            jQ('#metodoDePagoError').text('Forma de Pago es requerida');
            hasError = true;
        }

        if (jQ('#paymentDate').val() == '') {
            jQ('#paymentDateError').text('Fecha es requerida');
            hasError = true;
        }

        if (jQ('#amount').val() == '' || isNaN(jQ('#amount').val()) || parseFloat(jQ('#amount').val()) <= 0) {
            jQ('#amountError').text('Importe es requerido y debe ser un número mayor a 0');
            hasError = true;
        }

        // Validar que el importe no exceda el saldo (con conversión de moneda si aplica)
        var amount = parseFloat(jQ('#amount').val());
        var saldo = parseFloat(jQ('#saldoComprobante').val());
        var monedaPago = jQ('#tipoDeMoneda').val();
        var monedaComprobante = jQ('#monedaComprobante').val();
        var tipoCambio = parseFloat(jQ('#tipoCambio').val()) || 1;
        
        if (!isNaN(amount) && !isNaN(saldo)) {
            var amountConvertido = amount;
            
            // Si las monedas son diferentes, convertir
            if (monedaPago != monedaComprobante) {
                // Convertir el monto del pago a la moneda del comprobante
                amountConvertido = convertirMoneda(amount, monedaPago, monedaComprobante, tipoCambio);
            }
            
            if (amountConvertido > saldo) {
                if (monedaPago != monedaComprobante) {
                    jQ('#amountError').text('El importe convertido (' + amountConvertido.toFixed(4) + ' ' + monedaComprobante + ') excede el saldo disponible (' + saldo.toFixed(2) + ' ' + monedaComprobante + ')');
                } else {
                    jQ('#amountError').text('El importe no puede ser mayor al saldo disponible (' + saldo.toFixed(2) + ')');
                }
                hasError = true;
            }
        }

        if (jQ('#metodoDePago').val() != 'Saldo a Favor' && jQ('#deposito').val() == '') {
            jQ('#depositoError').text('Deposito es requerido');
            hasError = true;
        }

        if (jQ('#tipoDeMoneda').val() == '') {
            jQ('#tipoDeMonedaError').text('Tipo de Moneda es requerido');
            hasError = true;
        }

        // Validar tipoCambio si está visible
        if (jQ('#tipoCambioDiv').is(':visible')) {
            var tipoCambio = parseFloat(jQ('#tipoCambio').val());
            if (isNaN(tipoCambio) || tipoCambio <= 0) {
                jQ('#tipoCambioError').text('Tipo de Cambio es requerido y debe ser un número mayor a 0');
                hasError = true;
            }
        }

        // Validar confirmAmount si está visible (monedas diferentes)
        if (jQ('#confirmAmountDiv').is(':visible')) {
            var confirmAmount = parseFloat(jQ('#confirmAmount').val());
            if (isNaN(confirmAmount) || confirmAmount <= 0) {
                jQ('#confirmAmountError').text('Importe de confirmación es requerido y debe ser un número mayor a 0');
                hasError = true;
            } else if (confirmAmount > saldo) {
                jQ('#confirmAmountError').text('El importe de confirmación no puede ser mayor al saldo disponible (' + saldo.toFixed(2) + ' ' + monedaComprobante + ')');
                hasError = true;
            }
        }

        if (hasError) {
            return;
        }

        jQ.ajax({
            url: AJAX_PATH,
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function(){
                jQ('#loading-img').show();
                jQ('#'+id).hide();
            },
            success: function(response){
                var splitResp = response.split("[#]");
                console.log(response);
                if(splitResp[0]=='ok')
                {
                    ShowStatusPopUp(splitResp[1]);
                    form[0].reset();
                    jQ('#loading-img').hide();
                    jQ('#'+id).show();

                }else{
                    jQ('#loading-img').hide();
                    jQ('#'+id).show();
                    ShowStatusPopUp(splitResp[1]);
                }

            },

        });

   });
});
