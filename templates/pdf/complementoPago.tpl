{if $xmlData.pagos}
    <p class="bold">Complemento de pago</p>
    {foreach from=$xmlData.pagos item=pago}
        <table width="100%" class="outline-table">
            <tbody>
            <tr class="border-bottom border-right center font-smallest">
                <td class="border-top" width="20%"><strong>Fecha pago</strong></td>
                <td class="border-top" width="20%"><strong>Forma pago</strong></td>
                <td class="border-top" width="20%"><strong>Moneda</strong></td>
                <td class="border-top" width="20%"><strong>Monto</strong></td>
                <td class="border-top" width="20%"><strong># Operacion</strong></td>
            </tr>
            <tr class="border-right border-bottom">
                <td class="left">{$pago.pago.FechaPago|replace:"T":" "}</td>
                <td class="left">{$pago.pago.FormaDePagoP}</td>
                <td class="left">{$pago.pago.MonedaP}</td>
                <td class="left">{$pago.pago.Monto|number}</td>
                <td class="left">{$pago.pago.NumOperacion}</td>
            </tr>
            <tr class="border-right">
                <td colspan="1" width="20%" class="pad-left no-border-right">
                    &nbsp;
                </td>
                <td colspan="4" width="80%" class="pad-left no-border-left padding-vertical">
                    <table width="100%" class="outline-table no-border">
                        <tbody>
                        <tr class="border-bottom">
                            <td colspan="5" class="font-smaller"><strong>Documento relacionado</strong></td>
                        </tr>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-left" width="20%"><strong>Id Documento</strong></td>
                            <td width="20%"><strong>Serie</strong></td>
                            <td width="20%"><strong>Foiio</strong></td>
                            <td width="20%"><strong>Moneda</strong></td>
                            <td width="20%"><strong>Metodo de Pago</strong></td>
                        </tr>
                        <tr class="border-right font-smallest">
                            <td class="center border-bottom border-left">{$pago.doctoRelacionado.IdDocumento}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.Serie}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.Folio}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.MonedaDR}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.MetodoDePagoDR}</td>
                        </tr>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-left" width="20%"><strong># Parcialidad</strong></td>
                            <td width="20%"><strong>Saldo anterior</strong></td>
                            <td width="20%"><strong>Importe pagado</strong></td>
                            <td width="20%"><strong>Saldo insoluto</strong></td>
                            <td width="20%">&nbsp;</td>
                        </tr>
                        <tr class="border-right font-smallest">
                            <td class="center border-bottom border-left">{$pago.doctoRelacionado.NumParcialidad}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpSaldoAnt|number}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpPagado|number}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpSaldoInsoluto|number}</td>
                            <td class="center border-bottom"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="small-height">&nbsp;</p>
    {/foreach}
{/if}