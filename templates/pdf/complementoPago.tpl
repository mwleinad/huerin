{if $xmlData.pagos}
    <p class="bold">Documentos / Comprobantes Pagados</p>
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
                <td colspan="5" style="width: 100%; padding-left: 15px" class="pad-left no-border-left padding-vertical">
                    <table width="100%" class="outline-table no-border">
                        <tbody>
                        <tr class="border-bottom">
                            <td colspan="9" class="font-smaller"><strong>Documento relacionado</strong></td>
                        </tr>
                        <tr class="border-bottom border-right center font-smallest">
                            <td class="border-left" width="25%"><strong>Id Documento</strong></td>
                            <td width="8%"><strong># Parcialidad</strong></td>
                            <td width="6%"><strong>Serie</strong></td>
                            <td width="6%"><strong>Folio</strong></td>
                            <td width="12%"><strong>Saldo anterior</strong></td>
                            <td width="12%"><strong>Importe pagado</strong></td>
                            <td width="8%"><strong>Saldo insoluto</strong></td>
                            <td width="8%"><strong>Moneda</strong></td>
                            <td width="8%"><strong>Obj. Impuesto</strong></td>
                        </tr>
                        <tr class="border-right font-smallest">
                            <td class="center border-bottom border-left">{$pago.doctoRelacionado.IdDocumento}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.NumParcialidad}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.Serie}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.Folio}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpSaldoAnt|number}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpPagado|number}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.ImpSaldoInsoluto|number}</td>
                            <td class="center border-bottom">{$pago.doctoRelacionado.MonedaDR}</td>
                            <td class="center border-bottom">{{$pago.doctoRelacionado.ObjetoImpDR}}</td>
                        </tr>
                        {if $pago.TrasladosDR|count > 0}
                        <tr class="border-right">
                            <td style="border:0px">&nbsp;</td>
                            <td colspan="8">
                                <table style="width: 100%;" class="outline-table no-border">
                                    <tbody>
                                    <tr class="border-bottom border-top center font-smallest">
                                        <td class="border-left" style="width: 10%"><strong>Base</strong></td>
                                        <td style="width: 10%"><strong>Impuesto</strong></td>
                                        <td style="width: 10%"><strong>Factor</strong></td>
                                        <td style="width: 10%"><strong>Tasa / Cuota</strong></td>
                                        <td style="width: 10%"><strong>Importe</strong></td>
                                    </tr>
                                    {foreach from=$pago.TrasladosDR item=traslado}
                                        <tr class="border-right font-smallest">
                                            <td class="center border-bottom border-left">{$traslado.BaseDR|number}</td>
                                            <td class="center border-bottom border-left">Traslado ({$catalogos.impuestos[{$traslado.ImpuestoDR}]})s</td>
                                            <td class="center border-bottom">{$traslado.TipoFactorDR}</td>
                                            <td class="center border-bottom">{$traslado.TasaOCuotaDR}</td>
                                            <td class="center border-bottom">{$traslado.ImporteDR|number}</td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        {/if}
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    {/foreach}

    {if $xmlData.impuestosP|count > 0}
        <p class="bold">Impuestos de documentos pagados</p>
        <table width="80%" class="outline-table">
        <tbody>
            <tr class="border-bottom border-right center font-smallest">
                <td class="border-top" width="20%"><strong>Base</strong></td>
                <td class="border-top" width="20%"><strong>Impuesto</strong></td>
                <td class="border-top" width="20%"><strong>Factor</strong></td>
                <td class="border-top" width="20%"><strong>Tasa / Cuota</strong></td>
                <td class="border-top" width="20%"><strong>Importe</strong></td>
            </tr>
            {foreach from=$xmlData.impuestosP item=impuestoP}
                <tr class="border-right border-bottom">
                    <td class="center">{$impuestoP.BaseP|number}</td>
                    <td class="center">{$catalogos.impuestos[{$impuestoP.ImpuestoP}]}</td>
                    <td class="center">{$impuestoP.TipoFactorP}</td>
                    <td class="center">{if $impuestoP.TipoFactorP != 'Exento'} {$impuestoP.TasaOCuotaP} {/if}</td>
                    <td class="center">{if $impuestoP.TipoFactorP != 'Exento'} {$impuestoP.ImporteP|number} {/if}</td>
                </tr>
            {/foreach}
        </tbody>
        </table>
    {/if}
{/if}