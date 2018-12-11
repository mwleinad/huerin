<pre>
<div style="width: 650px">
    {if $endState=='bajaParcial'}
    <p style="line-height: 1.5;">Los servicios de la razon social {$razon.razon} del cliente {$razon.cliente} han sido dados de baja temporalmente por el colaborador {$who} </p>
    <p style="line-height: 1.5;">En la siguiente lista se muestran los servicios afectados con informacion detallada:</p>
    {else}
        <p style="line-height: 1.5;">Los servicios de la razon social {$razon.razon} del cliente {$razon.cliente} han sido reactivados por el colaborador {$who} </p>
        <p style="line-height: 1.5;">En la siguiente lista se muestran los servicios afectados con informacion detallada: </p>
    {/if}
    <table width="100%">
        <tr>
            <th style="width:40%;text-align: left;border:1px dotted #000000">Nombre servicio</th>
            {if $endState=='bajaParcial'}
                <th style="width: 10%;text-align: left;border:1px dotted #000000">Fecha de ultimo workflow</th>
            {/if}
            <th style="width:10%;text-align: left;border:1px dotted #000000">Inicio de operaciones</th>
            <th style="width:10%;text-align: left;border:1px dotted #000000">Inicio de facturacion</th>
            <th style="width:10%;text-align: left;border:1px dotted #000000">Costo</th>

        </tr>
    {foreach from=$serviciosAfectados item=item key=key}
        <tr>
            <td style="border:1px dotted #000000">{$item.nombreServicio}</td>
            {if $endState=='bajaParcial'}
                <td style="border:1px dotted #000000">{$item.ultimoWorkflow}</td>
            {/if}
            <td style="border:1px dotted #000000">{$item.inicioOperaciones}</td>
            <td style="border:1px dotted #000000">{$item.inicioFactura}</td>
            <td style="border:1px dotted #000000">{$item.costo}</td>
        </tr>
    {/foreach}
    </table>
    {if $endState=='bajaParcial'}
    <p style="line-height: 1.5;">La columna "fecha de ultimo workflow", es la fecha del ultimo workflow que sera creado en plataforma, de esa fecha en adelante se suspende la creacion de workflows para el servicio.</p>
    {/if}
</div>
