    {if $xmlData.impuestosLocales|count > 0}
    <p>Deducciones</p>
    <table width="100%" class="outline-table pre font-smaller no-bold">
        {assign var="totalFromImpuestos" value=(string)$xmlData.cfdi.SubTotal+(string)$traslado.Importe}
        {assign var="totalDeducciones" value=0}
        {foreach from=$xmlData.impuestosLocales key=keyTipo item=impuesto}
            {assign var="totalDeducciones" value=(string)$totalDeducciones+(string)$impuesto.impuesto.importe}
            <tr class="border-right border-bottom">
                <td class="left">{$impuesto.impuesto.impuesto}</td>
                <td class="right"></td>
                <td class="right">{$impuesto.impuesto.importe|number}</td>
                <td class="right"></td>
            </tr>
        {/foreach}
        <tr class="border-right border-bottom">
            <td class="left">Total deducciones</td>
            <td class="right"></td>
            <td class="right"></td>
            <td class="right">{$totalDeducciones|number}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td colspan="4" class="border-top left">&nbsp;</td>
        </tr>
        <tr class="border-right border-bottom">
            <td class="left"></td>
            <td class="right"></td>
            <td class="right">Alcance liquido</td>
            <td class="right">
                {assign var="alcanceLiquido" value=(string)$subtotalAlcanceLiquido - (string)$totalDeducciones}
                {$xmlData.cfdi.Total|number}
            </td>
        </tr>
    </table>
{/if}