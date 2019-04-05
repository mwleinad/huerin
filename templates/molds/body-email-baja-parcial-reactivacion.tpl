{if $endState=='bajaParcial'}
    <p style="line-height: 1.5;">Los servicios de la razon social {$razon.razon} del cliente {$razon.cliente} han sido dados de baja temporalmente por el colaborador {$who} </p>
    <p style="line-height: 1.5;">En el archivo adjunto se detallan los servicios afectados.</p>
{else}
    <p style="line-height: 1.5;">Los servicios de la razon social {$razon.razon} del cliente {$razon.cliente} han sido reactivados por el colaborador {$who} </p>
    <p style="line-height: 1.5;">En el archivo adjunto se detallan los servicios afectados.</p>
{/if}