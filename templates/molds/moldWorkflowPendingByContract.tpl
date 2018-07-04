
<p><b>{$data.razon}</b></p>
<p>Estimado cliente:</p>
<p>Le informamos que su contabilidad y declaraciones fiscales de acuedo a nuestros controles y revisíon, tiene pendientes.</p>
<p>Los meses detectados son:</p>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" border="1">
    <thead>
    </thead>
    <tbody>
    {foreach from=$data.servicios item=item key=key}
        <tr>
            <td colspan="2"><b>{$item.nombre}</b></td>
        </tr>
        <tr>
            <td><b>Mes</b></td>
            <td><b>Año</b></td>
        </tr
        {foreach from=$item.instancias item=item2}
            {assign var=dateArray value="-"|explode:$item2}
            <tr>
                <td>{$meses[$dateArray[1]]}</td>
                <td>{$dateArray[0]}</td>
            </tr>
        {/foreach}
    {/foreach}
    </tbody>
</table>
