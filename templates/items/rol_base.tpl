<tr class="{$clase}">
    <td style="width:10%;">{$key+1}</td>
    <td>{$rol.name}</td>
    <td>
        {if in_array(131,$permissions) || $User.isRoot}
        <img src="{$WEB_ROOT}/images/settings.png" class="spanConfig" title="Configurar" id="{$rol.rolId}"/>
        {/if}
    </td>
</tr>
