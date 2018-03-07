<tr>
    <td align="center">{$item.fecha}</td>
    <td align="center">{$item.tipo}</td>
    <td align="center">{$item.descripcion}</td>
    <td align="center">{$item.usuario}</td>
    <td align="center">{$item.nameContact}</td>
    <td align="center">{$item.name}</td>
    <td align="center">{$item.servicio}</td>
    <td align="left">
        {foreach from=$item.oldValue  item=itemo key=ko}
            {$ko} => {$itemo}<hr/> <br>
        {/foreach}
    </td>
    <td align="left">
        {foreach from=$item.newValue  item=itemn key=kn}
            {$kn} => {$itemn}<hr/><br>
        {/foreach}
    </td>
</tr>
             