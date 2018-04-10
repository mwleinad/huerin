<tr class="{$clase}">
    <td style="width:10%;">{$key+1}</td>
    <td  style="width:35%;">{$rol.name}</td>
    <td  style="width:35%;">{$rol.departamento}</td>
    <td  style="width:20%;">
        {if in_array(131,$permissions) || $User.isRoot}
        <img src="{$WEB_ROOT}/images/settings.png" style="width: 16px;height: 16px;" class="spanConfig" title="Configurar" id="{$rol.rolId}"/>
        {/if}
        {if in_array(131,$permissions) || $User.isRoot}
            <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEdit" title="Editar" id="{$rol.rolId}"/>
        {/if}
        {if in_array(131,$permissions) || $User.isRoot}
            <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" title="Eliminar rol" id="{$rol.rolId}"/>
        {/if}
    </td>
</tr>
