<?php
class CurrencyConverter extends Producto
{

    /**
     * Calcula EquivalenciaDR basado en los montos pagados en ambas monedas.
     * EquivalenciaDR = (monto pagado en moneda del DR) / (monto pagado en moneda del pago)
     *
     * @param float $amountDR Monto pagado en la moneda del documento relacionado
     * @param float $amountP Monto pagado en la moneda del pago
     * @return float EquivalenciaDR con 10 decimales de precisión
     */
    public function calculateEquivalenciaDRFromAmount($amountDR, $amountP, $decimals = 10)
    {
        if ($amountP == 0) {
            throw new Exception("El monto en moneda del pago no puede ser cero.");
        }

        // EquivalenciaDR = amountDR / amountP
        $equivalencia = bcdiv((string)$amountDR, (string)$amountP, $decimals);

        return (float)$equivalencia;
    }

    /**
     * Convierte el monto del pago a la moneda del documento relacionado usando EquivalenciaDR.
     * Redondea al entero más cercano usando redondeo aritmético estándar.
     *
     * @param float $amount Monto original del pago
     * @param float $equivalenciaDR EquivalenciaDR calculada
     * @return float Monto convertido y redondeado
     */
    public function convertAmount($amount, $equivalenciaDR)
    {
        // Convertir: amount * EquivalenciaDR
        $converted = bcmul((string)$amount, (string)$equivalenciaDR, 10);

        // Redondear al entero más cercano
        $convertedFloat = (float)$converted;
        $convertedRounded = round($convertedFloat,2);

        return $convertedRounded;
    }

    /**
     * Invierte la conversión: calcula el monto original a partir del convertido y EquivalenciaDR.
     * Debe ser igual al amount original si no hay redondeo intermedio.
     *
     * @param float $amountConvertido Monto convertido (redondeado)
     * @param float $equivalenciaDR EquivalenciaDR calculada
     * @return float Monto original (sin redondeo adicional, para verificación)
     */
    public function reverseConvertAmount($amountConvertido, $equivalenciaDR)
    {
        // Invertir: amountConvertido / EquivalenciaDR
        $original = bcdiv((string)$amountConvertido, (string)$equivalenciaDR, 10);
        return round((float)$original,2);
    }
}
?>