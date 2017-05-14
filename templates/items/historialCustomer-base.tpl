{foreach from=$historial item=item key=key}
    <tr id="1">
        <td align="center" class="id">{$item.fecha}</td>
        <td align="center">{$item.personalName}</td>
        <td align="center" class="id">{if $item.status == 1} Si {else} No {/if}</td>
        <td align="center">{$item.nameContact}</td>
    </tr>
{/foreach}
