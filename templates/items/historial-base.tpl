{foreach from=$historial item=item key=key}
    <tr id="1">
        <td align="center" class="id">{$item.fecha}</td>
        <td align="center">{$item.personalName}</td>
        <td align="center">{$item.nombreServicio}</td>
        <td align="center">{$item.contractName}</td>
        <td align="center">{$item.nameContact}</td>
        <td align="center">{$item.costo}</td>
        <td align="center">{$item.status}</td>
    </tr>
{/foreach}
