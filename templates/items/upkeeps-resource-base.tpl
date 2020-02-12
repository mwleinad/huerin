<tr class="{$clase}">
    <td style="width:50%;">{$res.upkeep_description}</td>
    <td style="width:20%;">{$res.upkeep_responsable}</td>
    <td style="width:20%;">{$res.upkeep_date|date_format:'%d-%m-%Y'}</td>
    <td>
        <div style="min-width: 40px">
            {if in_array(265,$permissions)|| $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanAll spanEdit" title="Editar"
                     id="{$res.upkeep_resource_office_id}" data-resource="{$res.office_resource_id}"/>
            {/if}
            {if in_array(266,$permissions)|| $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanAll spanDelete" title="Eliminar"
                     id="{$res.upkeep_resource_office_id}" data-resource="{$res.office_resource_id}"/>
            {/if}
        </div>
    </td>
</tr>

