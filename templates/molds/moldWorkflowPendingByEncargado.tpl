<html>
<p><b>CLIENTES CON PENDIENTES</b></p>
<p></p>
{foreach from=$data item=one key=keyone}
    <p><b>{$one.razon}</b></p>
    <div><p><b>Servicios</b></p>
    {$one.servicios}</div><hr />
{/foreach}
</html>