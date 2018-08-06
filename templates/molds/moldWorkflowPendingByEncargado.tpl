<html>
<p><b>CLIENTES CON PENDIENTES</b></p>
<p></p>
{foreach from=$data item=one key=keyone}
    <p><b>{$one.razon}</b></p>
    <div><p><b>Servicios</b></p>
    {$one.servicios}</div><hr />
   {* <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" border="1">
        <thead>
        </thead>
        <tbody>
        {foreach from=$one.servicios item=item key=key}
            <tr>
                <td colspan="2"><b>{$item.nombreServicio}</b></td>
            </tr>
            <tr>
                <td><b>Año</b></td>
                <td><b>Meses</b></td>
            </tr
            {*foreach from=$item.dtm item=item2}
                {assign var=dateArray value=":"|explode:$item2}
                <tr>
                    <td>{$dateArray[0]}</td>
                    <td>{$dateArray[1]}</td>
                </tr>
            {/foreach}
        {/foreach}
        </tbody>
    </table> *}
{/foreach}
</html>