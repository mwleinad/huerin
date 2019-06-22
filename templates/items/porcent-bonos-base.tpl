<tr class="{$clase}">
    <td  style="width:35%;">{$porcent.name}</td>
    <td  style="width:35%;">{$porcent.categoria}</td>
    <td  style="width:35%;">{$porcent.porcentaje}</td>
    <td  style="width:20%;">
        {if in_array(228,$permissions) || $User.isRoot}
            <img src="{$WEB_ROOT}/images/b_edit.png" class="spanEditPorcent" title="Editar" id="{$porcent.porcentId}"/>
        {/if}
        {if in_array(229,$permissions) || $User.isRoot}
            <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDeletePorcent" title="Eliminar rol" id="{$porcent.porcentId}"/>
        {/if}
    </td>
</tr>
