<table width="100%">
    <tbody>
    <tr>
        <td width="60%" valign="top">
            <table width="100%">
                <tbody>
                <tr>
                    <td width="50%" valign="top">
                        <strong>Moneda:</strong> {$xmlData.cfdi.Moneda}
                    </td>
                    <td width="50%" valign="top">
                        <strong>Forma de pago:</strong> {$xmlData.cfdi.FormaPago} {$catalogos.FormaPago}
                    </td>
                </tr>
                <tr>
                    <td width="50%" valign="top">
                        <strong>Método de pago:</strong> {$xmlData.cfdi.MetodoPago} {$catalogos.MetodoPago}
                    </td>
                    <td width="50%" valign="top">
                        <strong>Condiciones de pago:</strong> {$xmlData.cfdi.CondicionesDePago}
                    </td>
                </tr>
                {if $xmlData.escuela.banco || $xmlData.escuela.referencia || $xmlData.escuela.fechaDeposito}
                    <tr>
                        <td width="50%" valign="top">
                            <strong>Banco:</strong> {$xmlData.escuela.banco} Referencia: {$xmlData.escuela.referencia}
                        </td>
                        <td width="50%" valign="top">
                            <strong>Fecha deposito:</strong> {$xmlData.escuela.fechaDeposito}
                        </td>
                    </tr>
                {/if}

                </tbody>
            </table>
        </td>
        <td width="40%" valign="top">
            {if $xmlData.impuestosLocales|count > 0}
                {foreach from=$xmlData.impuestosLocales key=keyTipo item=impuesto}
                    {assign var="totalDeducciones" value=(string)$totalDeducciones+(string)$impuesto.impuesto.importe}
                {/foreach}
                {assign var="impuestosSubtotal" value=(string)$xmlData.cfdi.SubTotal-(string)$totalDeducciones}
            {/if}
            <table width="100%">
                <tbody>
                <tr>
                    <td class="right" width="50%" valign="top">
                        <strong>Subtotal:</strong>
                    </td>
                    <td class="right border-bottom" width="50%" valign="top">
                            <span class="underline">
                                {if $xmlData.impuestosLocales|count > 0}
                                    {$impuestosSubtotal|number}
                                {else}
                                    {$xmlData.cfdi.SubTotal|number}
                                {/if}
                            </span>
                    </td>
                </tr>
                {if $xmlData.cfdi.Descuento > 0}
                    <tr>
                        <td class="right" width="50%" valign="top">
                            <strong>Descuento:</strong>
                        </td>
                        <td class="right border-bottom" width="50%" valign="top">
                            {$xmlData.cfdi.Descuento|number}
                        </td>
                    </tr>
                {/if}
                {if count($xmlData.impuestos.traslados) > 0}
                    <tr>
                        <td class="right" width="50%" valign="top">
                            <strong>Impuestos trasladados</strong>
                        </td>
                        <td class="right" width="50%" valign="top">
                            &nbsp;
                        </td>
                    </tr>
                    {foreach from=$xmlData.impuestos.traslados item=traslado}
                        <tr>
                            <td class="right" width="50%" valign="top">
                                <strong>{$catalogos.impuestos[{$traslado.Impuesto}]}:</strong>
                            </td>
                            <td class="right border-bottom" width="50%" valign="top">
                                {$traslado.Importe|number}
                            </td>
                        </tr>
                    {/foreach}
                {/if}
                {if $xmlData.impuestosLocales.ish.Importe > 0}
                    <tr>
                        <td class="right" width="50%" valign="top">
                            <strong>{$xmlData.impuestosLocales.ish.ImpLocTrasladado}:</strong>
                        </td>
                        <td class="right border-bottom" width="50%" valign="top">
                            {$xmlData.impuestosLocales.ish.Importe|number}
                        </td>
                    </tr>
                {/if}
                {if count($xmlData.impuestos.retenciones) > 0}
                    <tr>
                        <td class="right" width="50%" valign="top">
                            <strong>Impuestos retenidos</strong>
                        </td>
                        <td class="right" width="50%" valign="top">
                            &nbsp;
                        </td>
                    </tr>
                    {foreach from=$xmlData.impuestos.retenciones item=retencion}
                        <tr>
                            <td class="right" width="50%" valign="top">
                                <strong>{$catalogos.impuestos[{$retencion.Impuesto}]}:</strong>
                            </td>
                            <td class="right border-bottom" width="50%" valign="top">
                                {$retencion.Importe|number}
                            </td>
                        </tr>
                    {/foreach}
                {/if}
                <tr>
                    <td class="right" width="50%" valign="top">
                        <strong>TOTAL:</strong>
                    </td>
                    <td class="right border-bottom" width="50%" valign="top">
                        {$xmlData.cfdi.Total|number}
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>