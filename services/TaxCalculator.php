<?php
class TaxCalculator extends Producto
{
    /**
     * Calcula los impuestos para DR y P proporcionalmente.
     * Para cada tipo de impuesto, calcula DR con el monto pagado en moneda DR y P con el monto pagado en moneda P.
     *
     * @param array $xmlData Datos del XML del comprobante
     * @param float $totalDR Total del comprobante en moneda DR
     * @param float $paidDR Monto pagado convertido a moneda DR
     * @param float $paidP Monto pagado en moneda P
     * @param float $equivalenciaDR EquivalenciaDR entre moneda DR y P
     * @return array ['trasladosDR' => [...], 'trasladosP' => [...]]
     */
    public function calculateTaxes($traslados, $totalDR, $paidDR, $equivalenciaDR, $paidP = null)
    {
        $trasladosDR = [];
        $trasladosP = [];

        if (DESGLOSAR_IMPUESTOS_COMPLEMENTO_PAGO && is_array($traslados)) {

            foreach ($traslados as $traslado) {

                $tras = array_values(json_decode(json_encode($traslado), true));
                $currentTraslado = $tras[0];

                // Calcular porcentaje proporcional: (Base + Importe) / totalDR, vienen con dos decimales
                $baseMasImporte = $currentTraslado['Base'] + $currentTraslado['Importe'];
                $porcentaje = bcdiv((string)$baseMasImporte, (string)$totalDR, 10);

                // Para DR: Monto proporcional en moneda DR
                $montoProporcionalDR = bcmul((string)$paidDR, $porcentaje, 10);
                $tasa = (string)$currentTraslado['TasaOCuota'];
                $baseDRRaw = bcdiv($montoProporcionalDR, bcadd('1.0', $tasa, 10), 10);
                $baseDR = round((float)$baseDRRaw, 2);
                $trasladoDR['BaseDR'] = $baseDR;
                $trasladoDR['ImpuestoDR'] = $currentTraslado['Impuesto'];
                $trasladoDR['TipoFactorDR'] = $currentTraslado['TipoFactor'];
                $importeDR = bcmul($baseDRRaw, $tasa, 2);

                if ($trasladoDR['TipoFactorDR'] !== 'Exento') {
                    $trasladoDR['TasaOCuotaDR'] = $tasa;
                    $trasladoDR['ImporteDR'] = $importeDR;
                }

                $trasladosDR[] = $trasladoDR;

                // Para P: Monto proporcional en moneda P con alta precisión
                $basePRaw = bcdiv($baseDR, $equivalenciaDR, 10);
                $baseP = round((float)$basePRaw, 2);
                $importePRaw = bcdiv($importeDR, $equivalenciaDR, 10);
                $importeP = round((float)$importePRaw, 2);
                
                $trasladoP['BaseP'] = $baseP;
                $trasladoP['ImpuestoP'] = $currentTraslado['Impuesto'];
                $trasladoP['TipoFactorP'] = $currentTraslado['TipoFactor'];

                if ($trasladoP['TipoFactorP'] !== 'Exento') {
                    $trasladoP['TasaOCuotaP'] = $tasa;
                    $trasladoP['ImporteP'] = $importeP;
                }

                $trasladosP[] = $trasladoP;
            }

            // Validar el cuadre del pago para DR: suma BaseDR + ImporteDR debe ser igual a paidDR
            $sumBaseDR = '0';
            $sumImporteDR = '0';
            for ($i = 0; $i < count($trasladosDR); $i++) {
                $sumBaseDR = bcadd($sumBaseDR, (string)$trasladosDR[$i]['BaseDR'], 2);
                if (isset($trasladosDR[$i]['ImporteDR'])) {
                    $sumImporteDR = bcadd($sumImporteDR, (string)$trasladosDR[$i]['ImporteDR'], 2);
                }
            }
            $totalSumDR = bcadd($sumBaseDR, $sumImporteDR, 2);
            $diffDR = bcsub((string)$paidDR, $totalSumDR, 2);
            
            if (bccomp($diffDR, '0.01', 2) == 0) {
                // Te falta 0.01, súmaselo al ImporteDR del último traslado
                $lastIndex = count($trasladosDR) - 1;
                if ($lastIndex >= 0 && isset($trasladosDR[$lastIndex]['ImporteDR'])) {
                    $trasladosDR[$lastIndex]['ImporteDR'] = bcadd((string)$trasladosDR[$lastIndex]['ImporteDR'], '0.01', 2);
                    // Actualizar el correspondiente ImporteP con precisión alta
                    $importePRaw = bcdiv((string)$trasladosDR[$lastIndex]['ImporteDR'], (string)$equivalenciaDR, 10);
                    $trasladosP[$lastIndex]['ImporteP'] = round((float)$importePRaw, 2);
                }
            } elseif (bccomp($diffDR, '-0.01', 2) == 0) {
                // Te sobra 0.01, réstaselo al ImporteDR del último traslado
                $lastIndex = count($trasladosDR) - 1;
                if ($lastIndex >= 0 && isset($trasladosDR[$lastIndex]['ImporteDR'])) {
                    $trasladosDR[$lastIndex]['ImporteDR'] = bcsub((string)$trasladosDR[$lastIndex]['ImporteDR'], '0.01', 2);
                    // Actualizar el correspondiente ImporteP con precisión alta
                    $importePRaw = bcdiv((string)$trasladosDR[$lastIndex]['ImporteDR'], (string)$equivalenciaDR, 10);
                    $trasladosP[$lastIndex]['ImporteP'] = round((float)$importePRaw, 2);
                }
            }

            // Validar el cuadre del pago para P: suma BaseP + ImporteP debe ser igual a paidP
            if ($paidP !== null) {
                $sumBaseP = '0';
                $sumImporteP = '0';
                for ($i = 0; $i < count($trasladosP); $i++) {
                    $sumBaseP = bcadd($sumBaseP, (string)$trasladosP[$i]['BaseP'], 2);
                    if (isset($trasladosP[$i]['ImporteP'])) {
                        $sumImporteP = bcadd($sumImporteP, (string)$trasladosP[$i]['ImporteP'], 2);
                    }
                }
                $totalSum = bcadd($sumBaseP, $sumImporteP, 2);
                $diff = bcsub((string)$paidP, $totalSum, 2);
                
                if (bccomp($diff, '0.01', 2) == 0) {
                    // Te falta 0.01, súmaselo al ImporteP del último traslado
                    $lastIdx = count($trasladosP) - 1;
                    if ($lastIdx >= 0 && isset($trasladosP[$lastIdx]['ImporteP'])) {
                        $trasladosP[$lastIdx]['ImporteP'] = bcadd((string)$trasladosP[$lastIdx]['ImporteP'], '0.01', 2);
                    }
                } elseif (bccomp($diff, '-0.01', 2) == 0) {
                    // Te sobra 0.01, réstaselo al ImporteP del último traslado
                    $lastIdx = count($trasladosP) - 1;
                    if ($lastIdx >= 0 && isset($trasladosP[$lastIdx]['ImporteP'])) {
                        $trasladosP[$lastIdx]['ImporteP'] = bcsub((string)$trasladosP[$lastIdx]['ImporteP'], '0.01', 2);
                    }
                }
                // Si es igual, no hacer nada
            }
        }

        return ['trasladosDR' => $trasladosDR, 'trasladosP' => $trasladosP];
    }
}
?>