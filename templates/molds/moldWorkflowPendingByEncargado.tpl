
<p><b>Clientes con pendientes</b></p>
<p></p>
{foreach from=$data item=one key=keyone}
<p>{$one.razon}</p>
    <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" border="1">
        <thead>
        </thead>
        <tbody>
        {foreach from=$one.servicios item=item key=key}
            <tr>
                <td colspan="2"><b>{$item.nombreServicio}</b></td>
            </tr>
            <tr>
                <td><b>Mes</b></td>
                <td><b>Año</b></td>
            </tr
            {foreach from=$item.meses item=item2}
                {assign var=dateArray value="-"|explode:$item2}
                <tr>
                    <td>{$meses[$dateArray[1]]}</td>
                    <td>{$dateArray[0]}</td>
                </tr>
            {/foreach}
        {/foreach}
        </tbody>
    </table>

{/foreach}
