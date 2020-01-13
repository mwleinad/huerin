{foreach from=$contracts item=item key=key}
    <tr id="1">
        {if in_array(200,$permissions) || $User.isRoot}
            <td align="center" class="id">{$item.contractId}</td>
        {/if}
        {if in_array(201,$permissions) || $User.isRoot}
            <td align="center" class="id">{$item.name}</td>
        {/if}
        {if in_array(202,$permissions) || $User.isRoot}
            <td align="center" class="id">{$item.type}</td>
        {/if}
        {if in_array(203,$permissions) || $User.isRoot}
            <td align="center">{$item.rfc}</td>
        {/if}
        {if in_array(204,$permissions) || $User.isRoot}
            <td align="left">
            {if is_array($item.encargadosXdep)}
                {foreach from=$departamentos item=depto}
                    {assign var="idDepto" value="{$depto.departamentoId}"}
                    {if array_key_exists($idDepto,$item.encargadosXdep)}
                        <b>{$depto.departamento}:</b> {$item.encargadosXdep.$idDepto}<br>
                    {/if}
                {/foreach}
            {/if}
            </td>
        {/if}
        {if in_array(205,$permissions) || $User.isRoot}
        <td align="left">
            {$item.activo}{if $item.haveTemporal}- Con Bajas Temporales{/if}
        </td>
        {/if}
        {if in_array(206,$permissions) || $User.isRoot}
            <td align="center">
            {if $item.activo == 'Si'}
                {$item.noServicios}
                {if $item.noServicios > 0}
                    {if in_array(86,$permissions)|| $User.isRoot}
                        <a href="{$WEB_ROOT}/services/id/{$item.contractId}" onclick="return parent.GB_show('Servicios de Razon Social', this.href,500,970) ">
                            <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Servicios"/>
                        </a>
                    {/if}
                {/if}
                {if in_array(85,$permissions)|| $User.isRoot}
                    <img class="spanAddService" id="{$item.contractId}" src="{$WEB_ROOT}/images/icons/add.png" title="Agregar Servicios"/>
                {/if}
            {else}
                N/A
            {/if}
            </td>
        {/if}
        {if in_array(207,$permissions) || $User.isRoot}
        <td align="center">
            {if $item.activo == 'Si'}
                {if in_array(65,$permissions) || $User.isRoot}
                    <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$item.contractId}" title="Desactivar"/>
                {/if}
            {else}
                {if in_array(177,$permissions) || $User.isRoot}
                    <img src="{$WEB_ROOT}/images/icons/activate.png" class="spanDelete" id="{$item.contractId}" title="Activar"/>
                {/if}
            {/if}
            {if (in_array(64,$permissions)|| $User.isRoot) && $item.activo == 'Si'}
                <a href="{$WEB_ROOT}/contract-edit/contId/{$item.contractId}">
                <img src="{$WEB_ROOT}/images/icons/edit.gif" class="spanEdit" id="{$item.contractId}" title="Editar" border="0"/></a>
            {/if}
            {if (in_array(219,$permissions)|| $User.isRoot) && $item.activo == 'Si'}
                <a href="javascript:;" title="Actualizar archivos en workflows">
                    <img src="{$WEB_ROOT}/images/icons/folder-file-16x16.png" class="spanAll spanUpdateWorkflow" data-id="{$item.contractId}"  border="0"/></a>
            {/if}
            {if $User.isRoot && $item.activo == 'Si'}
                <a href="javascript:;" title="Actualizar permisos">
                    <img src="{$WEB_ROOT}/images/icons/backup_16x16.png" class="spanAll spanUpdatePermisos" data-id="{$item.contractId}"  border="0"/></a>
            {/if}
            {if in_array(66,$permissions) || $User.isRoot}
               <a href="{$WEB_ROOT}/contract-view/contId/{$item.contractId}">
                <img src="{$WEB_ROOT}/images/icons/view.png" title="Ver Detalles" />
               </a>
            {/if}
            {if in_array(216,$permissions) || $User.isRoot}
                <img src="{$WEB_ROOT}/images/icons/transbetweenuser.png" class="spanAll spanTransfer" id="{$item.contractId}" title="Transferir raazon a cliente" />
            {/if}

        </td>
       {/if}
    </tr>
{foreachelse}
<tr><td colspan="6" align="center">No se encontr&oacute; ning&uacute;n registro.</td></tr>
{/foreach}
