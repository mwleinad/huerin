<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a" class="sortable resizable">
    <thead>
    <tr>
        <th width="">#</th>
        <th width="">Platillo</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$platillos item=item key=key}
        <tr id="1">
            <td align="center">{$key}</td>
            <td align="center">{$item}</td>
            <td align="center">
                <img src="{$WEB_ROOT}/images/icons/action_delete.gif" class="spanDelete" id="{$key}" title="Eliminar"/>
            </td>
        </tr>
    {foreachelse}
        <tr><td colspan="3"  style="text-align: center"> No se encontr&oacute; ning&uacute;n platillo.</td></tr>
    {/foreach}
    </tbody>
</table>