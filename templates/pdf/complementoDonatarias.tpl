{if $xmlData.donatarias}
    <p class="bold">Complemento donatarias</p>
    <table width="100%" class="outline-table">
        <tbody>
        <tr class="border-bottom border-right center font-smallest">
            <td class="border-top" width="15%"><strong># Autorizaocion</strong></td>
            <td class="border-top" width="15%"><strong>Fecha autorizacion</strong></td>
            <td class="border-top" width="70%"><strong>Leyenda</strong></td>
        </tr>
        <tr class="border-right border-bottom">
            <td class="left">{$xmlData.donatarias.noAutorizacion}</td>
            <td class="left">{$xmlData.donatarias.fechaAutorizacion}</td>
            <td class="left">{$xmlData.donatarias.leyenda}</td>
        </tr>
        </tbody>
    </table>
    <p class="small-height">&nbsp;</p>
{/if}