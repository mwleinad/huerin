{if $tipo eq 'new'}
<div class="fieldBold">
    <p>Se han realizado altas de servicios para la siguiente razon social :  {$contrato.name} del cliente {$contrato.nameContact}</p>
    <p>por el colaborador {$currentUser.name}</p>
</div>
{else}
<div class="fieldBold">
    <p>La siguiente razon social :  {$contrato.name} del cliente {$contrato.nameContact}</p>
    <p>ha sido modificada por el colaborador {$currentUser.name}</p>
</div>
{/if}