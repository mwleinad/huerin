<html>
<head>
    <title>Invoice</title>
    <style type="text/css">
        body {
            font-family: helvetica, Sans-Serif;
            font-size: 11px;
            line-height: 1;
        }
        #page-wrap {
            width: 700px;
            margin: 0 auto;
            page-break-inside: avoid;
        }
        table {
            font-size: 11px;
            line-height: 20px;
        }
        table.outline-table {
            border: 2px solid #ccc;
            border-spacing: 0;
        }
        tr.border-bottom td, td.border-bottom {
            border-bottom: 1px solid #ccc;
        }
        tr.border-top td, td.border-top {
            border-top: 1px solid #ccc;
        }
        tr.border-right td, td.border-right {
            border-right: 1px solid #ccc;
        }
        tr.border-left td, td.border-left {
            border-left: 1px solid #ccc;
        }
        tr.border-right td:last-child {
            border-right: 0px;
        }
        tr.center td, td.center {
            text-align: center;
            vertical-align: text-top;
        }
        td.pad-left {
            padding-left: 5px;
        }
        tr.right-center td, td.right-center {
            text-align: right;
            padding-right: 50px;
        }
        tr.right td, td.right {
            text-align: right;
        }
        .font-smallest{
            font-size: 8px;
            line-height: 10px;
            font-weight: bold;;
        }
        .font-smaller{
            font-size: 9px;
            line-height: 1.5;
            font-weight: bold;;
        }
        .bold {
            font-weight: bold;
        }
        .no-bold{
            font-weight: normal !important;
        }
        .no-border-left {
            border-left:none !important;
        }
        .no-border-right {
            border-right:none !important;
        }
        .no-border {
            border: none !important;
        }
        .small-height{
            max-height: 10px;
            height: 10px;
            min-height: 10px;
            margin: 0px;
        }
        .word-break{
            word-break: break-all;
        }
        .text-center {
            text-align: center;
        }
        .padding-vertical {
            padding: 10px 0px
        }
        .pre {
            font-family:"Courier New";
        }
        pre {
            line-height: 10px;
            white-space: pre-wrap;
        }
        .no-margin{
            margin: 0;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="page-wrap">
    <table width="100%">
        <tbody>
        <tr>
            {if $logo}
            <td width="20%" valign="top" style="vertical-align: middle">
                <img src="{$logo}" width="130px">
            </td>
            {/if}
            <td width="40%" valign="top">
                <strong style="margin-top: 0px">Datos del emisor</strong>
                <p style="line-height: 1.2; margin-top: 0px; margin-bottom: .5px">
                    <span style="display:block"><strong>Nombre:</strong>{$xmlData.emisor.Nombre}</span>
                    <span style="display:block"><strong>RFC:</strong>{$xmlData.emisor.Rfc}</span>
                    <span style="display:block"><strong>Régimen fiscal:</strong>{$xmlData.emisor.RegimenFiscal} {$catalogos.RegimenFiscal}</span>
                </p>
                <strong>Datos del receptor</strong>
                <p style="line-height: 1.2; margin-top: 0px">
                    <span style="display: block"><strong>Nombre:</strong>{$xmlData.receptor.Nombre}</span>
                    <span style="display: block"><strong>RFC:</strong>{$xmlData.receptor.Rfc}</span>
                   {if $xmlData.receptor.RegimenFiscalReceptor}<span style="display: block"><strong>Régimen fiscal:</strong> {$xmlData.receptor.RegimenFiscalReceptor} {$catalogos.RegimenFiscalReceptor}</span>{/if}
                   {if $xmlData.receptor.DomicilioFiscalReceptor}<span style="display: block"><strong>Domicilio fiscal:</strong> {$xmlData.receptor.DomicilioFiscalReceptor}</span>{/if}
                </p>
            </td>
            <td width="40%" valign="top">
                <span style="line-height: 1.2; margin-top: 0px"><strong>Folio fiscal:</strong> {$xmlData.timbreFiscal.UUID}</span>
                <p style="line-height: 1.2; margin-top: 0px">
                    <span style="display: block"> <strong>No. de serie del CSD:</strong> {$xmlData.cfdi.NoCertificado}</span>
                    <span style="display: block"> <strong>Lugar, fecha y hora de emisión:</strong> {$xmlData.cfdi.LugarExpedicion} {$xmlData.cfdi.Fecha|replace:'T':' '}</span>
                    <span style="display: block"> <strong>Efecto de comprobante:</strong> {$xmlData.cfdi.TipoDeComprobante} {$catalogos.EfectoComprobante}</span>
                    <span style="display: block"> <strong>Folio y serie:</strong> {$xmlData.cfdi.Serie} {$xmlData.cfdi.Folio}</span>
                    <span style="display: block"> <strong>Uso CFDI:</strong> {$xmlData.receptor.UsoCFDI} {$catalogos.UsoCFDI}</span>
                    <span style="display: block">
                        {if $xmlData.cfdiRelacionados}
                            <strong>CFDI Relacionado:</strong> {$xmlData.cfdiRelacionados.uuid}
                        {/if}
                    </span>
                    {if $xmlData.escuela.noControl}<span style="display: block"><strong># Control:</strong>{$xmlData.escuela.noControl}</span>{/if}
                    {if $xmlData.escuela.carrera} <span style="display: block"><strong>Carrera:</strong> {$xmlData.escuela.carrera}</span> {/if}
                </p>
            </td>
        </tr>
        </tbody>
    </table>
    {*{$xmlData.db.observaciones|urldecode|replace:"[%]MAS[%]":"+"}*}
    <p class="bold no-margin">Conceptos</p>
    {foreach from=$xmlData.conceptos item=concepto}
    <table width="100%" class="outline-table">
        <tbody>
        <tr class="border-bottom border-right center font-smallest">
            <td class="border-top" width="5%"><strong>Cve prod/serv</strong></td>
            <td class="border-top" width="5%"><strong>Cve unidad</strong></td>
            <td class="border-top" width="10%"><strong>No. identification</strong></td>
            <td class="border-top" width="10%"><strong>Cantidad</strong></td>
            <td class="border-top" width="10%"><strong>Unidad</strong></td>
            <td class="border-top" width="10%"><strong>Valor unitario</strong></td>
            <td class="border-top" width="10%"><strong>Importe</strong></td>
            <td class="border-top" width="10%"><strong>Descuento</strong></td>
        </tr>
        <tr class="border-right border-bottom">
            <td class="left">{$concepto.concepto.ClaveProdServ}</td>
            <td class="left">{$concepto.concepto.ClaveUnidad}</td>
            <td class="left">{$concepto.NoIdentificacion}</td>
            <td class="left">{$concepto.concepto.Cantidad}</td>
            <td class="left">{$concepto.concepto.Unidad}</td>
            <td class="right">{$concepto.concepto.ValorUnitario|number}</td>
            <td class="right">{$concepto.concepto.Importe|number}</td>
            <td class="right">{$concepto.concepto.Descuento|number}</td>
        </tr>
        <tr class="border-right border-bottom">
            <td colspan="8" class="pad-left pre" style="font-family: monospace">
                {$concepto.concepto.Descripcion|wordwrap|nl2br|replace:" ":"&nbsp;"|replace:"[%]MAS[%]":"+"}

                {if $xmlData.amortizacionData.amortizacionFiniquitoSubtotal > 0 || $xmlData.amortizacionData.amortizacion > 0}
                    <table width="100%" class="">
                        {if $xmlData.amortizacionData.amortizacionFiniquitoSubtotal > 0}
                            <tr class="no-border">
                                <td class="no-border left" width="55%">{$xmlData.amortizacionData.amortizacionFiniquito|urldecode}</td>
                                <td class="no-border right" width="15%">SUBTOTAL</td>
                                <td class="no-border right" width="15%">{$xmlData.amortizacionData.amortizacionFiniquitoSubtotal|number}</td>
                                <td class="no-border right" width="15%">&nbsp;</td>
                            </tr>
                            <tr class="no-border">
                                <td class="no-border left" width="55%"></td>
                                <td class="no-border right" width="15%">IVA</td>
                                <td class="no-border right" width="15%">{$xmlData.amortizacionData.amortizacionFiniquitoIva|number}</td>
                                <td class="no-border right" width="15%"><u>{$xmlData.amortizacionData.amortizacionFiniquitoIva+$xmlData.amortizacionData.amortizacionFiniquitoSubtotal|number}</u></td>
                            </tr>
                        {/if}
                        {if $xmlData.amortizacionData.amortizacion > 0}
                            <tr class="no-border">
                                <td class="no-border left">AMORTIZACION DEL ANTICIPO</td>
                                <td class="no-border right">SUBTOTAL</td>
                                <td class="no-border right">{$xmlData.amortizacionData.amortizacion|number}</td>
                                <td class="no-border right">&nbsp;</td>
                            </tr>
                            <tr class="no-border">
                                <td class="no-border left"></td>
                                <td class="no-border right">IVA</td>
                                <td class="no-border right">{$xmlData.amortizacionData.amortizacionIva|number}</td>
                                <td class="no-border right"><u>{$xmlData.amortizacionData.amortizacionIva+$xmlData.amortizacionData.amortizacion|number}</u></td>
                            </tr>
                        {/if}

                        <tr class="no-border">
                            <td class="no-border left"></td>
                            <td class="no-border right"></td>
                            <td class="no-border right">ALCANCE LIQUIDO</td>
                            <td class="no-border right"><u>{$xmlData.cfdi.Total|number}</u></td>
                        </tr>
                    </table>
                {/if}
            </td>
        </tr>
        <tr class="border-right">
            <td colspan="3" width="100%" class="pad-left no-border-right">
                &nbsp;
            </td>
            <td colspan="5" width="100%" class="pad-left no-border-left padding-vertical">
                {if count($concepto.traslados) > 0}
                <table width="100%" class="outline-table no-border">
                    <tbody>
                    <tr class="border-bottom">
                        <td colspan="5" class="font-smaller">Traslados</strong></td>
                    </tr>
                    <tr class="border-bottom border-right center font-smallest">
                        <td class="border-left" width="15%">Base</strong></td>
                        <td width="10%"><strong>Impuesto</strong></td>
                        <td width="10%"><strong>Tipo factor</strong></td>
                        <td width="20%"><strong>Tasa o cuota</strong></td>
                        <td width="20%"><strong>Importe</strong></td>
                    </tr>
                    {foreach from=$concepto.traslados item=traslado}
                        <tr class="border-right font-smallest">
                            <td class="center border-bottom border-left">{$traslado.Base|number}</td>
                            <td class="center border-bottom">{$catalogos.impuestos[{$traslado.Impuesto}]}</td>
                            <td class="center border-bottom">{$traslado.TipoFactor}</td>
                            <td class="center border-bottom">{$traslado.TasaOCuota}</td>
                            <td class="center border-bottom">{$traslado.Importe|number}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                {/if}

                {if count($concepto.retenciones) > 0}
                <table width="100%" class="outline-table no-border">
                    <tbody>
                    <tr class="border-bottom">
                        <td colspan="5" class="font-smaller"><strong>Retenciones</strong></td>
                    </tr>
                    <tr class="border-bottom border-right center font-smallest">
                        <td class="border-left" width="15%"><strong>Base</strong></td>
                        <td width="10%"><strong>Impuesto</strong></td>
                        <td width="10%"><strong>Tipo factor</strong></td>
                        <td width="20%"><strong>Tasa o cuota</strong></td>
                        <td width="20%"><strong>Importe</strong></td>
                    </tr>
                    {foreach from=$concepto.retenciones item=retencion}
                    <tr class="border-right font-smallest">
                        <td class="center border-bottom border-left">{$retencion.Base|number}</td>
                        <td class="center border-bottom">{$catalogos.impuestos[{$retencion.Impuesto}]}</td>
                        <td class="center border-bottom">{$retencion.TipoFactor}</td>
                        <td class="center border-bottom">{$retencion.TasaOCuota}</td>
                        <td class="center border-bottom">{$retencion.Importe|number}</td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
                {/if}

                {if $concepto.cuentaPredial.Numero}
                    Cuenta predial: {$concepto.cuentaPredial.Numero}
                {/if}
            </td>
        </tr>
        </tbody>
    </table>
    {/foreach}
    {if $xmlData.db.status == 0 && $xmlData.db.comprobanteId}
        <span style="font-size: 96px; color: #f00; text-align: center;position: absolute;top:20%">CANCELADO</span>
    {elseif $xmlData.db.status == 1 && $xmlData.db.cfdi_cancel_status}
        <span style="font-size: 70px; color: #f59b25; text-align: center;line-height:1;position: absolute;top:20%">CANCELADO</span>
    {/if}

    {*Complemento de impuestos*}
    {include file="{$DOC_ROOT}/templates/pdf/complementoImpuestos.tpl"}

    <p class=""><span class="no-bold word-break pre">{$xmlData.db.observaciones|urldecode|replace:"[%]MAS[%]":"+"}</span> </p>

    {*Totales*}
    {include file="{$DOC_ROOT}/templates/pdf/totales.tpl"}


    {if $empresaId == 15 && $xmlData.impuestosLocales|count == 0}
        <table width="100%" class="outline-table">
            <tbody>
            <tr class="border-bottom border-right center font-smallest">
                <td class="border-top" width="50%"><strong>Nombre y firma del cajero</strong></td>
                <td class="border-top" width="50%"><strong>Sello</strong></td>
            </tr>
            <tr class="border-right border-bottom">
                <td class="left">&nbsp;<br>&nbsp;</td>
                <td class="left">&nbsp;<br>&nbsp;</td>
            </tr>
            </tbody>
        </table>
    {/if}

    {if $xmlData.firmasLocales}
        <table width="100%" class="outline-table">
            <tbody>
                <tr class="border-bottom border-right center font-smallest">
                    {foreach from=$xmlData.firmasLocales item=firma}
                        <td class="border-top" width="20%"><strong>{$firma.nombre}<br><br><br>{$firma.valor|nl2br}</strong></td>
                    {/foreach}
                </tr>
            </tbody>
        </table>
    {/if}
    {*Complemento de pagos*}
    {include file="{$DOC_ROOT}/templates/pdf/complementoPago.tpl"}

    {*Complemento de nomina*}
    {include file="{$DOC_ROOT}/templates/pdf/complementoNomina.tpl"}

    {*Complemento de nomina*}
    {include file="{$DOC_ROOT}/templates/pdf/complementoDonatarias.tpl"}

    {*Cadenas y timbres*}
    {include file="{$DOC_ROOT}/templates/pdf/cadenasTimbres.tpl"}

    <p class="text-center small-height">Este documento es una representación impresa de un CFDI</p>
</div>
</body>
</html>
